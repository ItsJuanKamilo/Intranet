<?php

namespace App\Http\Controllers;

use App\Models\Web\EventAttendee;
use App\Models\Web\Event;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Yajra\DataTables\Facades\DataTables;
use App\Models\Web\EventSurveyAnswer;
use Illuminate\Support\Str;

class EventController extends Controller
{
    /**
     * Muestra la lista de eventos.
     */
    public function index(Request $request)
    {

        return view('marketing.events.index');
    }


    /**
     * Muestra el formulario para crear un nuevo evento.
     */
    public function create()
    {
        $lastEvent = Event::latest()->first();
        return view('marketing.events.create', compact('lastEvent'));
    }

    /**
     * Almacena un evento nuevo en la base de datos.
     */
    public function store(Request $request)
    {

        // Mensajes de validación personalizados
        $messages = [
            'date_end.after_or_equal' => 'La fecha de finalización debe ser una fecha igual o posterior a la fecha de inicio.',
        ];

        // Validación de los campos del evento con mensajes personalizados
        $validated = $request->validate([
            'active' => 'required|boolean',
            'visible' => 'required|boolean',
            'title' => 'required|string|max:255',
            'type' => 'required|string|max:255',
            'date_start' => 'required|date',
            'date_end' => 'required|date|after_or_equal:date_start',
            'date_time_start' => 'required',
            'date_time_end' => 'required',
            'brand' => 'required|string|max:255',
            'category' => 'required|string|max:255',
            'sku' => 'nullable|string|max:255',
            'is_paid' => 'required|boolean',
            'exponent' => 'nullable|string|max:255',
            'exponent_job' => 'nullable|string|max:255',
            'quota' => 'required|integer',
            'location' => 'required|string|max:255',
            'location_gmap' => 'nullable|string|max:255',
            'net_price' => 'nullable|string',
            'external_url' => 'nullable|string|max:255'
        ], $messages);

        $validated['title'] = ucwords(strtolower($validated['title']));
        $validated['brand'] = ucwords(strtolower($validated['brand']));
        $validated['exponent'] = !empty($validated['exponent']) ? ucwords(strtolower($validated['exponent'])) : null;
        $validated['exponent_job'] = !empty($validated['exponent_job']) ? ucwords(strtolower($validated['exponent_job'])) : null;
        $validated['location_gmap'] = !empty($validated['location_gmap']) ? ucwords(strtolower($validated['location_gmap'])) : null;


        try {
            if (is_null($validated['net_price'])) {
                $validated['net_price'] = '0';
            }

            $event = Event::create($validated);
            $event->createURL();
            $event->createToken();
            $event->createQrImageUrl();
            $event->updateSEOfields();

            return redirect()->route('marketing.events.index')
                ->with('success', 'Evento creado exitosamente.');
        } catch (\Exception $e) {
            \Log::error('Error al crear evento: ' . $e->getMessage());
            return redirect()->back()
                ->withErrors('No se pudo crear el evento.')
                ->withInput();
        }

    }


    /**
     * Muestra la información de un evento.
     */
    public function show(Event $event)
    {
        return view('marketing.events.show', compact('event'));
    }

    /**
     * Muestra el formulario para editar un evento.
     */
    public function edit(Event $event)
    {
        $attendees = $event->attendances()->get();
        $responses = \App\Models\Web\EventSurveyAnswer::with('question', 'attendee')
            ->where('event_id', $event->id)
            ->whereNotNull('answer')
            ->where('answer', '<>', '')
            ->get();


        $attendeeRuts = $attendees->pluck('tax_vat')
            ->map(function ($taxVat) {
                // Quitar el guion y el dígito verificador de tax_vat
                return substr($taxVat, 0, strlen($taxVat) - 2);
            })
            ->toArray();

        // Obtener los vendedores de la base de datos de ventas
        $vendedores = \DB::connection('pgsql')
            ->table('partners')
            ->whereIn(\DB::raw('CAST(id AS TEXT)'), $attendeeRuts)  // Use 'id' instead of 'vat'
            ->join('users', \DB::raw('CAST(partners.customer_salesperson_tax AS TEXT)'), '=', \DB::raw('CAST(users.rut AS TEXT)'))
            ->select('partners.id', 'users.name', 'users.surname_1', 'partners.customer_salesperson_tax')  // Removed 'vat_dv'
            ->get();
        foreach ($attendees as $attendee) {
            $fullVat = substr($attendee->tax_vat, 0, strlen($attendee->tax_vat) - 2);
            $vendedor = $vendedores->firstWhere('id', $fullVat);
            $attendee->vendor_name = $vendedor ? $vendedor->name . ' ' . $vendedor->surname_1 : 'No asignado';
        }

        // Contadores
        $emailConfirmedCount = $attendees->where('email_reminder_confirm_at', true)->count();
        $surveyCount = $attendees->where('email_survey', true)->count();
        $reminderCount = $attendees->where('email_reminder', 1)->count();

        $confirmedCount = $attendees->where('confirmed', true)->count();
        $totalAttendees = $attendees->count();

        return view('marketing.events.edit', compact(
            'event',
            'responses',
            'attendees',
            'emailConfirmedCount',
            'surveyCount',
            'reminderCount',
            'confirmedCount',
            'totalAttendees'
        ));
    }


