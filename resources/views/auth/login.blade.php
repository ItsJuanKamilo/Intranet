@extends('adminlte::auth.login')

@section('title', 'Iniciar Sesión')

@section('auth_body')
    <script>
        // Si no somos la ventana principal, redirigimos al top window
        if (window.top !== window.self) {
            // Construimos la URL con el parámetro de alerta
            let url = new URL(window.location.href);
            url.searchParams.set('status', 'session_expired');
            window.top.location.href = url.href;
        }
    </script>

    <div class="card-body login-card-body">
        <!-- Alerta de éxito si se cambió la contraseña -->
        @if(request()->query('status') == 'password_changed')
            <div class="alert alert-success text-center">
                <strong>Tu contraseña ha sido cambiada correctamente.</strong>
            </div>
        @endif

        <!-- Alerta de sesión expirada-->
        @if(request()->query('status') == 'session_expired')
            <div class="alert alert-warning text-center">
                <strong>Tu sesión ha expirado. Inicia sesión nuevamente.</strong>
            </div>
        @endif
        <form method="POST" action="{{ route('login') }}">
            @csrf

            <!-- Campo RUT -->
            <div class="mb-3">
                <label for="rut" class="form-label fw-bold text-navy">RUT</label>
                <div class="input-group">
                    <input type="text" name="rut" id="rut"
                           class="form-control @error('rut') is-invalid @enderror"
                           placeholder="XX.XXX.XXX-X" value="{{ old('rut') }}" required autofocus maxlength="13">
                    <div class="input-group-append">
                        <div class="input-group-text">
                            <span class="fas fa-id-card"></span>
                        </div>
                    </div>
                    @error('rut')
                    <span class="invalid-feedback" role="alert">
                        <strong>{{ $message }}</strong>
                    </span>
                    @enderror
                </div>
            </div>

            <!-- Campo Contraseña -->
            <div class="mb-3">
                <label for="password" class="form-label fw-bold text-navy">Contraseña</label>
                <div class="input-group">
                    <input type="password" name="password" id="password"
                           class="form-control @error('password') is-invalid @enderror"
                           placeholder="Contraseña" required>
                    <div class="input-group-append">
                        <div class="input-group-text">
                            <span class="fas fa-lock"></span>
                        </div>
                    </div>
                    @error('password')
                    <span class="invalid-feedback" role="alert">
                        <strong>{{ $message }}</strong>
                    </span>
                    @enderror
                </div>
            </div>


            <!-- Botón Enviar -->
            <div class="d-grid">
                <button type="submit" class="btn btn-block bg-navy">
                    <i class="fas fa-sign-in-alt"></i> Iniciar Sesión
                </button>
            </div>

        </form>
    </div>
@endsection

@section('js')
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const rutInput = document.getElementById('rut');

            const formatRUT = (rut) => {
                // Remover caracteres no válidos y convertir el dígito verificador a mayúsculas
                rut = rut.replace(/[^0-9kK]/g, '').toUpperCase();

                // Limitar a 9 caracteres (ajusta según la longitud mínima/máxima del RUT)
                if (rut.length > 9) {
                    rut = rut.slice(0, 9);
                }

                // Separar cuerpo y dígito verificador
                const body = rut.slice(0, -1);
                const dv = rut.slice(-1);

                // Formatear cuerpo con puntos cada tres dígitos
                const formattedBody = body.replace(/\B(?=(\d{3})+(?!\d))/g, '.');

                // Agregar el guion solo si hay al menos un número en el cuerpo
                return body.length > 1 ? formattedBody + '-' + dv : formattedBody + dv;
            };

            rutInput.addEventListener('input', () => {
                rutInput.value = formatRUT(rutInput.value);
            });
        });



            document.addEventListener("DOMContentLoaded", function () {
            // Buscar cualquier alerta en la página y ocultarla después de 4 segundos (4000ms)
            setTimeout(() => {
                document.querySelectorAll('.alert').forEach(alert => {
                    alert.style.transition = "opacity 0.5s ease";
                    alert.style.opacity = "0";
                    setTimeout(() => alert.remove(), 500); // Espera el tiempo de la transición antes de removerlo
                });
            }, 4000);
        });

        document.addEventListener("DOMContentLoaded", function () {
            // Si no estamos en la página de login y el flag de sesión no está establecido, redirige
            if(window.location.pathname !== "/login" && !localStorage.getItem("session_active")) {
                window.location.href = "{{ route('login', ['status' => 'session_expired']) }}";
            }
            // Cada vez que el usuario interactúe, actualiza el flag
            document.addEventListener("mousemove", () => localStorage.setItem("session_active", "true"));
            document.addEventListener("keydown", () => localStorage.setItem("session_active", "true"));
        });


    </script>
@endsection


