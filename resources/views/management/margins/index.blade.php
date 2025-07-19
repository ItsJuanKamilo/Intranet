@extends('adminlte::page')

@section('title', 'Listado de Planned Margins')

@section('content')
    @include('components._page')

    @php
        $heads = [
            'ID',
            'Marca',
            'Rubro',
            'Margen Objetivo',
            'Reseller Exclusivo',
            'Reseller Avanzado',
            'Factor Exclusivo',
            'Factor Avanzado',
            'Actualizado Por',
            'Fecha de Actualización',
            'Acciones',
        ];

        $config = [
            'ajax' => route('management.margins.datatable'),
            'columns' => [
                ['data' => 'id', 'className' => 'text-center'],
                ['data' => 'brand'],
                ['data' => 'category'],
                ['data' => 'target_margin'],
                ['data' => 'exclusive_reseller_margin'],
                ['data' => 'advanced_reseller_margin'],
                ['data' => 'exclusive_reseller_factor'],
                ['data' => 'advanced_reseller_factor'],
                ['data' => 'updated_by'],
                ['data' => 'updated_at'],
                ['data' => 'action', 'orderable' => false, 'searchable' => false, 'className' => 'text-center'],
            ],
            'language' => [
                'url' => 'https://cdn.datatables.net/plug-ins/1.13.6/i18n/es-ES.json',
            ],
            'dom' => '<"top d-flex justify-content-between align-items-center"<"dt-export"B><"dt-search"f>>rt<"d-flex justify-content-end"p>',
            'pageLength' => -1,
            'order' => [[1, 'asc'],[2,'asc']],
        ];
    @endphp

        <!-- Content wrapper adjustments -->
    <div class="content-wrapper">
        <div class="content">
            <div class="container-fluid">
                <div class="card mt-4 ajax-datatable-compact-margins card-navy card-outline">
                    <div class="card-header">
                        <h5 class="card-title">Listado de Margenes Planeados</h5>
                        <div class="card-tools">
                            <button type="button" class="btn btn-sm btn-dark" id="btnAddMargin">
                                <i class="fas fa-plus-circle me-1"></i> Nuevo Margen
                            </button>
                            <button type="button" class="btn btn-tool" data-card-widget="collapse" title="Minimizar">
                                <i class="fas fa-minus"></i>
                            </button>
                        </div>
                    </div>
                    <!-- Modal para agregar -->
                    <div class="modal fade" id="addModal" tabindex="-1" aria-labelledby="addModalLabel" aria-hidden="true">
                        <div class="modal-dialog modal-lg" style="min-width:90%;">
                            <div class="modal-content custom-modal-font-size">
                                <div class="modal-header">
                                    <h5 class="modal-title" id="addModalLabel">Agregar Nuevo Margen Planeado</h5>
                                </div>
                                <div class="modal-body">
                                    <form id="addForm">
                                        <!-- Fila 1: Datos generales -->
                                        <div class="row">
                                            <div class="col">
                                                <label for="brand" class="form-label">Marca</label>
                                                <select class="form-control" id="brand" name="brand" required>
                                                    <option value="">Selecciona una marca</option>
                                                    @foreach($brands as $brand)
                                                        <option value="{{ $brand->name }}">{{ $brand->name }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                            <div class="col">
                                                <label for="category" class="form-label">Rubro</label>
                                                <select class="form-control" id="category" name="category" required>
                                                    <option value="">Selecciona un rubro</option>
                                                    @foreach($categories as $category)
                                                        <option value="{{ $category->name }}">{{ $category->name }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>
                                        <hr>
                                        <!-- Fila 2: Configuración del margen y factores -->
                                        <div class="row">
                                            <div class="col">
                                                <label for="target_margin" class="form-label">Margen Objetivo</label>
                                                <input type="number" step="0.01" class="form-control" id="target_margin" name="target_margin" required>
                                            </div>
                                            <div class="col">
                                                <label for="exclusive_reseller_factor" class="form-label">Factor Exclusivo</label>
                                                <input type="number" step="0.01" class="form-control" id="exclusive_reseller_factor" name="exclusive_reseller_factor" required>
                                            </div>
                                            <div class="col">
                                                <label for="advanced_reseller_factor" class="form-label">Factor Avanzado</label>
                                                <input type="number" step="0.01" class="form-control" id="advanced_reseller_factor" name="advanced_reseller_factor" required>
                                            </div>
                                        </div>
                                        <div class="row mt-2">
                                            <div class="col">
                                                <label for="exclusive_reseller_margin" class="form-label">Margen Exclusivo</label>
                                                <input type="number" step="0.01" class="form-control" id="exclusive_reseller_margin" name="exclusive_reseller_margin" disabled>
                                            </div>
                                            <div class="col">
                                                <label for="advanced_reseller_margin" class="form-label">Margen Avanzado</label>
                                                <input type="number" step="0.01" class="form-control" id="advanced_reseller_margin" name="advanced_reseller_margin" disabled>
                                            </div>
                                        </div>
                                    </form>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary" id="closeAddModalButton" aria-label="Close">Cerrar</button>
                                    <button type="button" class="btn btn-primary" id="saveNewMargin">Guardar</button>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="pt-2">
                        <div id="custom-search-container"></div>
                    </div>

                    <div class="card-body p-2">
                        <div class="table-responsive p-1">
                            <x-adminlte-datatable
                                id="planned-margins-table"
                                :heads="$heads"
                                :config="$config"
                                striped
                                hoverable
                                with-buttons
                                class="table table-striped table-hover table-sm m-0 text-nowrap"
                            />
                        </div>
                    </div>

                    <div class="card-footer">
                        @include('components.card-footer')
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection


<!-- Modal para editar -->
<div class="modal fade" id="editModal" tabindex="-1" aria-labelledby="editModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" style="min-width:90%;">
        <div class="modal-content custom-modal-font-size">
            <div class="modal-header">
                <h5 class="modal-title" id="editModalLabel">Editar Margen Planeado</h5>
            </div>
            <div class="modal-body">
                <form id="editForm">
                    <!-- Fila 1: Modo solo lectura (muestra los valores actuales) -->
                    <h5 class="text-center text-gray">Datos Actuales</h5>
                    <hr class="my-1">
                    <div class="row">
                        <div class="col">
                            <label for="target_margin_1" class="form-label">Margen Objetivo</label>
                            <input type="number" class="form-control" id="target_margin_1" name="target_margin_1" disabled>
                        </div>
                        <div class="col">
                            <label for="exclusive_reseller_margin_1" class="form-label">Margen Exclusivo</label>
                            <input type="number" class="form-control" id="exclusive_reseller_margin_1" name="exclusive_reseller_margin_1" disabled>
                        </div>
                        <div class="col">
                            <label for="advanced_reseller_margin_1" class="form-label">Margen Avanzado</label>
                            <input type="number" class="form-control" id="advanced_reseller_margin_1" name="advanced_reseller_margin_1" disabled>
                        </div>
                        <div class="col">
                            <label for="exclusive_reseller_factor_1" class="form-label">Factor Exclusivo</label>
                            <input type="number" class="form-control" id="exclusive_reseller_factor_1" name="exclusive_reseller_factor_1" disabled>
                        </div>
                        <div class="col">
                            <label for="advanced_reseller_factor_1" class="form-label">Factor Avanzado</label>
                            <input type="number" class="form-control" id="advanced_reseller_factor_1" name="advanced_reseller_factor_1" disabled>
                        </div>
                    </div>

                    <!-- Fila 2: Modo solo lectura (muestra los valores actuales) -->
                    <h5 class="text-center text-gray mt-2">Ingresa los nuevos cambios</h5>
                    <hr class="my-1">
                    <div class="row">
                        <div class="col">
                            <label for="target_margin_2" class="form-label">Margen Objetivo</label>
                            <input type="number" class="form-control" id="target_margin_2" name="target_margin_2" >
                        </div>
                        <div class="col">
                            <label for="exclusive_reseller_margin_2" class="form-label">Margen Exclusivo</label>
                            <input type="number" class="form-control" id="exclusive_reseller_margin_2" name="exclusive_reseller_margin_2" disabled>
                        </div>
                        <div class="col">
                            <label for="advanced_reseller_margin_2" class="form-label">Margen Avanzado</label>
                            <input type="number" class="form-control" id="advanced_reseller_margin_2" name="advanced_reseller_margin_2" disabled>
                        </div>
                        <div class="col">
                            <label for="exclusive_reseller_factor_2" class="form-label">Factor Exclusivo</label>
                            <input type="number" class="form-control" id="exclusive_reseller_factor_2" name="exclusive_reseller_factor_2">
                        </div>
                        <div class="col">
                            <label for="advanced_reseller_factor_2" class="form-label">Factor Avanzado</label>
                            <input type="number" class="form-control" id="advanced_reseller_factor_2" name="advanced_reseller_factor_2">
                        </div>
                    </div>

                    <input type="hidden" id="margin_id">
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" id="closeModalButton" aria-label="Close">Cerrar</button>
                <button type="button" class="btn btn-primary" id="saveChanges">Guardar cambios</button>
            </div>
        </div>
    </div>
</div>


@section('js')
    <script>


        $('#btnAddMargin').on('click', function () {
            $('#addModal').modal('show');
        });
        function updateNewMargin() {
            var targetMargin = parseFloat($('#target_margin').val());
            var exclusiveFactor = parseFloat($('#exclusive_reseller_factor').val());
            var advancedFactor = parseFloat($('#advanced_reseller_factor').val());

            if (!isNaN(targetMargin) && !isNaN(exclusiveFactor) && exclusiveFactor >= 0 && exclusiveFactor <= 1) {
                var exclusiveMargin = (targetMargin * exclusiveFactor).toFixed(2);
                $('#exclusive_reseller_margin').val(exclusiveMargin);
            }

            if (!isNaN(targetMargin) && !isNaN(advancedFactor) && advancedFactor >= 0 && advancedFactor <= 1) {
                var advancedMargin = (targetMargin * advancedFactor).toFixed(2);
                $('#advanced_reseller_margin').val(advancedMargin);
            }
        }

        $('#target_margin, #exclusive_reseller_factor, #advanced_reseller_factor').on('input', function () {
            updateNewMargin();
        });


        // Después de que el nuevo margen sea creado, reseteamos el formulario
        $('#saveNewMargin').click(function () {
            // Mostrar la alerta de cargando
            Swal.fire({
                title: 'Cargando..',
                allowOutsideClick: false,
                allowEscapeKey: false,
                allowEnterKey: false,
                didOpen: () => {
                    Swal.showLoading();
                }
            });

            // Preparar los datos del formulario
            var data = {
                _token: '{{ csrf_token() }}',
                brand: $('#brand').val(),
                category: $('#category').val(),
                target_margin: $('#target_margin').val(),
                exclusive_reseller_factor: $('#exclusive_reseller_factor').val(),
                advanced_reseller_factor: $('#advanced_reseller_factor').val(),
                exclusive_reseller_margin: $('#exclusive_reseller_margin').val(),
                advanced_reseller_margin: $('#advanced_reseller_margin').val(),
            };

            // Realizar la solicitud AJAX para crear el nuevo margen
            $.ajax({
                url: '{{ route('management.margins.store') }}',
                type: 'POST',
                data: data,
                success: function (response) {
                    if (response.success) {
                        Swal.fire(
                            'Proceso Correcto',
                            response.success,
                            'success'
                        ).then(() => {
                            // Cerrar el modal, resetear el formulario y recargar la tabla
                            $('#addModal').modal('hide');
                            $('#addForm')[0].reset();  // Esto resetea el formulario

                            // Limpiar los selectores de los 'select' para que queden en blanco
                            $('#brand').val(null).trigger('change');  // Limpiar el campo de marca
                            $('#category').val(null).trigger('change');  // Limpiar el campo de rubro

                            // Recargar la tabla
                            $('#planned-margins-table').DataTable().ajax.reload();
                        });
                    }
                },
                error: function (xhr, status, error) {
                    // Si el error es 400 (margen ya existe), mostrar el error correspondiente
                    if (xhr.status == 400) {
                        Swal.fire(
                            'Error',
                            xhr.responseJSON.error,  // Muestra el mensaje de error
                            'error'
                        );
                    } else {
                        // Para otros errores, muestra un mensaje genérico
                        Swal.fire(
                            'Error en el Proceso',
                            'Ocurrió un error desconocido',
                            'error'
                        );
                    }
                }
            });
        });



        $('#closeAddModalButton').on('click', function () {
            $('#addModal').modal('hide');  // Cierra el modal
            $('#addForm')[0].reset();
            $('#brand').val(null).trigger('change');
            $('#category').val(null).trigger('change');
        });


        // MODAL PARA EDITAR EL MARGEN
        $(document).ready(function () {
            var table = $('#planned-margins-table').DataTable();

            // Cargar datos al modal al hacer clic en el botón de editar
            $('#planned-margins-table').on('click', '.edit-btn', function () {
                var rowData = table.row($(this).closest('tr')).data();

                // Rellenar el formulario con los datos actuales
                $('#target_margin_1').val(rowData.target_margin);
                $('#exclusive_reseller_margin_1').val(rowData.exclusive_reseller_margin);
                $('#advanced_reseller_margin_1').val(rowData.advanced_reseller_margin);
                $('#exclusive_reseller_factor_1').val(rowData.exclusive_reseller_factor);
                $('#advanced_reseller_factor_1').val(rowData.advanced_reseller_factor);

                $('#target_margin_2').val(rowData.target_margin);
                $('#exclusive_reseller_margin_2').val(rowData.exclusive_reseller_margin);
                $('#advanced_reseller_margin_2').val(rowData.advanced_reseller_margin);
                $('#exclusive_reseller_factor_2').val(rowData.exclusive_reseller_factor);
                $('#advanced_reseller_factor_2').val(rowData.advanced_reseller_factor);

                $('#margin_id').val(rowData.id);

                // Modificar el título del modal con los valores de "brand" y "category"
                $('#editModalLabel').text('Editar Margen Planeado / ' + rowData.brand + ' / ' + rowData.category);

                // Mostrar el modal
                $('#editModal').modal('show');
            });

            $('#closeModalButton').on('click', function () {
                $('#editModal').modal('hide'); // Cerrar el modal
            });

            // Calcular los márgenes en tiempo real
            function updateMargins() {
                var targetMargin = parseFloat($('#target_margin_2').val());
                var exclusiveFactor = parseFloat($('#exclusive_reseller_factor_2').val());
                var advancedFactor = parseFloat($('#advanced_reseller_factor_2').val());

                if (!isNaN(targetMargin) && !isNaN(exclusiveFactor) && exclusiveFactor >= 0 && exclusiveFactor <= 1) {
                    var exclusiveMargin = (targetMargin * exclusiveFactor).toFixed(2);
                    $('#exclusive_reseller_margin_2').val(exclusiveMargin);
                }

                if (!isNaN(targetMargin) && !isNaN(advancedFactor) && advancedFactor >= 0 && advancedFactor <= 1) {
                    var advancedMargin = (targetMargin * advancedFactor).toFixed(2);
                    $('#advanced_reseller_margin_2').val(advancedMargin);
                }
            }

            // Detectar cambios en los campos editables y actualizar los márgenes
            $('#target_margin_2, #exclusive_reseller_factor_2, #advanced_reseller_factor_2').on('input', function() {
                updateMargins();
            });

            $('#saveChanges').click(function () {
                // Mostrar la alerta de cargando
                Swal.fire({
                    title: 'Cargando..',
                    allowOutsideClick: false,
                    allowEscapeKey: false,
                    allowEnterKey: false,
                    didOpen: () => {
                        Swal.showLoading();
                    }
                });

                // Preparar los datos para la solicitud AJAX
                var data = {
                    _token: $('input[name="_token"]').val(),
                    target_margin: $('#target_margin_2').val(),
                    exclusive_reseller_margin: $('#exclusive_reseller_margin_2').val(),
                    advanced_reseller_margin: $('#advanced_reseller_margin_2').val(),
                    exclusive_reseller_factor: $('#exclusive_reseller_factor_2').val(),
                    advanced_reseller_factor: $('#advanced_reseller_factor_2').val(),
                };

                // Realizar la solicitud AJAX para guardar los cambios
                $.ajax({
                    url: '/management/margins/' + $('#margin_id').val(),
                    type: 'PUT',
                    data: data,
                    success: function(response) {
                        Swal.fire(
                            'Proceso Correcto',
                            response.message,
                            'success'
                        ).then(() => {
                            // Cerrar el modal y recargar la tabla con los nuevos datos
                            $('#editModal').modal('hide');
                            table.ajax.reload();
                        });
                    },
                    error: function(xhr, status, error) {
                        // Mostrar la alerta de error
                        Swal.fire(
                            'Error en el Proceso',
                            xhr.responseText || 'Ocurrió un error desconocido',
                            'error'
                        );
                    }
                });
            });

            // Cerrar modal correctamente al ocultarlo
            $('#editModal').on('hidden.bs.modal', function () {
                $('#editForm')[0].reset();
            });
        });



    </script>
@endsection


