<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\SendsPasswordResetEmails;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Password;
use App\Models\User;
use App\Rules\ValidRut; // Asegúrate de tener esta regla implementada

class ForgotPasswordController extends Controller
{
    use SendsPasswordResetEmails;

    /**
     * Envía el link de reseteo de contraseña usando el RUT.
     */
    public function sendResetLinkEmail(Request $request)
    {
        // Valida que se envíe el RUT y que cumpla con la regla ValidRut
        $request->validate([
            'rut' => ['required', new ValidRut]
        ]);

        // Limpieza del RUT: eliminar puntos, guiones y demás caracteres no válidos
        $cleanedRut = preg_replace('/[^0-9kK]/', '', $request->rut);

        // Verifica que el RUT tenga al menos dos caracteres (cuerpo y dígito verificador)
        if (strlen($cleanedRut) < 2) {
            return back()->withErrors(['rut' => 'El formato del RUT es incorrecto.']);
        }

        // Separar el cuerpo y el dígito verificador
        $body = substr($cleanedRut, 0, -1);
        $dv = strtoupper(substr($cleanedRut, -1));

        // Buscar al usuario en la base de datos usando el cuerpo y el dígito verificador
        $user = User::where('rut', $body)
            ->where('dv', $dv)
            ->first();

        if (!$user) {
            return back()->withErrors(['rut' => 'No se encontró un usuario con ese RUT.']);
        }

        // Inyectar el correo del usuario en el request para que el trait procese el envío
        $request->merge(['email' => $user->email]);

        // Enviar el link de reseteo usando el broker de restablecimiento de contraseña
        $response = $this->broker()->sendResetLink(
            $request->only('email')
        );

        if ($response == Password::RESET_LINK_SENT) {
            return back()->with([
                'status' => 'Se ha enviado un correo de recuperación. Revisa tu bandeja de entrada.',
                'success' => true
            ]);
        }

        return back()->withErrors(['email' => 'No se pudo enviar el correo de recuperación. Inténtelo más tarde.']);
    }
}
