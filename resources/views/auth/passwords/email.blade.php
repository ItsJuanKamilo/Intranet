@extends('adminlte::auth.passwords.email')

@section('auth_body')
    <div class="card-body login-card-body">
        <!-- Mensaje de éxito si el correo se envió correctamente -->
        @if(session('status'))
            <div id="successMessage" class="alert alert-success text-center">
                <i class="fas fa-check-circle"></i> {{ session('status') }}
            </div>
        @endif

        <form method="POST" action="{{ route('password.email') }}" id="passwordResetForm">
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

            <!-- Botón Enviar -->
            <div class="d-grid">
                <button type="submit" id="submitButton" class="btn btn-block bg-navy">
                    <i class="fas fa-envelope"></i> Recuperar Contraseña
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

                // Limitar a 9 caracteres (ajusta según la longitud del RUT)
                if (rut.length > 9) {
                    rut = rut.slice(0, 9);
                }

                // Separar cuerpo y dígito verificador
                const body = rut.slice(0, -1);
                const dv = rut.slice(-1);

                // Formatear cuerpo con puntos cada tres dígitos
                const formattedBody = body.replace(/\B(?=(\d{3})+(?!\d))/g, '.');

                // Agregar el guión si hay al menos un número en el cuerpo
                return body.length > 1 ? formattedBody + '-' + dv : formattedBody + dv;
            };

            rutInput.addEventListener('input', () => {
                rutInput.value = formatRUT(rutInput.value);
            });
        });


        document.addEventListener('DOMContentLoaded', () => {
            const passwordResetForm = document.getElementById('passwordResetForm');
            const submitButton = document.getElementById('submitButton');
            const successMessage = document.getElementById('successMessage');

            // Si el mensaje de éxito está presente, deshabilitar el botón permanentemente
            if (successMessage) {
                submitButton.disabled = true;
                submitButton.innerHTML = '<i class="fas fa-check"></i> Enviado';
            }

            passwordResetForm.addEventListener('submit', () => {
                // Deshabilitar el botón y cambiar el texto al enviar
                submitButton.disabled = true;
                submitButton.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Enviando...';
            });
        });
    </script>
@endsection
