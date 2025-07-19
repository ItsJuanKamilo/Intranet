@extends('adminlte::page')

@section('title', 'Editar Rol')

@section('content')
    @include('components._page')

    <div class="card mt-4 card-navy card-outline">
        <div class="card-header">
            <h3 class="card-title">Editar Rol</h3>
        </div>

        <form action="{{ route('admin.roles.update', $role->id) }}" method="POST">
            @csrf
            @method('PUT')
            <div class="card-body">
                {{-- Campo: Nombre del Rol --}}
                <div class="mb-4">
                    <label for="name" class="form-label">Nombre del Rol</label>
                    <input type="text" name="name" id="name"
                           class="form-control @error('name') is-invalid @enderror"
                           value="{{ old('name', $role->name) }}"
                           placeholder="Ingresa el nombre del rol" required>
                    @error('name')
                    <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                {{-- Campo: Permisos --}}
                <div class="mb-4">
                    <label for="permissions" class="form-label">Permisos</label>
                    <div class="permissions-container">
                        @foreach($permissions as $permission)
                            <div class="checkbox-container">
                                <input type="checkbox" name="permissions[]" value="{{ $permission->id }}"
                                       id="perm-{{ $permission->id }}" class="checkbox-input"
                                       @if(in_array($permission->id, $rolePermissions)) checked @endif>
                                <label for="perm-{{ $permission->id }}" class="checkbox">
                                    <span class="line line1"></span>
                                    <span class="line line2"></span>
                                </label>
                                <p class="permission-text">{{$permission->name}}</p>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>

            {{-- Botones de Acción --}}
            <div class="card-footer d-flex justify-content-between">
                <a href="{{ route('admin.roles.index') }}" class="btn btn-dark">
                    <i class="fas fa-undo-alt"></i> Volver atrás
                </a>
                <button type="submit" class="btn btn-primary">
                    Guardar Cambios <i class="far fa-save"></i>
                </button>
            </div>
        </form>
    </div>
@endsection

@push('css')
    <style>
        /* Contenedor de Permisos */
        .permissions-container {
            display: flex;
            flex-wrap: wrap;
            justify-content: center;
            gap: 15px; /* Espaciado entre checkboxes */
            max-width: 100%;
        }

        /* Contenedor de cada Checkbox */
        .checkbox-container {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            width: 120px; /* Tamaño fijo para alineación */
            text-align: center;
        }

        /* Ocultar el checkbox original */
        .checkbox-input {
            display: none;
        }

        /* Estilos del Checkbox */
        .checkbox {
            position: relative;
            width: 30px;
            height: 30px;
            background: #e0e0e0;
            border-radius: 8px;
            display: flex;
            justify-content: center;
            align-items: center;
            cursor: pointer;
            transition: background-color 0.3s ease-in-out;
            box-shadow:
                inset -4px -4px 8px #ffffff,
                inset 4px 4px 8px #b0b0b0;
        }

        /* X Mark Lines */
        .line {
            position: absolute;
            width: 30px;
            height: 4px;
            background: #a0a0a0;
            border-radius: 2px;
            transition: all 0.3s ease-in-out;
        }

        .line1 {
            transform: rotate(45deg);
        }

        .line2 {
            transform: rotate(-45deg);
        }

        /* Checked State */
        .checkbox-input:checked + .checkbox {
            background: #d4e9d4;
        }

        .checkbox-input:checked + .checkbox .line1 {
            transform: rotate(50deg) translateX(-3px) translateY(8px) scaleX(0.6);
            background: #003b7a;
        }

        .checkbox-input:checked + .checkbox .line2 {
            transform: rotate(-50deg) translateX(6px) translateY(3px);
            background: #003b7a;
        }

        /* Etiqueta del Permiso */
        .permission-text {
            font-size: 14px;
            font-weight: bold;
            color: #333;
            margin-top: 5px;
            text-align: center;
        }

    </style>
@endpush
