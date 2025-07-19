<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\ResetsPasswords;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;


class ResetPasswordController extends Controller
{
    use ResetsPasswords;

    protected $redirectTo = '/login';

    /**
     * Procesa la solicitud de restablecimiento de contraseña.
     */
    public function reset(Request $request)
    {
        // **1️⃣ Validar datos**
        $this->validateResetRequest($request);

        // **2️⃣ Verificar que el token sea válido antes de continuar**
        $user = User::where('email', $request->email)->first();

        if (!$user || !Password::broker()->tokenExists($user, $request->token)) {
            return back()->withErrors(['token' => __('passwords.token')]);
        }

        // **3️⃣ Ejecuta la lógica normal de restablecimiento de contraseña**
        $response = $this->broker()->reset(
            $this->credentials($request), function ($user, $password) {
            $this->resetPassword($user, $password);
        }
        );

        return $response == Password::PASSWORD_RESET
            ? $this->sendResetResponse($request, $response)
            : $this->sendResetFailedResponse($request, $response);
    }

    /**
     * Valida los datos de restablecimiento de contraseña.
     */
    protected function validateResetRequest(Request $request)
    {
        $request->validate([
            'email' => 'required|email|exists:users,email',
            'token' => 'required',
            'password' => 'required|string|min:8|confirmed',
        ], [
            'email.required' => 'El correo es obligatorio.',
            'email.email' => 'Debe ingresar un correo válido.',
            'email.exists' => 'No se encontró un usuario con ese correo.',
            'token.required' => 'El enlace para restablecer la contraseña no es válido o ha expirado.',
            'password.required' => 'La contraseña es obligatoria.',
            'password.min' => 'La contraseña debe tener al menos 8 caracteres.',
            'password.confirmed' => 'Las contraseñas no coinciden.',
        ]);
    }

    /**
     * Evita que se inicie sesión automáticamente después de restablecer la contraseña.
     */
    protected function resetPassword($user, $password)
    {
        // **Actualizar la contraseña en la base de datos**
        $user->forceFill([
            'password' => Hash::make($password),
        ])->save();

        // **Eliminar TODAS las sesiones activas en la base de datos (si Laravel usa sesiones en DB)**
        DB::table('sessions')->where('user_id', $user->rut)->delete();

        // **No iniciar sesión automáticamente**
        session()->flash('status', 'Tu contraseña ha sido restablecida correctamente. Inicia sesión con tu nueva contraseña.');
    }

    /**
     * Mensaje de éxito después de restablecer la contraseña.
     */
    protected function sendResetResponse(Request $request, $response)
    {
        // **Forzar cierre de sesión en todas las pestañas y eliminar cookies**
        return response('<script>
        // Notificar a todas las pestañas que cierren sesión
        localStorage.setItem("force_logout", "true");
        sessionStorage.clear(); // Eliminar cualquier sesión almacenada

        // Limpiar cookies para asegurarse de que la sesión se cierre completamente
        document.cookie.split(";").forEach(function(c) {
            document.cookie = c.replace(/^ +/, "").replace(/=.*/, "=;expires=" + new Date().toUTCString() + ";path=/");
        });

        // Redirigir al login
        window.location.href = "'.route('login', ['status' => 'password_changed']).'";
    </script>');
    }
}
