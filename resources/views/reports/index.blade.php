@extends('adminlte::page')

@section('title', 'Listado de Reportes')

@section('content')
    @include('components._page')
    <div class="row mt-3">
        <div class="col-12">
            <div class="card card-navy card-outline">
                <div class="card-header text-center">
                    <h3 class="card-title mb-0">Listado de Reportes</h3>
                </div>

                <div class="card-body">
                    @php
                        // Definir las columnas que quieres en el thead
                        $heads = [
                            'ID',
                            'Nombre',
                            'Tipo',
                            'Estado',
                            'Autor',
                            'Duración',
                            'Origen',
                            'Fecha',
                            'Acciones',
                        ];

                        $config = [
                            'paging'     => true,
                            'searching'  => true,
                            'ordering'   => true,
                            'columnDefs' => [
                                // Desactivar el orden en la columna de Acciones (índice 6)
                                ['orderable' => false, 'targets' => 6],
                            ],
                            'language'   => [
                                'url' => 'https://cdn.datatables.net/plug-ins/1.13.6/i18n/es-ES.json',
                            ],
                            'dom' => '<"d-flex justify-content-between align-items-center"<"dt-export"B><"dt-search"f>>rt<"d-flex justify-content-end"p>',
                            'pageLength' => 10,
                            'order'      => [[0, 'desc']],
                        ];
                    @endphp

                    <x-adminlte-datatable
                        id="reports-table"
                        :heads="$heads"
                        :config="$config"
                        striped
                        hoverable
                        with-buttons
                        class="table table-striped table-hover table-sm m-0 text-nowrap"
                    >
                        @foreach ($reports as $report)
                            <tr>
                                <td>{{ $report->id }}</td>
                                <td>{{ $report->name }}</td>
                                <td>{{ $report->type }}</td>
                                <td>{{ $report->status }}</td>
                                <td>{{ is_array($report->emails) ? implode(', ', $report->emails) : $report->emails }}</td>
                                <td>{{ $report->duration }}</td>
                                <td>{{ $report->view }}</td>
                                <td>{{ $report->date_emailed }}</td>
                                <td class="text-center">
                                    <!-- Agregar los botones de acción -->
                                    <!-- Agregar botón de Enviar Correo -->
                                    <form action="{{ route('admin.reports.sendEmail', $report->id) }}" method="POST">
                                        @csrf
                                        <button type="submit" class="btn btn-sm btn-primary" title="Enviar Reporte por Email">
                                            <i class="fas fa-envelope"></i>
                                        </button>
                                    </form>

                                </td>
                            </tr>
                        @endforeach
                    </x-adminlte-datatable>
                </div>
            </div>
        </div>
    </div>
@stop
