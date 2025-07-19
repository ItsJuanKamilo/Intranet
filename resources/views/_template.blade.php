@extends('adminlte::page')

@section('title', 'Jobs Programados')

@section('content')
    @include('components._page')
    <div class="card mt-4 card-navy card-outline">
        <div class="card-header">
            <h3 class="card-title">
                Listado de Trabajos Programados
            </h3>
            <div class="card-tools">
                @role('1')
                <a href="{{ route('admin.schedule_jobs.create') }}" class="btn btn-sm btn-dark">Crear Job</a>
                @endrole
                <button type="button" class="btn btn-tool" data-card-widget="collapse" title="Collapse">
                    <i class="fas fa-minus"></i>
                </button>
            </div>
        </div>

        <div class="pt-4">
            <div id="custom-search-container"></div>
        </div>

        <div class="card-body"> <!-- card-body -->
            <table class="table table-bordered table-sm table-striped" id="jobs-table">
                <thead>
                <tr>
                    <th>Nombre</th>
                    <th>Tipo</th>
                    <th>Clase</th>
                    <th>Schedule</th>
                    <th>Estado</th>
                    <th>Acciones</th>
                </tr>
                </thead>
                <tbody>
                @foreach($jobs as $job)
                    <tr>
                        <td>{{ $job->name }}</td>
                        <td>{{ ucfirst($job->type) }}</td>
                        <td>{{ $job->class }}</td>
                        <td>{{ $job->schedule ?? 'Manual' }}</td>
                        <td>
                            <span class="badge bg-{{ $job->enabled ? 'success' : 'danger' }}">
                                {{ $job->enabled ? 'Activo' : 'Inactivo' }}
                            </span>
                        </td>
                        <td>
                            <a href="{{ route('schedule_jobs.show', $job->id) }}" class="btn btn-info btn-sm">Ver</a>
                            <a href="{{ route('schedule_jobs.edit', $job->id) }}" class="btn btn-warning btn-sm">Editar</a>
                            <form action="{{ route('schedule_jobs.destroy', $job->id) }}" method="POST" style="display:inline;">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('¿Eliminar este Job?')">Eliminar</button>
                            </form>
                        </td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div> <!-- card-body -->
        <div class="card-footer">
            @include('components.card-footer')
        </div>
    </div>
@endsection

@section('js')
    <script>
        $(document).ready(function() {

            let jobsTable= $('#jobs-table').DataTable({
                paging: false,
                searching: true,
                ordering: true,
                columnDefs: [{ orderable: false, targets: "no-sort" }],
                language:  "/assets/datatables/es-ES.json",
                dom: '<"d-flex align-items-center justify-content-center"<"header-search"f>>t', // Centra el campo de búsqueda
                initComplete: function () {
                    let searchContainer = $('#custom-search-container');
                    searchContainer.append($('.dataTables_filter'));

                    // Eliminar la etiqueta "Buscar"
                    searchContainer.find('label').contents().filter(function() {
                        return this.nodeType === 3; // Nodo de texto
                    }).remove();

                    // Cambiar placeholder del input
                    searchContainer.find('input').attr('placeholder', 'Buscar');
                }
            });

        });
    </script>
@endsection
