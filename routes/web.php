<?php

use App\Http\Controllers\Auth\ForgotPasswordController;
use App\Http\Controllers\Auth\ResetPasswordController;
use App\Http\Controllers\Catalog\DahuaController;
use App\Http\Controllers\Catalog\ProductController;
use App\Http\Controllers\EventController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\Management\PlannedMarginController;
use App\Http\Controllers\Partners\PartnerController;
use App\Http\Controllers\Server\ReportController;
use App\Http\Controllers\Server\ScheduleJobController;
use App\Http\Controllers\Server\ServerController;
use App\Http\Controllers\Test\TestJLeitonController;
use App\Http\Controllers\Test\TestMtapayController;
use App\Http\Controllers\Users\PermissionController;
use App\Http\Controllers\Users\ProfileController;
use App\Http\Controllers\Users\RoleController;
use App\Http\Controllers\Users\UserController;
use App\Http\Middleware\PreventIframeLogin;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Route;


Route::get('/', function () {
    return view('auth.login');
});


//init-rutas de autenticación
Route::get('password/reset', [ForgotPasswordController::class, 'showLinkRequestForm'])->name('password.request');
Route::post('password/email', [ForgotPasswordController::class, 'sendResetLinkEmail'])->name('password.email');
Route::get('password/reset/{token}', [ResetPasswordController::class, 'showResetForm'])->name('password.reset');
Route::post('password/reset', [ResetPasswordController::class, 'reset'])->name('password.update');
//end-rutas de autenticación

Route::get('/inicio', function () {
    return view('inicio');
})->name('inicio');

// Rutas protegidas con middleware de autenticación
Route::middleware(['auth'])->group(function () {

    // Rutas para usuarios
    Route::prefix('admin')->name('admin.')->group(function () {
        Route::resource('users', UserController::class);
        Route::get('users/datatable', [UserController::class, 'datatable'])->name('users.datatable');
        Route::get('users/download/excel', [UserController::class, 'downloadExcel'])->name('users.download.excel');
        Route::post('users/{rut}/restore', [UserController::class, 'restore'])->name('users.restore');
        Route::resource('roles', RoleController::class);
        Route::resource('schedule_jobs', ScheduleJobController::class);
        Route::resource('permissions', PermissionController::class);
        Route::get('server', [ServerController::class, 'index'])->name('server');
        Route::resource('reports', ReportController::class);
        Route::post('reports/{report}/send-email', [ReportController::class, 'sendEmail'])->name('reports.sendEmail');


        Route::post('/horizon/clear', [ServerController::class, 'horizonClear'])->name('horizon.clear');
        Route::post('/horizon/restart', [ServerController::class, 'horizonRestart'])->name('horizon.restart');
    });

    Route::prefix('dahua')->name('dahua.')->group(function () {
        Route::resource('dahua', DahuaController::class);
        Route::get('dahua/datatable/index', [DahuaController::class, 'datatable'])->name('dahua.datatable');
    });

    Route::prefix('marketing')->name('marketing.')->group(function () {
        Route::resource('events', EventController::class);
        Route::get('events/datatable/index', [EventController::class, 'datatable'])->name('events.datatable');
        Route::put('events/{event}/image', [EventController::class, 'updateImage'])->name('events.update.image');
        Route::put('events/{event}/activate', [EventController::class, 'activate'])->name('events.activate');
        Route::delete('events/{event}/attendees/{attendee}', [\App\Http\Controllers\EventAttendeeController::class, 'destroy'])
            ->name('events.attendees.destroy');
        Route::get('events/{event}/attendees/datatable', [\App\Http\Controllers\EventAttendeeController::class, 'datatable'])
            ->name('events.attendees.datatable');
        Route::put('events/{event}/attendees/{attendee}/confirm', [\App\Http\Controllers\EventAttendeeController::class, 'confirm'])
            ->name('events.attendees.confirm');
        Route::post('events/{event}/attendees/{attendee}/sendqr', [\App\Http\Controllers\EventAttendeeController::class, 'sendQR'])
            ->name('events.attendees.sendqr');

    });

    Route::prefix('management')->name('management.')->group(function () {
        // Rutas generadas por resource para CRUD
        Route::resource('margins', PlannedMarginController::class);

        // Ruta personalizada para el datatable
        Route::get('margins/datatable/index', [PlannedMarginController::class, 'datatable'])->name('margins.datatable');

        // Ruta personalizada para la actualización vía AJAX
        Route::put('margins/{id}', [PlannedMarginController::class, 'updateAjax'])->name('margins.update.ajax');
    });



    //Rutas de Test
    Route::prefix('test')->name('test.')->group(function () {
        Route::get('testMtapay', [TestMtapayController::class,'index'])->name('testMtapay');
        Route::get('testJleiton', [TestJLeitonController::class, 'index'])->name('testJleiton');
    });

    // Rutas de perfil
    Route::prefix('profile')->name('profile.')->group(function () {
        Route::get('/', [ProfileController::class, 'show'])->name('show');
        Route::get('edit', [ProfileController::class, 'edit'])->name('edit');
        Route::put('update', [ProfileController::class, 'update'])->name('update');
    });

    Route::get('/home', [HomeController::class, 'index'])->name('home');


    // Rutas para Partners (Clientes)
    Route::resource('partners', PartnerController::class);
    Route::get('partners/datatable/index', [PartnerController::class, 'datatable'])
        ->name('partners.datatable');


    // Rutas para productos
    Route::resource('products', ProductController::class);
    Route::get('products/datatable/index', [ProductController::class, 'datatable'])
        ->name('products.datatable');

});



//TEST CORREO
Route::get('test-mail', function () {
    Mail::raw('Este es un correo de prueba', function ($message) {
        $message->to('mtapay@artilec.com')
            ->subject('Prueba de Correo');
    });

    return 'Correo de prueba enviado';
});

Auth::routes();

