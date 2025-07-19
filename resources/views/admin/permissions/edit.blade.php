@extends('adminlte::page')

@section('title', 'Editar Permiso')

@section('content')
    @include('components._page')

    <div class="card mt-4 card-navy card-outline">
        <div class="card-header">
            <h3 class="card-title">Editar Permiso</h3>
            <div class="card-tools">
                <button type="button" class="btn btn-tool" data-card-widget="collapse" title="Collapse">
                    <i class="fas fa-minus"></i>
                </button>
            </div>
        </div>

        <form action="{{ route('admin.permissions.update', $permission->id) }}" method="POST">
            @csrf
            @method('PUT')
            <div class="card-body">
                <div class="row">

                    {{-- Nombre del Permiso --}}
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Nombre del Permiso</label>
                            <input type="text" name="name" class="form-control @error('name') is-invalid @enderror"
                                   value="{{ old('name', $permission->name) }}" required>
                            @error('name')
                            <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>

                    {{-- Guard Name --}}
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Guard Name</label>
                            <input type="text" name="guard_name" class="form-control @error('guard_name') is-invalid @enderror"
                                   value="{{ old('guard_name', $permission->guard_name) }}" required>
                            @error('guard_name')
                            <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>

                </div>
            </div>

            <div class="card-footer">
                <div class="float-right">
                    <button type="submit" class="btn btn-primary">Actualizar <i class="far fa-save"></i></button>
                </div>
                <a href="{{ route('admin.permissions.index') }}" class="btn btn-dark">
                    <i class="fas fa-undo-alt"></i> Volver atrás
                </a>
            </div>
        </form>
    </div>
@endsection
