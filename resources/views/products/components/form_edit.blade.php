<div class="card-header" style="border-bottom: none;">
    <!-- Fila 1: SKU, Nombre del Producto -->
    <div class="row g-3 mb-4">
        <div class="col-md-2">
            <div class="form-group">
                <label for="sku" class="form-label">SKU</label>
                <input type="text" name="sku" id="sku" class="form-control" value="{{ old('sku', $product->sku) }}" readonly>
                @error('sku')<div class="text-danger">{{ $message }}</div>@enderror
            </div>
        </div>
        <div class="col-md-7">
            <div class="form-group">
                <label for="name" class="form-label">Nombre del Producto</label>
                <input type="text" name="name" id="name" class="form-control" value="{{ old('name', $product->name) }}" readonly>
                @error('name')<div class="text-danger">{{ $message }}</div>@enderror
            </div>
        </div>
    </div>

    <!-- Fila 2: Modelo, Marca, Índice -->
    <div class="row g-3 mb-4">
        <div class="col-md-3 ">
            <div class="form-group">
                <label for="model" class="form-label">Modelo</label>
                <input type="text" name="model" id="model" class="form-control" value="{{ old('model', $product->model) }}" readonly>
                @error('model')<div class="text-danger">{{ $message }}</div>@enderror
            </div>
        </div>
        <div class="col-md-3">
            <div class="form-group">
                <label for="brand" class="form-label">Marca</label>
                <input type="text" name="brand" id="brand" class="form-control" value="{{ old('brand', $product->brand ?? 'N/A') }}" readonly>
                @error('brand')<div class="text-danger">{{ $message }}</div>@enderror
            </div>
        </div>
        <div class="col-md-1">
            <div class="form-group">
                <label for="index" class="form-label">Índice</label>
                <input type="text" name="index" id="index" class="form-control" value="{{ old('index', $product->index) }}" readonly>
                @error('index')<div class="text-danger">{{ $message }}</div>@enderror
            </div>
        </div>
    </div>
    <!-- Fila 3: Editor de Descripción -->
    <div class="row g-3 mb-4">
        <div class="col-md-12">
            <div class="card card-outline card-navy">
                <div class="card-header p-2">
                    <h3 class="card-title">Editar Descripción</h3>
                </div>
                <div class="card-body p-2">
                    <textarea id="summernote" name="description">{{ old('description', $product->description) }}</textarea>
                    @error('description')
                    <div class="text-danger">{{ $message }}</div>
                    @enderror
                </div>
            </div>
        </div>
    </div>

</div>
