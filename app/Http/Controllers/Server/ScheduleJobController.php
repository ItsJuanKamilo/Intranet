<?php

namespace App\Http\Controllers\Server;

use App\Http\Controllers\Controller;
use App\Models\Role;
use App\Models\Server\ScheduleJob;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

class ScheduleJobController extends Controller
{
    /**
     * Mostrar la lista de jobs programados.
     */
    public function index()
    {
        $jobs = ScheduleJob::all();
        return view('admin.schedule_jobs.index', compact('jobs'));
    }

    /**
     * Mostrar formulario para crear un nuevo Job.
     */
    public function create()
    {
        $roles = Role::all(); // Obtener todos los roles
        return view('admin.schedule_jobs.create', compact('roles'));
    }


    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|unique:schedule_jobs,name',
            'role_id' => 'required|exists:roles,id',
            'type' => 'required|in:Report,Update,Check,Sync',
            'schedule' => 'nullable|string',
            'description' => 'nullable|string|max:255',
            'days' => 'required|string',
            'custom_days' => 'nullable|array',
        ]);

        // Process the schedule and days fields
        $schedule = $request->schedule;
        $scheduleParts = explode(' ', $schedule);

        if ($request->days == 'weekdays') {
            $scheduleParts[4] = '1-5';
        } elseif ($request->days == 'custom') {
            $customDays = implode(',', $request->custom_days);
            $scheduleParts[4] = $customDays;
        } elseif ($request->days == 'weekly') {
            $customDays = implode(',', $request->custom_days);
            $scheduleParts[4] = $customDays;
        } elseif ($request->days == 'monthly') {
            $scheduleParts = ['0', '9', '1', '*', '*'];
        }

        $schedule = implode(' ', $scheduleParts);

        // Obtener el nombre del rol
        $role = Role::find($request->role_id);
        if (!$role) {
            return redirect()->back()->with('error', 'Rol no encontrado.');
        }

        // Generar el nombre optimizado de la clase
        $optimizedClass = Str::of($request->name)
            ->replaceMatches('/[^A-Za-z0-9]/', ' ') // Eliminar caracteres especiales
            ->title() // Capitalizar cada palabra
            ->replace(' ', ''); // Unir sin espacios

        // Definir el namespace donde se guardará el Job
        $namespace = "App\\Jobs\\{$role->name}\\{$request->type}";
        $fullClassName = "{$namespace}\\{$optimizedClass}";

        // Definir la ruta del Job
        $jobPath = app_path("Jobs/{$role->name}/".Str::title($request->type));

        // Crear el directorio si no existe
        if (!file_exists($jobPath)) {
            mkdir($jobPath, 0775, true);
            chmod($jobPath, 0755);
        }

        // Verificar si el archivo ya existe para no sobrescribirlo
        $jobFile = "{$jobPath}/{$optimizedClass}.php";
        if (!file_exists($jobFile)) {
            // Usar Artisan para generar el Job
            Artisan::call('make:job', [
                'name' => "{$role->name}/{$request->type}/{$optimizedClass}"
            ]);
            if (!chmod($jobFile, 0674)) {
                if (File::exists($jobFile)) {
                    File::delete($jobFile);
                }
                return redirect()->back()->with('error', 'Failed to set permissions for file.');
            }
        }

        // Guardar el registro en la base de datos
        $job = ScheduleJob::create([
            'name' => $request->name,
            'role_id' => $request->role_id,
            'type' => $request->type,
            'schedule' => $schedule,
            'description' => $request->description,
            'enabled' => false,
            'class' => $fullClassName, // Guardar el namespace en la BD
            'path' => $jobFile, // Guardar la ruta del archivo en la BD
        ]);

        return redirect()->route('admin.schedule_jobs.index')->with('success', 'Job creado correctamente.');
    }
    /**
     * Mostrar detalles de un Job específico.
     */
    public function show(ScheduleJob $scheduleJob)
    {
        return view('admin.schedule_jobs.show', compact('scheduleJob'));
    }

    /**
     * Mostrar formulario para editar un Job.
     */
    public function edit(ScheduleJob $scheduleJob)
    {
        return view('admin.schedule_jobs.edit', compact('scheduleJob'));
    }

    /**
     * Actualizar un Job en la base de datos.
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'schedule' => 'nullable|string',
            'days' => 'required|string',
            'custom_days' => 'nullable|array',
            'enabled' => 'required|boolean',
        ]);

        $job = ScheduleJob::findOrFail($id);

        // Process the schedule and days fields
        $schedule = $request->schedule;
        $scheduleParts = explode(' ', $schedule);

        if ($request->days == 'weekdays') {
            $scheduleParts[4] = '1-5';
        } elseif ($request->days == 'custom') {
            $customDays = implode(',', $request->custom_days);
            $scheduleParts[4] = $customDays;
        } elseif ($request->days == 'weekly') {
            $customDays = implode(',', $request->custom_days);
            $scheduleParts[4] = $customDays;
        } elseif ($request->days == 'monthly') {
            $scheduleParts = ['0', '9', '1', '*', '*'];
        }

        $schedule = implode(' ', $scheduleParts);

        $job->update([
            'schedule' => $schedule,
            'days' => $request->days,
            'enabled' => $request->enabled,
        ]);

        return redirect()->route('admin.schedule_jobs.index')->with('success', 'Job actualizado correctamente.');
    }

    /**
     * Eliminar un Job de la base de datos.
     */
    public function destroy(ScheduleJob $scheduleJob)
    {
        // Use the path saved in the database
        $jobFilePath = $scheduleJob->path;

        // Debugging information
        if (!File::exists($jobFilePath)) {
            return redirect()->route('admin.schedule_jobs.index')
                ->with('error', 'Job file not found: ' . $jobFilePath);
        }

        // Check if the job file exists and delete it
        if (File::exists($jobFilePath)) {
            File::delete($jobFilePath);
        }

        // Delete the job record from the database
        $scheduleJob->delete();

        return redirect()->route('admin.schedule_jobs.index')
            ->with('success', 'Job eliminado correctamente.');
    }
}
