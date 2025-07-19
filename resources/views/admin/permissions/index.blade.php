@extends('adminlte::page')

@section('title', 'Lista de Permisos')

@section('content')
    @include('components._page')
    <div class="card mt-4 card-navy card-outline">
        <div class="card-header">
            <h5 class="card-title">Listado de Permisos</h5>
            <div class="card-tools">
                @role('1|11')
                <a href="{{ route('admin.permissions.create') }}" class="btn btn-sm btn-dark">
                    <i class="fas fa-user-shield me-1"></i> Nuevo Permiso
                </a>
                @endrole
                <button type="button" class="btn btn-tool" data-card-widget="collapse" title="Collapse">
                    <i class="fas fa-minus"></i>
                </button>
            </div>
        </div>

        <!-- Contenedor para búsqueda personalizada -->
        <div class="pt-4">
            <div id="custom-search-container"></div>
        </div>

        <div class="card-body p-2">
            <div class="table-responsive">
                <table id="permissions-table" class="table table-striped table-hover table-sm m-0">
                    <thead>
                    <tr>
                        <th>ID</th>
                        <th>Nombre</th>
                        <th>Guard Name</th>
                        <th>Fecha de Creación</th>
                        @role('1|11')
                        <th class="no-sort text-center">Acciones</th>
                        @endrole
                    </tr>
                    </thead>
                    <tbody>
                    @foreach($permissions as $permission)
                        <tr>
                            <td>{{$permission->id}}</td>
                            <td>{{ $permission->name }}</td>
                            <td>{{ $permission->guard_name }}</td>
                            <td>{{ $permission->created_at->format('d-m-Y H:i') }}</td>
                            @role('1|11')
                            <td class="text-center">
                                <!-- Botón Editar -->
                                <a href="{{ route('admin.permissions.edit', $permission->id) }}" class="btn btn-sm btn-primary btn-table" title="Editar">
                                    <i class="fa fa-edit"></i>
                                </a>

                                <!-- Botón Eliminar -->
                                <form action="{{ route('admin.permissions.destroy', $permission->id) }}" method="POST" class="d-inline" onsubmit="return confirm('¿Eliminar este permiso permanentemente?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-danger btn-table" title="Eliminar Permanentemente">
                                        <i class="fa fa-trash-alt"></i>
                                    </button>
                                </form>
                            </td>
                            @endrole
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        <div class="card-footer">
            @include('components.card-footer')
        </div>
    </div>
@endsection

@section('js')
    <script>
        $(document).ready(function() {
            // Inicializar DataTables
            let permissionsTable = $('#permissions-table').DataTable({
                paging: false,
                searching: true,
                ordering: true,
                columnDefs: [{ orderable: false, targets: "no-sort" }],
                language: { url: "https://cdn.datatables.net/plug-ins/1.13.6/i18n/es-ES.json" },
                dom: '<"d-flex align-items-center justify-content-center"<"header-search"f>>t', // Centra el campo de búsqueda
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
