@extends('adminlte::auth.auth-page', ['auth_type' => 'login'])

@section('auth_body')
    <div class="card-body login-card-body">
        @if(session('status'))
            <div class="alert alert-success text-center">
                <strong>{{ session('status') }}</strong>
                <br>Serás redirigido al inicio de sesión en unos segundos...
            </div>

            <script>
                setTimeout(() => {
                    window.location.href = "{{ route('login') }}";
                }, 4000);
            </script>
        @else
            <h5 class="login-box-msg fw-bolder text-navy">Ingrese su nueva contraseña</h5>

            <!-- Mostrar mensajes de error -->
            @if ($errors->any())
                <div class="alert alert-danger">
                    <ul>
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form method="POST" action="{{ route('password.update') }}">
                @csrf
                <input type="hidden" name="token" value="{{ $token }}">
                <input type="hidden" name="email" value="{{ request()->email }}">

                <!-- Nueva Contraseña -->
                <div class="mb-3">
                    <label for="password" class="form-label fw-bold text-navy">Nueva Contraseña</label>
                    <input type="password" name="password" id="password"
                           class="form-control @error('password') is-invalid @enderror"
                           placeholder="Ingrese su nueva contraseña" required minlength="8">
                    @error('password')
                    @php $errors->forget('password'); @endphp
                    @enderror
                </div>

                <!-- Confirmar Contraseña -->
                <div class="mb-3">
                    <label for="password_confirmation" class="form-label fw-bold text-navy">Confirmar Contraseña</label>
                    <input type="password" name="password_confirmation" id="password_confirmation"
                           class="form-control @error('password_confirmation') is-invalid @enderror"
                           placeholder="Confirme su nueva contraseña" required minlength="8">
                    @error('password')
                    @php $errors->forget('password'); @endphp
                    @enderror

                </div>

                <!-- Botón para restablecer la contraseña -->
                <div class="d-grid">
                    <button type="submit" id="resetButton" class="btn btn-block bg-navy">
                        <i class="fas fa-key"></i> Restablecer Contraseña
                    </button>
                </div>
            </form>
        @endif
    </div>
@endsection

@section('js')
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const resetButton = document.getElementById('resetButton');

            document.querySelector('form').addEventListener('submit', () => {
                resetButton.disabled = true;
                resetButton.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Procesando...';
            });
        });
    </script>
@endsection
