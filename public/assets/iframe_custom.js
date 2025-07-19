// iframe.js (Template para uso reutilizable)
function initializeIframe({
                              tableId,               // ID de la tabla de DataTable
                              searchInputId,         // ID del input de búsqueda
                              iframeNavSelector,     // Selector del contenedor de las pestañas
                              iframeContentSelector, // Selector del contenedor de contenido de las pestañas
                              editUrlSelector,       // Selector para el enlace de edición (botón o enlace)
                              closeTabSelector,      // Selector del botón para cerrar pestañas
                              closeButtonClass       // Clase del botón de cerrar
                          }) {
    $(document).ready(function () {
        const parentWindow   = window.parent;
        const parentDoc      = parentWindow.document;
        const $iframeNav     = $(parentDoc).find(iframeNavSelector);
        const $iframeContent = $(parentDoc).find(iframeContentSelector);
        const table          = $(tableId).DataTable();  // Dynamic table ID

        // 1) Función para cerrar pestaña y activar la adyacente
        function closeTab(tabId) {
            const $tabLi = $iframeNav.find(`#tab-${tabId}`);
            const $pane  = $iframeContent.find(`#${tabId}`);

            // Buscar la pestaña anterior
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

            // Remover la pestaña y su panel
            $tabLi.remove();
            $pane.remove();

            // Activar la pestaña adyacente
            if ($activateLink && $activateLink.length) {
                parentWindow.$($activateLink).trigger('click');
            }
        }

        // 2) Listener desde el iframe para cerrar las pestañas
        parentWindow.addEventListener('message', function(e) {
            if (e.data?.action === 'closeIframeTab') {
                closeTab(e.data.tabId);
            }
        });

        // 3) Búsqueda en DataTable
        $(searchInputId).on('keyup', () => table.search($(searchInputId).val()).draw());

        // 4) Click en “Editar” (data-use-iframe="true")
        $(document).on('click', `${tableId} a[data-use-iframe="true"]`, function(e) {
            e.preventDefault();

            // 4.1) Extraer datos de la fila
            const $tr = $(this).closest('tr');
            const rowData = table.row($tr).data();
            const id = rowData.id || rowData[0];  // Asegurarse que 'id' sea el identificador único
            const name = rowData.name || rowData[1];  // Extraer el nombre
            const title = `Nombre: ${name}`;  // Título con el nombre del cliente

            // 4.2) URL
            const url = $(this).attr('href');

            // 4.3) Si ya existe la pestaña, activarla
            const $exists = $iframeNav.find(`a[data-url="${url}"]`);
            if ($exists.length) {
                parentWindow.$($exists).trigger('click');
                return;
            }

            // 4.4) Crear nueva pestaña
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
                    <button class="${closeButtonClass} text-danger border-0 ml-2" data-target="${tabId}">
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

            // 4.5) Cierre con el botón "x"
            $newTab.find(`.${closeButtonClass}`).on('click', () => closeTab(tabId));
        });

        // 5) Al clicar “Productos” o “Clientes” en el sidebar, cierra todas las de edición
        $(parentDoc).find('a[href$="/products"], a[href$="/clients"]').on('click', function() {
            $iframeNav.find('li.nav-item[id^="tab-"]').each(function() {
                const tabId = this.id.replace('tab-','');
                if (tabId !== 'default') closeTab(tabId);
            });
        });
    });
}

