@extends('adminlte::page')

@section('title', 'Crear Evento')

@section('content')
    <div class="row">
        <div class="col-12">
            <div class="card mt-4 card-navy card-outline">
                <div class="card-header d-flex align-items-center">
                    <h3 class="card-title mb-0">Crear Evento</h3>
                    <i class="fas fa-question-circle ml-auto"
                       data-toggle="tooltip"
                       data-placement="left"
                       data-html="true"
                       title="Se han rellenado los campos con los datos del evento anterior como referencia. Los campos 'Visible' y 'Activo', solo podrán habilitarse mediante la edición del evento."></i>
                    <script>
                        $(function () {
                            $('[data-toggle="tooltip"]').tooltip({
                                container: 'body'
                            });
                        });
                    </script>

                </div>
                <script>
                    document.addEventListener('DOMContentLoaded', function () {
                        var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
                        tooltipTriggerList.map(function (tooltipTriggerEl) {
                            return new bootstrap.Tooltip(tooltipTriggerEl)
                        })
                    });
                </script>

                <div class="card-body p-0">
                            <form action="{{ route('marketing.events.store') }}" method="POST" enctype="multipart/form-data" id="form-event">
                                @csrf
                                <div class="card-body">
                                    <!-- Row 1: Visible, Fechas y Horas, Tipo -->
                                    <div class="row g-3 mb-4">
                                        <div class="col-md-1">
                                            <label for="visible" class="form-label">Visible</label>
                                            <select id="visible" class="form-control" disabled>
                                                <option value="0" selected>No</option>
                                            </select>
                                            <input type="hidden" name="visible" value="0">
                                        </div>
                                        <div class="col-md-1">
                                            <label for="active" class="form-label">Activo</label>
                                            <select id="active" class="form-control" disabled>
                                                <option value="0" selected>No</option>
                                            </select>
                                            <input type="hidden" name="active" value="0">
                                        </div>


                                        <div class="col-md-2">
                                            <label for="date_start" class="form-label">Fecha Inicio</label>
                                            <input type="date" id="date_start" name="date_start" class="form-control" required
                                                   value="{{ old('date_start', isset($lastEvent) ? $lastEvent->date_start : '') }}">
                                            @error('date_start')<div class="text-danger">{{ $message }}</div>@enderror
                                        </div>

                                        <div class="col-md-2">
                                            <label for="date_end" class="form-label">Fecha Fin</label>
                                            <input type="date" id="date_end" name="date_end" class="form-control" required
                                            value="{{ old('date_end', isset($lastEvent) ? $lastEvent->date_end : '') }}">
                                            @error('date_end')<div class="text-danger">{{ $message }}</div>@enderror
                                        </div>
                                        <div class="col-md-2">
                                            <label for="date_time_start" class="form-label">Hora Inicio</label>
                                            <input type="time" id="date_time_start" name="date_time_start" class="form-control" required
                                            value="{{ old('date_time_start', isset($lastEvent) ? $lastEvent->date_time_start : '') }}">
                                            @error('date_time_start')<div class="text-danger">{{ $message }}</div>@enderror
                                        </div>
                                        <div class="col-md-2">
                                            <label for="date_time_end" class="form-label">Hora Fin</label>
                                            <input type="time" id="date_time_end" name="date_time_end" class="form-control" required
                                            value="{{ old('date_time_end', isset($lastEvent) ? $lastEvent->date_time_end : '') }}">
                                            @error('date_time_end')<div class="text-danger">{{ $message }}</div>@enderror
                                        </div>
                                        <div class="col-md-2">
                                            <label for="type" class="form-label">Tipo</label>
                                            <select id="type" name="type" class="form-control" required>
                                                <option value="" disabled {{ old('type', isset($lastEvent) ? $lastEvent->type : '') == '' ? 'selected' : '' }}>Seleccionar</option>
                                                <option value="CERTIFICACION" {{ old('type', isset($lastEvent) ? $lastEvent->type : '') == 'CERTIFICACION' ? 'selected' : '' }}>CERTIFICACION</option>
                                                <option value="TALLER" {{ old('type', isset($lastEvent) ? $lastEvent->type : '') == 'TALLER' ? 'selected' : '' }}>TALLER</option>
                                                <option value="CAPACITACION" {{ old('type', isset($lastEvent) ? $lastEvent->type : '') == 'CAPACITACION' ? 'selected' : '' }}>CAPACITACION</option>
                                                <option value="DESAYUNO" {{ old('type', isset($lastEvent) ? $lastEvent->type : '') == 'DESAYUNO' ? 'selected' : '' }}>DESAYUNO</option>
                                                <option value="PRESENTACION" {{ old('type', isset($lastEvent) ? $lastEvent->type : '') == 'PRESENTACION' ? 'selected' : '' }}>PRESENTACION</option>
                                                <option value="WEBINAR" {{ old('type', isset($lastEvent) ? $lastEvent->type : '') == 'WEBINAR' ? 'selected' : '' }}>WEBINAR</option>
                                                <option value="LANZAMIENTO" {{ old('type', isset($lastEvent) ? $lastEvent->type : '') == 'LANZAMIENTO' ? 'selected' : '' }}>LANZAMIENTO</option>
                                            </select>
                                            @error('type')<div class="text-danger">{{ $message }}</div>@enderror
                                        </div>
                                    </div>
                                    <!-- Row 2: Título, Marca, Rubro, SKU -->
                                    <div class="row g-3 mb-4">
                                        <div class="col-md-6">
                                            <label for="title" class="form-label">Título</label>
                                            <input type="text" id="title" name="title" class="form-control" required
                                            value="{{ old('title', isset($lastEvent) ? $lastEvent->title : '') }}">
                                            @error('title')<div class="text-danger">{{ $message }}</div>@enderror
                                        </div>
                                        <div class="col-md-2">
                                            <label for="brand" class="form-label">Marca</label>
                                            <input type="text" id="brand" name="brand" class="form-control" required
                                            value="{{ old('brand', isset($lastEvent) ? $lastEvent->brand : '') }}">
                                            @error('brand')<div class="text-danger">{{ $message }}</div>@enderror
                                        </div>
                                        <div class="col-md-2">
                                            <label for="category" class="form-label">Rubro</label>
                                            <select id="category" name="category" class="form-control" required>
                                                <option value="" disabled {{ old('category', isset($lastEvent) ? $lastEvent->category : '') == '' ? 'selected' : '' }}>Seleccionar</option>
                                                <option value="CCTV" {{ old('category', isset($lastEvent) ? $lastEvent->category : '') == 'CCTV' ? 'selected' : '' }}>CCTV</option>
                                                <option value="CABLEADO ESTRUCTURADO" {{ old('category', isset($lastEvent) ? $lastEvent->category : '') == 'CABLEADO ESTRUCTURADO' ? 'selected' : '' }}>CABLEADO ESTRUCTURADO</option>
                                                <option value="ALARMAS" {{ old('category', isset($lastEvent) ? $lastEvent->category : '') == 'ALARMAS' ? 'selected' : '' }}>ALARMAS</option>
                                                <option value="INCENDIO" {{ old('category', isset($lastEvent) ? $lastEvent->category : '') == 'INCENDIO' ? 'selected' : '' }}>INCENDIO</option>
                                                <option value="CONTROL DE ACCESO" {{ old('category', isset($lastEvent) ? $lastEvent->category : '') == 'CONTROL DE ACCESO' ? 'selected' : '' }}>CONTROL DE ACCESO</option>
                                                <option value="LEY DE DUCTOS" {{ old('category', isset($lastEvent) ? $lastEvent->category : '') == 'LEY DE DUCTOS' ? 'selected' : '' }}>LEY DE DUCTOS</option>
                                            </select>
                                            @error('category')<div class="text-danger">{{ $message }}</div>@enderror
                                        </div>

                                        <div class="col-md-2">
                                            <label for="sku" class="form-label">SKU <span class="text-muted">(Opcional)</span></label>
                                            <input type="text" id="sku" name="sku" class="form-control"
                                            value="{{ old('sku', isset($lastEvent) ? $lastEvent->sku : '') }}">
                                            @error('sku')<div class="text-danger">{{ $message }}</div>@enderror
                                        </div>
                                    </div>
                                    <!-- Row 3: Pagado, Exponente, Cargo, Cupos, Lugar -->
                                    <div class="row g-3 mb-4">
                                        <div class="col-md-2">
                                            <label for="is_paid" class="form-label">Pagado</label>
                                            <select id="is_paid" name="is_paid" class="form-control" required>
                                                <option value="" disabled {{ old('is_paid', isset($lastEvent) ? $lastEvent->is_paid : '') == '' ? 'selected' : '' }}>Seleccionar</option>
                                                <option value="0" {{ old('is_paid', isset($lastEvent) ? $lastEvent->is_paid : 0) == 0 ? 'selected' : '' }}>No</option>
                                                <option value="1" {{ old('is_paid', isset($lastEvent) ? $lastEvent->is_paid : 0) == 1 ? 'selected' : '' }}>Si</option>
                                            </select>
                                            @error('is_paid')<div class="text-danger">{{ $message }}</div>@enderror
                                        </div>
                                        <div class="col-md-3">
                                            <label for="exponent" class="form-label">Exponente <span class="text-muted">(Opcional)</span></label>
                                            <input type="text" id="exponent" name="exponent" class="form-control"
                                            value="{{ old('exponent', isset($lastEvent) ? $lastEvent->exponent : '') }}">
                                            @error('exponent')<div class="text-danger">{{ $message }}</div>@enderror
                                        </div>
                                        <div class="col-md-3">
                                            <label for="exponent_job" class="form-label">Cargo <span class="text-muted">(Opcional)</span></label>
                                            <input type="text" id="exponent_job" name="exponent_job" class="form-control"
                                            value="{{ old('exponent_job', isset($lastEvent) ? $lastEvent->exponent_job : '') }}">
                                            @error('exponent_job')<div class="text-danger">{{ $message }}</div>@enderror
                                        </div>
                                        <div class="col-md-1">
                                            <label for="quota" class="form-label">Cupos</label>
                                            <input type="number" id="quota" name="quota" class="form-control" required
                                            value="{{ old('quota', isset($lastEvent) ? $lastEvent->quota : '') }}">
                                            @error('quota')<div class="text-danger">{{ $message }}</div>@enderror
                                        </div>
                                        <div class="col-md-2">
                                            <label for="location" class="form-label">Lugar</label>
                                            <select id="location" name="location" class="form-control" required>
                                                <option value="" disabled {{ old('location', isset($lastEvent) ? $lastEvent->location : '') == '' ? 'selected' : '' }}>Seleccionar</option>
                                                <option value="PRESENCIAL" {{ old('location', isset($lastEvent) ? $lastEvent->location : '') == 'PRESENCIAL' ? 'selected' : '' }}>PRESENCIAL</option>
                                                <option value="ONLINE" {{ old('location', isset($lastEvent) ? $lastEvent->location : '') == 'ONLINE' ? 'selected' : '' }}>ONLINE</option>
                                            </select>
                                            @error('location')<div class="text-danger">{{ $message }}</div>@enderror
                                        </div>

                                    </div>

                                    <div class="row g-3 mb-4">
                                        <div class="col-md-4">
                                            <label for="location_gmap" class="form-label">GMap <span class="text-muted">(Opcional)</span></label>
                                            <input type="text" id="location_gmap" name="location_gmap" class="form-control"
                                            value="{{ old('location_gmap', isset($lastEvent) ? $lastEvent->location_gmap : '') }}">
                                            @error('location_gmap')<div class="text-danger">{{ $message }}</div>@enderror
                                        </div>
                                        <div class="col-md-2">
                                            <label for="net_price" class="form-label">Precio Neto <span class="text-muted">(Opcional)</span></label>
                                            <input type="text" id="net_price" name="net_price" class="form-control" placeholder="$1.000"
                                            value="{{ old('net_price', isset($lastEvent) ? $lastEvent->net_price : '') }}">
                                            @error('net_price')<div class="text-danger">{{ $message }}</div>@enderror
                                            <script src="https://cdn.jsdelivr.net/npm/autonumeric@4.6.0/dist/autoNumeric.min.js"></script>
                                            <script>
                                                document.addEventListener('DOMContentLoaded', function () {
                                                    new AutoNumeric('#net_price', {
                                                        currencySymbol: '$',
                                                        decimalCharacter: ',',
                                                        digitGroupSeparator: '.',
                                                        decimalPlaces: 0,
                                                        unformatOnSubmit: true
                                                    });
                                                });
                                            </script>
                                        </div>
                                        <div class="col-md-3">
                                            <label for="external_url" class="form-label">Url Externo <span class="text-muted">(Opcional)</span></label>
                                            <input type="text" id="external_url" name="external_url" class="form-control"
                                                   value="{{ old('external_url', isset($lastEvent) ? $lastEvent->external_url : '') }}">
                                            @error('external_url')<div class="text-danger">{{ $message }}</div>@enderror
                                        </div>
                                    </div>
                                </div>
                                <!-- Footer con botones en extremos -->
                                <div class="card-footer">
                                    <div class="row">
                                        <div class="col text-left">
                                            <a href="{{ route('marketing.events.index') }}" class="btn btn-dark">
                                                <i class="fas fa-undo-alt"></i> Volver atrás
                                            </a>
                                        </div>
                                        <div class="col text-right">
                                            <button type="submit" class="btn btn-primary">
                                                Guardar Evento <i class="far fa-save"></i>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div> <!-- /.tab-content -->
                </div> <!-- /.card-body -->
            </div> <!-- /.card -->


    <!-- Script para actualizar la vista previa de la imagen -->
    <script>
        document.getElementById('image_promo').addEventListener('change', function(e) {
            const [file] = this.files;
            if(file){
                document.getElementById('previewImage').src = URL.createObjectURL(file);
            }
        });
    </script>
@endsection
