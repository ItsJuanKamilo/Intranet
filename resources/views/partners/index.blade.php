@extends('adminlte::page')

@section('title', 'Listado de Clientes')

@section('content')
    @include('components._page')

    @php
        $heads = [
            'Cliente',
            'Contacto',
            'Créditos',
            'Tipo',
            'Vendedor',
            ['label' => 'Acciones', 'no-export' => true, 'width' => 5],
        ];

        $config = [
            'ajax'        => route('partners.datatable'),
            'processing'  => true,
            'serverSide'  => true,
            'columns'     => [
                ['data' => 'cliente_info', 'orderable' => false],
                ['data' => 'contacto', 'orderable' => false],
                ['data' => 'creditos', 'orderable' => false],
                ['data' => 'tipo', 'orderable' => false],
                ['data' => 'vendedor', 'orderable' => false],
                [
                    'data' => 'action',
                    'orderable' => false,
                    'searchable' => false,
                    'className' => 'text-center'
                ],
            ],
            'language' => [
                'url' => 'https://cdn.datatables.net/plug-ins/1.13.6/i18n/es-ES.json',
            ],

            'dom' => '<"top d-flex justify-content-between align-items-center">rt<"bottom d-flex justify-content-between"<"dt-info"i><"dt-pagination"p>><"clear">',

            'pageLength'  => 10,
        ];
    @endphp

    <div class="card mt-4 ajax-datatable-compact card-navy card-outline">
        <div class="card-header">
            <h5 class="card-title">Listado de Clientes</h5>
            <div class="card-tools">
                {{-- Botones adicionales si se requieren --}}
            </div>
        </div>

        <!-- Subheader: Buscador personalizado con margen extra -->
        <div class="card-subheader px-3 py-3 border-bottom">
            <div class="row">
                <div class="col-12">
                    <div class="input-group w-100">
                        <input type="text" id="customSearch" class="form-control" placeholder="Buscar clientes por contacto, cliente, vendedor, etc...">
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
                    id="partners-table"
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
@endsection
@section('js')
    <script>
        $(document).ready(function () {
            const parentWindow   = window.parent;
            const parentDoc      = parentWindow.document;
            const $iframeNav     = $(parentDoc).find('ul.navbar-nav.overflow-hidden');
            const $iframeContent = $(parentDoc).find('.tab-content');
            const table          = $('#partners-table').DataTable();  // Cambiado de 'products-table' a 'partners-table'

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
            $(document).on('click', '#partners-table a[data-use-iframe="true"]', function(e) {
                e.preventDefault();

                // 4.1) Extrae datos de la fila
                const $tr = $(this).closest('tr');
                const rowData = table.row($tr).data();
                const partnerId = rowData.id || rowData[0];  // Aseguramos que 'id' sea el identificador único del cliente
                const partnerName = rowData.cliente || rowData[1];  // Extraemos 'cliente' (nombre del cliente)
                const title = `Cliente: ${partnerName}`;  // Título con "Cliente: [name]"

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
                </li>
            `);
                const $newPane = $(`
                <div class="tab-pane fade active show" id="${tabId}" role="tabpanel">
                    <iframe name="${tabId}"
                            src="${url}"
                            style="height: calc(100vh - 120px); width: 100%; border: none;">
                    </iframe>
                </div>
            `);

                $iframeNav.append($newTab);
                $iframeContent.append($newPane);
                parentWindow.$(`#${tabId}-tab`).trigger('click');

                // 4.5) Cierre con la “x”
                $newTab.find('.btn-iframe-close').on('click', () => closeTab(tabId));
            });

            // 5) Al clicar “Clientes” en el sidebar, cierra todas las de edición
            $(parentDoc).find('a[href$="/partners"]').on('click', function() {  // Cambiado de '/products' a '/clients'
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
        $(document).ready(function() {
            var table = $('#partners-table').DataTable();
            $('#customSearch').on('keyup', function() {
                table.search($(this).val()).draw();
            });
        });
    </script>

@endsection
