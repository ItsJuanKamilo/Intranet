<?php

use App\Models\Server\ScheduleJob;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schedule;

Schedule::command('telescope:clear')->daily();
Schedule::command('horizon:snapshot')->everyFiveMinutes();
Schedule::command('emails:dispatch')->everyMinute(); // o cada 5 min, hourly, etc.

try {
    $schedules = ScheduleJob::where('enabled', true)->get();
    foreach ($schedules as $schedule) {
        if (class_exists($schedule->class)) {
            // Pasa la instancia de ScheduleJob al constructor del job
            $job = new $schedule->class($schedule);
            Schedule::job($job)
                ->cron($schedule->schedule ?? '* * * * *')
                ->sentryMonitor()
                ->description("Job: {$schedule->class} (ScheduleJob ID: {$schedule->id})");
        } else {
            Log::error("El Job {$schedule->class} no existe.");
        }
    }
} catch (Exception $e) {
    Log::error($e->getMessage());
}

