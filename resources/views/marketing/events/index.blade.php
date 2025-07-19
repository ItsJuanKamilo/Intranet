@extends('adminlte::page')

@section('title', 'Listado de Eventos')

@section('content')
    @include('components._page')
    @php
        // Define las columnas que quieres en el thead.
        $heads = [
            'Id',
            'Tipo',
            'Nombre',
            'Marca',
            'Fecha/Inicio',
            'Fecha/Termino',
            'Horario',
            'Asis',
            'Conf',
            'Cupo',
            'Estado',
            'Puntuacion',
            'Info',
            ['label' => 'Acciones', 'no-export' => true, 'width' => 5],
        ];

        $config = [
            'ajax' => route('marketing.events.datatable'),
            'processing' => true,
            'serverSide' => true,
            'columns' => [
                ['data' => 'id', 'className' => 'text-end'],
                ['data' => 'type'],
                ['data' => 'title'],
                ['data' => 'brand'],
                ['data' => 'date_start', 'orderable' => true],
                ['data' => 'date_end', 'orderable' => true],
                ['data' => 'time_range', 'name' => 'date_time_start'],
                ['data' => 'attendances_count', 'className' => 'text-center', 'searchable' => false],
                ['data' => 'confirmed_attendances_count', 'className' => 'text-center', 'searchable' => false],
                ['data' => 'quota', 'className' => 'text-center', 'searchable' => false],
                ['data' => 'active', 'className'=> 'text-center','searchable' => false],
                ['data' => 'score', 'className' => 'text-center', 'searchable' => false],
                ['data' => 'information', 'className' => 'text-center','orderable' => false, 'searchable' => false],
                [
                    'data' => 'action',
                    'orderable' => false,
                    'searchable' => true,
                    'className' => 'text-center'
                ],
            ],
            'language' => [
                'url' => 'https://cdn.datatables.net/plug-ins/1.13.6/i18n/es-ES.json',
            ],
            'dom' => '<"top d-flex justify-content-between align-items-center"<"dt-export"B><"dt-search"f>>rt<"d-flex justify-content-end"p>',
            'pageLength' => 10,
            'order' => [[4, 'desc']],
            'columnDefs' => [
                [
                    'targets' => 4,
                    'type'    => 'date',
                    'orderSequence' => ['desc', 'asc']
                ],
                [
                    'targets' => 5,
                    'type'    => 'date',
                    'orderSequence' => ['desc', 'asc']
                ],
            ],
        ];
    @endphp

    <div class="card mt-4 ajax-datatable-compact card-navy card-outline">
        <div class="card-header">
            <h5 class="card-title">Listado de Eventos</h5>
            <div class="card-tools">
                <a href="{{ route('marketing.events.create') }}" class="btn btn-sm btn-dark">
                    <i class="fas fa-calendar-plus me-1"></i> Nuevo Evento
                </a>
                <button type="button" class="btn btn-tool" data-card-widget="collapse" title="Minimizar">
                    <i class="fas fa-minus"></i>
                </button>
            </div>
        </div>

        <!-- Contenedor para búsqueda personalizada (si la usas) -->
        <div class="pt-2">
            <div id="custom-search-container"></div>
        </div>

        <div class="card-body p-2">
            <div class="table-responsive p-1">
                <x-adminlte-datatable
                    id="events-table"
                    :heads="$heads"
                    :config="$config"
                    striped
                    hoverable
                    with-buttons
                    class="table table-striped table-hover table-sm m-0"
                />
            </div>
        </div>

        <div class="card-footer">
            @include('components.card-footer')
        </div>
    </div>

    @section('js')
        <script>
            $(document).ready(function () {
                var table = $('#events-table').DataTable();
                table.on('processing.dt', function (e, settings, processing) {
                    if (processing) {
                        $('#custom-loading').fadeIn();
                    } else {
                        $('#custom-loading').fadeOut();
                    }
                });
            });
        </script>
    @endsection
@endsection
