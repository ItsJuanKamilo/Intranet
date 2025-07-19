<?php

namespace App\Http\Controllers;

use App\Mail\Marketing\Events\Attendee\SendQRCodeMail;
use App\Models\Web\Event;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;


class EventAttendeeController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Event $event)
    {
        // Contadores para los KPI
        $confirmedCount = $event->attendances()->whereNotNull('email_reminder_confirm_at')->count();
        $surveyCount = $event->attendances()->whereNotNull('email_survey')->count();
        $reminderCount = $event->attendances()->whereNotNull('email_reminder')->count();
        $attendedCount = $event->attendances()->where('confirmed', 1)->count();
        $totalAttendees = $event->attendances()->count();


        // Pasar las variables a la vista
        return view('marketing.events.edit', compact(
            'event',
            'confirmedCount',
            'surveyCount',
            'reminderCount',
            'attendedCount',
            'totalAttendees'
        ));
    }




    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Event $event, $attendeeId)
    {
        // Verifica que el asistente pertenezca al evento y, en caso afirmativo, elimínalo.
        $attendee = $event->attendances()->findOrFail($attendeeId);
        $attendee->delete();

        return redirect()->back()->with('success', 'Asistente eliminado exitosamente.');
    }

    public function confirm(Event $event, $attendee)
    {
        // Busca al asistente dentro del evento y actualiza los campos
        $attendeeRecord = $event->attendances()->findOrFail($attendee);
        $attendeeRecord->confirmed = 1;
        $attendeeRecord->confirmed_at = now();
        $attendeeRecord->save();

        return redirect()->back()->with('success', 'Asistencia confirmada exitosamente.');
    }

    public function sendQR(Event $event, $attendee)
    {
        // Busca al asistente dentro del evento
        $attendeeRecord = $event->attendances()->findOrFail($attendee);

        // Verifica que el asistente tenga un correo y un QR válido
        if (!$attendeeRecord->email || !$attendeeRecord->qr_image_url) {
            return response()->json(['message' => 'No se encontró email o QR para este asistente.'], 400);  // Error 400
        }

        // Envía el correo usando la clase Mailable
        try {
            Mail::to($attendeeRecord->email)->send(new SendQRCodeMail($attendeeRecord));

            // Si el correo se envía correctamente, devolvemos un mensaje de éxito
            return response()->json(['message' => 'Email con QR enviado exitosamente'], 200);  // Respuesta exitosa 200
        } catch (\Exception $e) {
            // Si ocurre un error, devolvemos un mensaje de error
            return response()->json(['message' => 'Hubo un error al enviar el correo: ' . $e->getMessage()], 500);  // Error 500
        }
    }



}
