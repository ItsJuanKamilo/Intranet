@extends('adminlte::page')

@section('title', 'Gestión de Roles')

@section('content')

    @include('components._page')
    <div class="row g-3 mt-3">
        <!-- KPI: Usuarios Sin Rol -->
        <div class="col-auto">
            <div class="info-box">
            <span class="info-box-icon bg-danger elevation-1">
                <i class="fas fa-user-slash"></i>
            </span>
                <div class="info-box-content">
                    <span class="info-box-text">Usuarios Sin Rol</span>
                    <span class="info-box-number">
                    {{ $usersWithoutRoleCount ?? 0 }}
                </span>
                </div>
            </div>
        </div>
    </div>



    <div class="card mt-1 card-navy card-outline">
        <!-- Encabezado de la Card -->
        <div class="card-header">
            <h5 class="card-title">Listado de Roles</h5>
            <div class="card-tools">
                <a href="{{ route('admin.roles.create') }}" class="btn btn-sm btn-dark">
                    <i class="fas fa-plus-circle me-1"></i> Nuevo Rol
                </a>
                <button type="button" class="btn btn-tool" data-card-widget="collapse" title="Collapse">
                    <i class="fas fa-minus"></i>
                </button>
            </div>
        </div>

        <!-- Contenedor del Buscador -->
        <div class="pt-3">
            <div id="custom-search-container"></div>
        </div>

        <!-- Cuerpo del card con la tabla -->
        <div class="card-body p-2">
            <div class="table-responsive">
                <table id="role-table" class="table table-striped table-hover table-sm m-0">
                    <thead>
                    <tr>
                        <th>Id</th>
                        <th>Nombre</th>
                        <th>Permisos</th>
                        <th>Usuarios</th>
                        <th class="no-sort text-center">Acciones</th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach($roles as $role)
                        <tr>
                            <td class="text-right">{{ $role->id }}</td>
                            <td>{{ $role->name }}</td>
                            <td>
                                @if($role->permissions->isNotEmpty())
                                    @foreach($role->permissions as $permission)
                                        <span class="badge bg-info text-wrap">
                                                {{$permission->name}}
                                            </span>
                                    @endforeach
                                @else
                                    <span class="text-muted">Sin permisos</span>
                                @endif
                            </td>
                            <!-- Hacer calculo de cuantos usuarios hay en cada rol -->
                            <td>{{ $role->users_count }}</td>

                            <td class="text-center">
                                <a href="{{ route('admin.roles.edit', $role->id) }}" class="btn btn-sm btn-primary btn-table" title="Editar">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <form action="{{ route('admin.roles.destroy', $role->id) }}" method="POST" class="d-inline" onsubmit="return confirm('¿Eliminar este rol permanentemente?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-danger btn-table" title="Eliminar Permanentemente">
                                        <i class="fa fa-trash-alt"></i>
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Footer de la Card -->
        <div class="card-footer">
            @include('components.card-footer')
        </div>
    </div>
@endsection

@section('js')
    <script>
        $(document).ready(function() {

            let rolesTable = $('#role-table').DataTable({
                paging: false,
                searching: true,
                ordering: true,
                columnDefs: [{ orderable: false, targets: "no-sort" }],
                language: "/assets/datatables/es-ES.json",
                dom: '<"d-flex align-items-center justify-content-center"<"header-search"f>>t',
                initComplete: function () {
                    let searchContainer = $('#custom-search-container');
                    searchContainer.append($('.dataTables_filter'));

                    // Eliminar la etiqueta "Buscar"
                    searchContainer.find('label').contents().filter(function() {
                        return this.nodeType === 3;
                    }).remove();

                    // Cambiar placeholder del input
                    searchContainer.find('input').attr('placeholder', 'Buscar');
                }
            });

        });
    </script>
@endsection
