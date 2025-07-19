@extends('adminlte::page')

@section('title', 'Listado de Productos')

@section('content')
    @include('components._page')

    @php
        $heads = [
            'Producto',
            'Imagen',
            'Precio Neto + IVA',
            'Movimientos',
            'Huechuraba',
            'Exequiel',
            'Mercado Libre',
            ['label' => 'Acciones', 'no-export' => true, 'width' => 5],
        ];

        $config = [
            'ajax' => route('products.datatable'),
            'processing' => true,
            'serverSide' => true,
            'columns' => [
                ['data' => 'product_info', 'name' => 'product_info','searchable' => true, 'orderable' => false],
                ['data' => 'product_image', 'className' => 'text-center', 'orderable' => false, 'searchable' => false, 'width' => '1%'],
                ['data' => 'price_net_iva',    'name'=>'price_net_iva','searchable'=>false, 'orderable'=>true,  'className'=>'text-center','width'=>'10%'],
                ['data' => 'pendiente', 'className' => 'text-center','orderable' => false,'searchable' => false, 'width' => '5%'],
                ['data' => 'stock_hue', 'name' => 'stock_huechuraba_qty','searchable' => false, 'className' => 'text-center', 'width' => '5%', 'orderable' => true],
                ['data' => 'stock_exe', 'name' => 'stock_exequiel_qty','searchable' => false, 'className' => 'text-center', 'width' => '5%', 'orderable' => true],
                ['data' => 'stockmercadolibre', 'name' => 'stock_ml_qty','searchable' => false, 'className' => 'text-center', 'width' => '8%', 'orderable' => true],
                ['data' => 'action', 'orderable' => false, 'searchable' => false, 'className' => 'text-center', 'width' => '5%'],
                ],
            'language' => [
                'url' => 'https://cdn.datatables.net/plug-ins/1.13.6/i18n/es-ES.json',
            ],
            'dom' => '<"top d-flex justify-content-between align-items-center">rt<"bottom d-flex justify-content-between"<"dt-info"i><"dt-pagination"p>><"clear">',
            'pageLength' => 10,
            'order' => [[4, 'desc']],

        ];
    @endphp

    <div class="card mt-4 ajax-datatable-compact card-navy card-outline">
        <div class="card-header">
            <h5 class="card-title">Listado de Productos</h5>
            <div class="card-tools">
            </div>
        </div>

        <!-- Subheader: Buscador personalizado con margen extra -->
        <div class="card-subheader px-3 py-3 border-bottom">
            <div class="row">
                <div class="col-12">
                    <div class="input-group w-100">
                        <input type="text" id="customSearch" class="form-control" placeholder="Busqueda de productos por Codigo 0 Nombre">
                        <div class="input-group-append">
                            <span class="input-group-text"><i class="fas fa-search"></i></span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="card-body p-2">
            <div class="table-responsive p-1">
                <x-adminlte-datatable
                    id="products-table"
                    :heads="$heads"
                    :config="$config"
                    striped
                    hoverable
                    with-buttons
                    class="table table-striped table-hover table-sm m-0"
                    rawColumns="['product_info',sku]"  {{-- Indicamos que la columna `product_info` debe ser tratada como HTML --}}
                />
            </div>
        </div>

        <div class="card-footer">
            @include('components.card-footer')
        </div>
    </div>
@endsection

@section('js')
    <script>
        $(document).ready(function () {
            const parentWindow   = window.parent;
            const parentDoc      = parentWindow.document;
            const $iframeNav     = $(parentDoc).find('ul.navbar-nav.overflow-hidden');
            const $iframeContent = $(parentDoc).find('.tab-content');
            const table          = $('#products-table').DataTable();

            // 1) Función para cerrar pestaña y activar la adyacente
            function closeTab(tabId) {
                const $tabLi = $iframeNav.find(`#tab-${tabId}`);
                const $pane  = $iframeContent.find(`#${tabId}`);

                // Buscamos la pestaña anterior; si no hay, la siguiente
                let $activateLink = null;
                const $prevLi = $tabLi.prev('li.nav-item');
                if ($prevLi.length) {
                    $activateLink = $prevLi.find('a.nav-link');
                } else {
                    const $nextLi = $tabLi.next('li.nav-item');
                    if ($nextLi.length) {
                        $activateLink = $nextLi.find('a.nav-link');
                    }
                }

                // Removemos la pestaña y su panel
                $tabLi.remove();
                $pane.remove();

                // Activamos la pestaña adyacente
                if ($activateLink && $activateLink.length) {
                    parentWindow.$($activateLink).trigger('click');
                }
            }

            // 2) Listener desde el iframe
            parentWindow.addEventListener('message', function(e) {
                if (e.data?.action === 'closeIframeTab') {
                    closeTab(e.data.tabId);
                }
            });

            // 3) Búsqueda en DataTable
            $('#customSearch').on('keyup', () => table.search($('#customSearch').val()).draw());

            // 4) Click en “Editar” (data-use-iframe="true")
            $(document).on('click', '#products-table a[data-use-iframe="true"]', function(e) {
                e.preventDefault();

                // 4.1) Extrae datos de la fila
                const $tr     = $(this).closest('tr');
                const rowData = table.row($tr).data();
                const sku   = rowData.sku   || rowData[0];
                const brand = (rowData.brand && rowData.brand.id) || rowData[3];
                const title = `${sku} – ${brand}`;

                // 4.2) URL
                const url = $(this).attr('href');

                // 4.3) Si ya existe, solo activa
                const $exists = $iframeNav.find(`a[data-url="${url}"]`);
                if ($exists.length) {
                    parentWindow.$($exists).trigger('click');
                    return;
                }

                // 4.4) Crear pestaña
                const tabId = 'iframe-' + Date.now();
                $iframeNav.find('.nav-link.active').removeClass('active');
                $iframeContent.find('.tab-pane.active').removeClass('active show');

                const $newTab = $(`
          <li class="nav-item" id="tab-${tabId}">
            <a class="nav-link active"
               id="${tabId}-tab"
               data-toggle="row"
               href="#${tabId}"
               role="tab"
               data-url="${url}">
              ${title}
            </a>
            <button class="btn-iframe-close text-danger border-0 ml-2" data-target="${tabId}">
              <i class="fas fa-times"></i>
            </button>
          </li>`);
                const $newPane = $(`
          <div class="tab-pane fade active show" id="${tabId}" role="tabpanel">
            <iframe name="${tabId}"
                    src="${url}"
                    style="height: calc(100vh - 120px); width: 100%; border: none;">
            </iframe>
          </div>`);

                $iframeNav.append($newTab);
                $iframeContent.append($newPane);
                parentWindow.$(`#${tabId}-tab`).trigger('click');

                // 4.5) Cierre con la “x”
                $newTab.find('.btn-iframe-close').on('click', () => closeTab(tabId));
            });

            // 5) Al clicar “Productos” en el sidebar, cierra todas las de edición
            $(parentDoc).find('a[href$="/products"]').on('click', function() {
                $iframeNav.find('li.nav-item[id^="tab-"]').each(function() {
                    const tabId = this.id.replace('tab-','');
                    if (tabId !== 'default') closeTab(tabId);
                });
            });

        });
    </script>
@endsection


@section('js')
    <script>
        $(document).ready(function () {
            var table = $('#products-table').DataTable();
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

