<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;
use App\Rules\ValidRut;
use Carbon\Carbon;

class LoginController extends Controller
{
    use AuthenticatesUsers;

    protected $redirectTo = '/home';

    public function __construct()
    {
        $this->middleware('guest')->except('logout');
    }

    /**
     * Define el campo principal como `rut` para la autenticación.
     *
     * @return string
     */
    public function username()
    {
        return 'rut'; // Campo principal para el inicio de sesión
    }

    /**
     * Personaliza la validación del inicio de sesión.
     *
     * @param Request $request
     * @return \Illuminate\Http\Response|\Illuminate\Contracts\Validation\Validator
     */
    public function login(Request $request)
    {
        // Validar los datos del formulario, incluyendo la regla ValidRut
        $request->validate([
            'rut' => ['required', new ValidRut],
            'password' => ['required'],
        ]);

        // Si la validación pasa, intenta autenticar
        return $this->attemptLogin($request)
            ? $this->sendLoginResponse($request)
            : $this->sendFailedLoginResponse($request);
    }

    /**
     * Ajustar el intento de inicio de sesión para manejar RUT y DV separados.
     *
     * @param Request $request
     * @return bool
     */
    protected function attemptLogin(Request $request)
    {
        // Limpia puntos y guion del RUT ingresado
        $rutInput = str_replace(['.', '-'], '', $request->input('rut'));

        // Divide el RUT y el DV
        $rut = substr($rutInput, 0, -1); // Todo menos el último carácter
        $dv = strtoupper(substr($rutInput, -1)); // Último carácter en mayúscula

        // Autentica usando el guard con ambas columnas
        return $this->guard()->attempt([
            'rut' => $rut,
            'dv' => $dv,
            'password' => $request->input('password'),
        ], $request->filled('remember'));
    }

    /**
     * Mensaje de error cuando el login falla.
     *
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    protected function sendFailedLoginResponse(Request $request)
    {
        return back()->withInput($request->only('rut', 'remember'))
            ->withErrors([
                'rut' => __('Credenciales incorrectas'),
            ]);
    }

    /**
     * Invalida la sesión al cerrar sesión y redirige al login.
     *
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function logout(Request $request)
    {
        $this->guard()->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect('/login');
    }


    /**
     * Metodo que se ejecuta después de un inicio de sesión exitoso.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  mixed  $user
     * @return mixed
     */
    protected function authenticated(Request $request, $user)
    {
        $user->ip_last_visit = $request->ip();
        $user->date_last_visit = Carbon::now()->toDateTimeString();
        $user->save();

        return redirect('/home');
    }
}