    /**
     * Actualiza un evento existente en la base de datos.
     */
    public function update(Request $request, Event $event)
    {
        // Mensajes de validación personalizados
        $messages = [
            'date_end.after_or_equal' => 'La fecha de finalización debe ser una fecha igual o posterior a la fecha de inicio.',
        ];
        $validated = $request->validate([
            'visible' => 'required|boolean',
            'active' => 'required|boolean',
            'title' => 'required|string|max:255',
            'type' => 'required|string|max:255',
            'date_start' => 'required|date',
            'date_end' => 'required|date|after_or_equal:date_start',
            'date_time_start' => 'required',
            'date_time_end' => 'required',
            'brand' => 'required|string|max:255',
            'category' => 'required|string|max:255',
            'sku' => 'nullable|string|max:255',
            'is_paid' => 'required|boolean',
            'exponent' => 'nullable|string|max:255',
            'exponent_job' => 'nullable|string|max:255',
            'quota' => 'required|integer',
            'location' => 'required|string|max:255',
            'location_gmap' => 'nullable|string|max:255',
            'net_price' => 'nullable|string',
            'description' => 'nullable',
            // Definimos la imagen como nullable para que no sea obligatoria
            'image_promo' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'external_url' => 'nullable|string|max:255'
        ], $messages);

        // Normalización de algunos campos
        $validated['title'] = ucwords(strtolower($validated['title']));
        $validated['brand'] = ucwords(strtolower($validated['brand']));
        $validated['exponent'] = !empty($validated['exponent']) ? ucwords(strtolower($validated['exponent'])) : null;
        $validated['exponent_job'] = !empty($validated['exponent_job']) ? ucwords(strtolower($validated['exponent_job'])) : null;
        $validated['location_gmap'] = !empty($validated['location_gmap']) ? ucwords(strtolower($validated['location_gmap'])) : null;

        try {
            // Si se sube una imagen, la procesamos y la agregamos al arreglo

            if ($request->hasFile('image_promo')) {
                $dateString = $validated['date_start'] ?? $event->date_start;
                if (!$dateString) {
                    return redirect()->back()->withErrors('La fecha de inicio es necesaria para actualizar la imagen.');
                }

                $date = \Carbon\Carbon::parse($dateString);
                $year = $date->year;
                $month = strtolower($date->locale('es')->isoFormat('MMMM'));
                $folder = "eventos/{$year}/{$month}";
                $file = $request->file('image_promo');

                $extension = $file->getClientOriginalExtension();
                $filename = \Illuminate\Support\Str::random(20) . '.' . $extension;
                $path = $folder . '/' . $filename;

                // Usa un stream para subir el archivo
                $success = Storage::disk('s3')->put($path, fopen($file->getRealPath(), 'r+'));

                if (!$success) {
                    \Log::error('No se pudo subir la imagen a S3', [
                        'path' => $path,
                        'user' => auth()->id(),
                        'event_id' => $event->id ?? null,
                    ]);
                    return redirect()->back()->withErrors('No se pudo subir la imagen a S3. Revisa las credenciales, permisos y configuración.');
                }

                $url = Storage::disk('s3')->url($path);
                $validated['image_promo'] = $url;
            } else {
                unset($validated['image_promo']);
            }

            $event->update($validated);
            $event->createURL();
            $event->updateSEOfields();

            return redirect()->route('marketing.events.index')
                ->with('success', 'Evento actualizado exitosamente.');
        } catch (\Exception $e) {
            throw $e;
            return redirect()->back()
                ->withErrors('No se pudo actualizar el evento.')
                ->withInput();
        }
    }


