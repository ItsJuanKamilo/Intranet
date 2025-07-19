@extends('adminlte::page')

@section('title', 'Detalle del Cliente')

@section('content')
    <div class="row mt-3">
        <div class="col-md-3">
            <div class="card card-navy card-outline">
                <div class="card-header">
                    <h3 class="card-title">Datos del Cliente</h3>
                </div>
                <div class="card-body p-3">

                    {{-- Datos Principales --}}
                    <p class="mb-1"><strong>RUT:</strong> {{ $partner->id }}-{{ $partner->dv }}</p>
                    <p class="mb-1"><strong>ID:</strong> {{ $partner->id }}</p>
                    <p class="mb-1"><strong>CLIENTE:</strong> {{ $partner->name }}</p>
                    <p class="mb-1"><strong>Tipo Socio:</strong></p>
                    @if($partner->is_customer) <p class="mb-1"><i class="fas fa-check text-success"></i> Cliente</p> @endif
                    @if($partner->is_vendor) <p class="mb-1"><i class="fas fa-check text-success"></i> Proveedor</p> @endif
                    @if($partner->is_subdistributor) <p class="mb-1"><i class="fas fa-check text-success"></i> Subdistribuidor</p> @endif
                    @if($partner->is_bdm) <p class="mb-1"><i class="fas fa-check text-success"></i> BDM</p> @endif
                    <p class="mb-1"><strong>GIRO:</strong> {{ $partner->vat_description ?? 'N/A' }}</p>
                    <p class="mb-1"><strong>LISTA:</strong> {{ $partner->customer_pricing_list_name ?? 'N/A' }}</p>
                    <p class="mb-1"><strong>CIUDAD:</strong> {{ ($partner->region ?? 'N/A') }}, {{ ($partner->city ?? 'N/A') }}</p>
                    <p class="mb-1"><strong>EMAIL DIRECCION:</strong>
                        @if($partner->email)
                            <a href="mailto:{{ $partner->email }}">{{ $partner->email }}</a>
                        @else
                            N/A
                        @endif
                    </p>

                    <p class="mb-1"><strong>EMAIL COMPRADOR:</strong>
                        @if($partner->email)
                            <a href="mailto:{{ $partner->email }}">{{ $partner->email }}</a>
                        @else
                            N/A
                        @endif
                    </p>

                    <p class="mb-1"><strong>EMAIL PAGADOR:</strong>
                        @if($partner->payment_email_1)
                            <a href="mailto:{{ $partner->payment_email_1 }}">{{ $partner->payment_email_1 }}</a>
                        @else
                            N/A
                        @endif
                    </p>
                    <p class="mb-1"><strong>TELEFONO:</strong>
                        @if($partner->phone)
                            <a href="https://wa.me/{{ $partner->phone }}" target="_blank">📞 {{ $partner->phone }}</a>
                        @else
                            N/A
                        @endif
                    </p>



                    {{-- Separador --}}
                    <h5 class="mt-3 text-gray">Vendedor</h5>
                    <hr class="my-1">


                    <p class="mb-1"><strong>VENDEDOR:</strong> {{ $partner->seller ?? 'No asignado' }}</p>
                    <p class="mb-1"><strong>TELEFONO:</strong> {{ $partner->phone_seller ?? 'N/A' }}</p>
                    <p class="mb-1"><strong>EMAIL VENDEDOR:</strong> {{ $partner->email_seller ?? 'N/A' }}</p>
                    <p class="mb-1"><strong>SUCURSAL:</strong> {{ $partner->local_seller ?? 'N/A' }}</p>

                    {{-- Separador --}}
                    <h5 class="mt-3 text-gray">Información Financiera</h5>
                    <hr class="my-1">

                    <p class="mb-1"><strong>INSTRUMENTO PAGO:</strong> {{ $partner->customer_payment_method_name ?? 'N/A' }}</p>
                    <p class="mb-1"><strong>CONDICION PAGO:</strong> {{ $partner->customer_payment_term_name ?? 'N/A' }}</p>
                    <p class="mb-1"><strong>LIMITE CREDITO:</strong> ${{ number_format($partner->credit_limit_max, 0, ',', '.') }}</p>
                    <p class="mb-1"><strong>CREDITO USADO:</strong> ${{ number_format($partner->credit_used, 0, ',', '.') }}</p>
                    <p class="mb-1"><strong style="color: red;">CREDITO DISPONIBLE:</strong>
                        ${{ number_format(($partner->credit_limit_max - $partner->credit_used), 0, ',', '.') }}</p>
                    <p class="mb-1"><strong>ESTADO CREDITO:</strong> {{ $partner->credit_status ?? 'N/A' }}</p>
                </div>
            </div>
        </div>

        {{-- Columna Derecha (8 columnas): Card con Tabs --}}
        <div class="col-md-9">
            <div class="card card-navy card-tabs">
                <div class="card-header p-0 pt-1">
                    <div class="d-flex justify-content-between align-items-center">
                        <div class="pl-3">
                            <h3 class="card-title m-0">Secciones</h3>
                        </div>
                        <div>
                            <ul class="nav nav-tabs" id="custom-tabs-two-tab" role="tablist">
                                <li class="nav-item">
                                    <a class="nav-link active" id="facturas-tab" data-toggle="pill" href="#facturas" role="tab"
                                       aria-controls="facturas" aria-selected="true">
                                        Facturas
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link" id="direcciones-tab" data-toggle="pill" href="#direcciones" role="tab"
                                       aria-controls="direcciones" aria-selected="false">
                                        Direcciones
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link" id="compras-tab" data-toggle="pill" href="#compras" role="tab"
                                       aria-controls="compras" aria-selected="false">
                                        Compras
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link" id="deudas-tab" data-toggle="pill" href="#deudas" role="tab"
                                       aria-controls="deudas" aria-selected="false">
                                        Deudas
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link" id="eventos-tab" data-toggle="pill" href="#eventos" role="tab"
                                       aria-controls="eventos" aria-selected="false">
                                        Eventos
                                    </a>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>

                {{-- Contenido de las Tabs --}}
                <div class="card-body">
                    <div class="tab-content" id="custom-tabs-two-tabContent">

                        @php
                            // Configuración para la tabla de facturas
                            $headsInvoices = [
                                'Fact',
                                'Referencia',
                                'Fecha',
                                'COD',
                                'Producto',
                                'Modelo',
                                'Qty',
                                'P/o',
                                'Total'
                            ];
                            $configInvoices = [
                                'processing'  => true,
                                'serverSide'  => true,
                                'columns'     => [
                                    ['data' => 'fact',       'name' => 'fact'],
                                    ['data' => 'reference',  'name' => 'reference'],
                                    ['data' => 'date',       'name' => 'date'],
                                    ['data' => 'cod',        'name' => 'cd'],
                                    ['data' => 'product',    'name' => 'product'],
                                    ['data' => 'model',      'name' => 'model'],
                                    ['data' => 'qty',        'name' => 'qty'],
                                    ['data' => 'p/o',        'name' => 'p/o'],
                                    ['data' => 'total',      'name' => 'total'],
                                ],
                                'language' => [
                                    'url' => 'https://cdn.datatables.net/plug-ins/1.13.6/i18n/es-ES.json',
                                ],
                                'dom' => '<"top d-flex justify-content-between align-items-center"<"dt-length"l><"dt-search"f>>rt<"bottom d-flex justify-content-between"<"dt-info"i><"dt-pagination"p>>',
                                'pageLength' => 10,
                                'lengthMenu' => [10, 25, 50, 100],
                                'order' => [[2, 'desc']], // Ordenar por Fecha (índice 2)
                            ];
                        @endphp

                        {{-- Pestaña Facturas --}}
                        <div class="tab-pane fade show active" id="facturas" role="tabpanel" aria-labelledby="facturas-tab">
                            <x-adminlte-datatable
                                id="invoices-table"
                                :heads="$headsInvoices"
                                :config="$configInvoices"
                                striped
                                hoverable
                                with-buttons
                                class="table table-striped table-hover table-sm m-0"
                            />
                        </div>

                        {{-- Pestaña Direcciones --}}
                        <div class="tab-pane fade" id="direcciones" role="tabpanel" aria-labelledby="direcciones-tab">
                            {{-- Verifica si el cliente tiene direcciones --}}
                            @if($partner->addresses->count() > 0)
                                <div class="table-responsive">
                                    <table class="table table-sm table-bordered table-striped mb-0">
                                        <thead class="thead-light">
                                        <tr>
                                            <th>Id</th>
                                            <th>Dirección</th>
                                            <th>Ciudad</th>
                                            <th>Región</th>
                                            <th>Pais</th>
                                            <th>Facturación</th>
                                            <th>Comentarios</th>
                                            <th>Creado</th>
                                            <th>Actualizado</th>
                                        </tr>
                                        </thead>
                                        <tbody>
                                        @foreach($partner->addresses as $address)
                                            <tr>
                                                <td>{{ $address->id ?? 'N/A' }}</td>
                                                <td>{{ $address->address ?? 'N/A' }}</td>
                                                <td>{{ $address->city ?? 'N/A' }}</td>
                                                <td>{{ $address->province ?? 'N/A' }}</td>
                                                <td>{{ $address->country ?? 'N/A' }}</td>
                                                <td class="text-center">
                                                    @if($address->is_billing)
                                                        <span class="badge badge-success">Sí</span>
                                                    @else
                                                        <span class="badge badge-secondary">No</span>
                                                    @endif
                                                </td>
                                                <td>{{ $address->comments ?? 'N/A' }}</td>
                                                <td>{{ $address->created_at ?? 'N/A' }}</td>
                                                <td>{{ $address->updated_at ?? 'N/A' }}</td>
                                            </tr>
                                        @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            @else
                                <p>No hay direcciones registradas para este cliente.</p>
                            @endif
                        </div>

                        {{-- Pestaña Compras --}}
                        <div class="tab-pane fade" id="compras" role="tabpanel" aria-labelledby="compras-tab">
                            <p>Contenido de compras...</p>
                        </div>

                        {{-- Pestaña Deudas --}}
                        <div class="tab-pane fade" id="deudas" role="tabpanel" aria-labelledby="deudas-tab">
                            <p>Contenido de deudas...</p>
                        </div>

                        {{-- Pestaña Eventos --}}
                        <div class="tab-pane fade" id="eventos" role="tabpanel" aria-labelledby="eventos-tab">
                            <p>Contenido de eventos...</p>
                        </div>

                    </div>
                </div>
            </div>
        </div>

    </div>
@stop
