<div class="card-header" style="border-bottom: none;">
    <!-- Fila 1: Visible, Fecha Inicio, Fecha Fin, Hora Inicio, Hora Fin, Tipo -->
    <div class="row g-3 mb-4">
        <div class="col-md-1">
            <label for="visible" class="form-label">Visible</label>
            <select id="visible" name="visible" class="form-control" required>
                <option value="" disabled>Seleccionar</option>
                <option value="1" {{ old('visible', $event->visible) == '1' ? 'selected' : '' }}>Si</option>
                <option value="0" {{ old('visible', $event->visible) == '0' ? 'selected' : '' }}>No</option>
            </select>
            @error('visible')<div class="text-danger">{{ $message }}</div>@enderror
        </div>
        <div class="col-md-1">
            <label for="active" class="form-label">Activo</label>
            <select id="active" name="active" class="form-control" required>
                <option value="" disabled>Seleccionar</option>
                <option value="1" {{ old('active', $event->active) == '1' ? 'selected' : '' }}>Si</option>
                <option value="0" {{ old('active', $event->active) == '0' ? 'selected' : '' }}>No</option>
            </select>
            @error('active')<div class="text-danger">{{ $message }}</div>@enderror
        </div>
        <div class="col-md-2">
            <label for="date_start" class="form-label">Fecha Inicio</label>
            <input type="date" id="date_start" name="date_start" class="form-control" value="{{ old('date_start', $event->date_start) }}" required>
            @error('date_start')<div class="text-danger">{{ $message }}</div>@enderror
        </div>
        <div class="col-md-2">
            <label for="date_end" class="form-label">Fecha Fin</label>
            <input type="date" id="date_end" name="date_end" class="form-control" value="{{ old('date_end', $event->date_end) }}" required>
            @error('date_end')<div class="text-danger">{{ $message }}</div>@enderror
        </div>
        <div class="col-md-2">
            <label for="date_time_start" class="form-label">Hora Inicio</label>
            <input type="time" id="date_time_start" name="date_time_start" class="form-control" value="{{ old('date_time_start', $event->date_time_start) }}" required>
            @error('date_time_start')<div class="text-danger">{{ $message }}</div>@enderror
        </div>
        <div class="col-md-2">
            <label for="date_time_end" class="form-label">Hora Fin</label>
            <input type="time" id="date_time_end" name="date_time_end" class="form-control" value="{{ old('date_time_end', $event->date_time_end) }}" required>
            @error('date_time_end')<div class="text-danger">{{ $message }}</div>@enderror
        </div>
        <div class="col-md-2">
            <label for="type" class="form-label">Tipo</label>
            <select id="type" name="type" class="form-control" required>
                <option value="" disabled>Seleccionar</option>
                <option value="CERTIFICACION" {{ old('type', $event->type) == 'CERTIFICACION' ? 'selected' : '' }}>CERTIFICACION</option>
                <option value="TALLER" {{ old('type', $event->type) == 'TALLER' ? 'selected' : '' }}>TALLER</option>
                <option value="CAPACITACION" {{ old('type', $event->type) == 'CAPACITACION' ? 'selected' : '' }}>CAPACITACION</option>
                <option value="DESAYUNO" {{ old('type', $event->type) == 'DESAYUNO' ? 'selected' : '' }}>DESAYUNO</option>
                <option value="PRESENTACION" {{ old('type', $event->type) == 'PRESENTACION' ? 'selected' : '' }}>PRESENTACION</option>
                <option value="WEBINAR" {{ old('type', $event->type) == 'WEBINAR' ? 'selected' : '' }}>WEBINAR</option>
                <option value="LANZAMIENTO" {{ old('type', $event->type) == 'LANZAMIENTO' ? 'selected' : '' }}>LANZAMIENTO</option>
            </select>
            @error('type')<div class="text-danger">{{ $message }}</div>@enderror
        </div>
    </div>
    <div class="row g-3 mb-4">
        <div class="col-md-12">
            <label for="title" class="form-label">Título</label>
            <input type="text" id="title" name="title" class="form-control" value="{{ old('title', $event->title) }}" required>
            @error('title')<div class="text-danger">{{ $message }}</div>@enderror
        </div>
    </div>
    <!-- Fila 2: Título, Marca, Categoría, SKU, Pagado -->
    <div class="row g-3 mb-4">
        <div class="col-md-2">
            <label for="brand" class="form-label">Marca</label>
            <input type="text" id="brand" name="brand" class="form-control" value="{{ old('brand', $event->brand) }}" required>
            @error('brand')<div class="text-danger">{{ $message }}</div>@enderror
        </div>
        <div class="col-md-2">
            <label for="category" class="form-label">Rubro</label>
            <select id="category" name="category" class="form-control" required>
                <option value="" disabled>Seleccionar</option>
                <option value="CCTV" {{ old('category', $event->category) == 'CCTV' ? 'selected' : '' }}>CCTV</option>
                <option value="CABLEADO ESTRUCTURADO" {{ old('category', $event->category) == 'CABLEADO ESTRUCTURADO' ? 'selected' : '' }}>CABLEADO ESTRUCTURADO</option>
                <option value="ALARMAS" {{ old('category', $event->category) == 'ALARMAS' ? 'selected' : '' }}>ALARMAS</option>
                <option value="INCENDIO" {{ old('category', $event->category) == 'INCENDIO' ? 'selected' : '' }}>INCENDIO</option>
                <option value="CONTROL DE ACCESO" {{ old('category', $event->category) == 'CONTROL DE ACCESO' ? 'selected' : '' }}>CONTROL DE ACCESO</option>
                <option value="LEY DE DUCTOS" {{ old('category', $event->category) == 'LEY DE DUCTOS' ? 'selected' : '' }}>LEY DE DUCTOS</option>
            </select>
            @error('category')<div class="text-danger">{{ $message }}</div>@enderror
        </div>
        <div class="col-md-2">
            <label for="sku" class="form-label">SKU <span class="text-muted">(Opcional)</span></label>
            <input type="text" id="sku" name="sku" class="form-control" value="{{ old('sku', $event->sku) }}">
            @error('sku')<div class="text-danger">{{ $message }}</div>@enderror
        </div>
        <div class="col-md-2">
            <label for="is_paid" class="form-label">Pagado</label>
            <select id="is_paid" name="is_paid" class="form-control" required>
                <option value="" disabled>Seleccionar</option>
                <option value="1" {{ old('is_paid', $event->is_paid) == '1' ? 'selected' : '' }}>Si</option>
                <option value="0" {{ old('is_paid', $event->is_paid) == '0' ? 'selected' : '' }}>No</option>
            </select>
            @error('is_paid')<div class="text-danger">{{ $message }}</div>@enderror
        </div>
        <div class="col-md-3">
            <label for="net_price" class="form-label">Precio Neto <span class="text-muted">(Opcional)</span></label>
            <input type="text" id="net_price" name="net_price" class="form-control" value="{{ old('net_price', $event->net_price) }}" placeholder="$1.000">
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
        <div class="col-md-1">
            <label for="quota" class="form-label">Cupos</label>
            <input type="number" id="quota" name="quota" class="form-control" value="{{ old('quota', $event->quota) }}" required>
            @error('quota')<div class="text-danger">{{ $message }}</div>@enderror
        </div>
    </div>
    <!-- Fila 3: Exponent, Exponent Job, Quota, Location, Location Gmap -->
    <div class="row g-3 mb-4">
        <div class="col-md-3">
            <label for="exponent" class="form-label">Exponente <span class="text-muted">(Opcional)</span></label>
            <input type="text" id="exponent" name="exponent" class="form-control" value="{{ old('exponent', $event->exponent) }}">
            @error('exponent')<div class="text-danger">{{ $message }}</div>@enderror
        </div>
        <div class="col-md-3">
            <label for="exponent_job" class="form-label">Cargo <span class="text-muted">(Opcional)</span></label>
            <input type="text" id="exponent_job" name="exponent_job" class="form-control" value="{{ old('exponent_job', $event->exponent_job) }}">
            @error('exponent_job')<div class="text-danger">{{ $message }}</div>@enderror
        </div>
        <div class="col-md-2">
            <label for="location" class="form-label">Lugar</label>
            <select id="location" name="location" class="form-control" required>
                <option value="" disabled>Seleccionar</option>
                <option value="PRESENCIAL" {{ old('location', $event->location) == 'PRESENCIAL' ? 'selected' : '' }}>PRESENCIAL</option>
                <option value="ONLINE" {{ old('location', $event->location) == 'ONLINE' ? 'selected' : '' }}>ONLINE</option>
            </select>
            @error('location')<div class="text-danger">{{ $message }}</div>@enderror
        </div>
    </div>
    <!-- Fila 4: Net Price, Url Externo y Descripción -->
    <div class="row g-3 mb-4">
        <div class="col-md-8">
            <label for="location_gmap" class="form-label">GMap <span class="text-muted">(Opcional)</span></label>
            <input type="text" id="location_gmap" name="location_gmap" class="form-control" value="{{ old('location_gmap', $event->location_gmap) }}">
            @error('location_gmap')<div class="text-danger">{{ $message }}</div>@enderror
        </div>
        <div class="col-md-4">
            <label for="external_url" class="form-label">Url Externo <span class="text-muted">(Opcional)</span></label>
            <input type="text" id="external_url" name="external_url" class="form-control" value="{{ old('external_url', $event->external_url) }}">
            @error('external_url')<div class="text-danger">{{ $message }}</div>@enderror
        </div>
        <div class="col-md-12 mt-4">
            <div class="card card-outline card-navy ">
                <div class="card-header p-2">
                    <h3 class="card-title">Editar Descripción</h3>
                </div>
                <div class="card-body p-0">
                    <textarea id="summernote" name="description">{{ old('description', $event->description) }}</textarea>
                    @error('description')
                    <div class="text-danger">{{ $message }}</div>
                    @enderror
                </div>
            </div>
        </div>

        @push('js')
            <script>
                $(document).ready(function() {
                    // Inicializa Summernote
                    $('#summernote').summernote({
                        height: 150, // Ajusta la altura según lo necesites
                        toolbar: [
                            ['style', ['style']],
                            ['font', ['bold', 'underline', 'clear']],
                            ['color', ['color']],
                            ['para', ['ul', 'ol', 'paragraph']],
                            ['insert', ['link', 'picture']],
                            ['view', ['fullscreen', 'codeview', 'help']]
                        ]
                    });

                    // Al enviar el formulario, actualiza el textarea con el contenido de Summernote
                    $('#form-event').on('submit', function() {
                        var contenido = $('#summernote').summernote('code');
                        console.log("Contenido de Summernote:", contenido); // Puedes usarlo para debug
                        $('#summernote').val(contenido);
                    });
                });
            </script>
        @endpush
    </div>
</div>