    /**
     * Elimina un evento de la base de datos.
     */
    public function destroy(Event $event)
    {
        try {
            // Marca el evento como inactivo
            $event->active = false;
            $event->save();

            return redirect()->route('marketing.events.index')
                ->with('success', 'Evento desactivado exitosamente.');
        } catch (\Exception $e) {
            \Log::error('Error al desactivar evento: ' . $e->getMessage());
            return redirect()->back()->withErrors('No se pudo desactivar el evento.');
        }
    }

    public function activate(Event $event)
    {
        try {
            $event->active = true;
            $event->save();

            return redirect()->route('marketing.events.index')
                ->with('success', 'Evento activado exitosamente.');
        } catch (\Exception $e) {
            \Log::error('Error al activar evento: ' . $e->getMessage());
            return redirect()->back()->withErrors('No se pudo activar el evento.');
        }
    }


    public function datatable(Request $request)
    {
        $query = Event::withCount(['attendances', 'confirmedAttendances']);

        return DataTables::eloquent($query)
            ->editColumn('title', function ($event) {
                return $event->title_link;
            })
            ->editColumn('time_range', function ($event) {
                return $event->time_range; // Usa el accesor getTimeRangeAttribute()
            })
            ->editColumn('active', function ($event) {
                return $event->active ? '<span class="badge bg-success">Activado</span>' : '<span class="badge bg-danger">Desactivado</span>';
            })
            ->editColumn('confirmed_attendances_count', function ($event) {
                $confirmed = $event->confirmed_attendances_count;
                $total = $event->attendances_count;
                if ($total > 0 && $confirmed < ($total / 2)) {
                    return '<span class="text-danger">' . $confirmed . '</span>';
                }
                return $confirmed;
            })
            ->addColumn('score', function ($event) {
                return $event->score;
            })
            ->addColumn('information', function ($event) {
                // Obtén la password desde el .env (asegúrate de que esté definida, por ejemplo, WEB_REVIEW_PASSWORD)
                $webReviewPassword = env('WEB_REVIEW_PASSWORD');
                // Usamos el slug si ya existe, de lo contrario lo generamos
                $slug = $event->slug ?? \Illuminate\Support\Str::slug($event->title, '-') . '-' . $event->id;
                // Arma la URL de revisión
                $reviewUrl = "https://www.artilec.com/events/{$slug}/register/qr?password={$webReviewPassword}";
                return '<a href="' . $reviewUrl . '" target="_blank" class="btn btn-sm btn-info" title="Revisar"><i class="fas fa-eye"></i></a>';
            })
            ->addColumn('action', function ($event) {
                $buttons = '';
                if ($event->active) {
                    // Si el evento está activo, muestra el botón de editar...
                    $editUrl = route('marketing.events.edit', $event->id);
                    $buttons .= '<a href="' . $editUrl . '" class="btn btn-sm btn-primary" title="Editar">
                                <i class="fas fa-edit"></i>
                             </a>';
                    // ... y el botón para desactivar
                    $deactivateUrl = route('marketing.events.destroy', $event->id);
                    $buttons .= '<form action="' . $deactivateUrl . '" method="POST" class="d-inline" onsubmit="confirmAction(event, this, \'desactivar\')">
                ' . csrf_field() . method_field('DELETE') . '
                <button type="submit" class="btn btn-sm btn-warning" title="Desactivar">
                    <i class="fas fa-ban"></i>
                </button>
            </form>';
                } else {
                    // Si el evento está desactivado, no se muestra el botón de editar; se muestra el botón para activarlo
                    $activateUrl = route('marketing.events.activate', $event->id);
                    $buttons .= '<form action="' . $activateUrl . '" method="POST" class="d-inline" onsubmit="confirmAction(event, this, \'activar\')">
                ' . csrf_field() . method_field('PUT') . '
                <button type="submit" class="btn btn-sm btn-success" title="Activar">
                    <i class="fas fa-check"></i>
                </button>
            </form>';
                }

                return $buttons;
            })
            ->rawColumns(['action', 'title', 'active', 'confirmed_attendances_count', 'information'])
            ->make(true);
    }


    public function attendee()
    {
        return $this->belongsTo(EventAttendee::class, 'attendee_id');
    }


}
