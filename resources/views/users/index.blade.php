@php use Illuminate\Support\Facades\Auth; @endphp
@extends('adminlte::page')

@section('title', 'Lista de Usuarios')

@section('content')
    @include('components._page')

    <div class="card mt-4 ajax-datatable-compact card-navy card-outline">
        <div class="card-header">
            <h5 class="card-title">Listado de Usuarios</h5>
            <div class="card-tools">
                @can('usuarios_editar')
                <a href="{{ route('admin.users.download.excel') }}" class="btn btn-sm btn-dark">
                    <i class="fas fa-file-excel"></i> Descargar Listado
                </a>
                <a href="{{ route('admin.users.create') }}" class="btn btn-sm btn-dark">
                    <i class="fas fa-user-plus me-1"></i> Nuevo Usuario
                </a>
                @endcan
                <button type="button" class="btn btn-tool" data-card-widget="collapse" title="Minimizar">
                    <i class="fas fa-minus"></i>
                </button>
            </div>
        </div>

        <!-- Contenedor para búsqueda personalizada -->
        <div class="pt-2">
            <div id="custom-search-container"></div>
        </div>

        <div class="card-body p-2">
            <div class="table-responsive p-1">
                @php
                    // Se muestran siempre la columna ID/RUT al inicio y Acciones al final.
                    $heads = [
                        'ID/RUT',
                        'Nombre',
                        'Departamento',
                        'Cargo',
                        'Anexo',
                        'Teléfono',
                        'Correo',
                        'Sucursal',
                        'Antigüedad',
                        'Cumpleaños'
                    ];

                    if(Auth::getUser()->can('usuarios_editar')){
                        $heads[] = ['label' => 'Acciones', 'no-export' => true, 'width' => 5];
                    }

                    $config = [
                        'paging'      => true,
                        'searching'   => true,
                        'ordering'    => true,
                        'language'    => [
                            'url' => 'https://cdn.datatables.net/plug-ins/1.13.6/i18n/es-ES.json',
                        ],
                        'dom'         => '<"top d-flex justify-content-between align-items-center"<"dt-export"B><"dt-search"f>>rt<"bottom d-flex justify-content-between"<"dt-info"i><"dt-pagination"p>><"clear">',
                        'pageLength'  => 100,
                        'order'       => [[1, 'asc']],
                    ];
                @endphp

                    <!-- Componente x-adminlte-datatable para el listado de usuarios -->
                <x-adminlte-datatable
                    id="users-table"
                    :heads="$heads"
                    :config="$config"
                    striped
                    hoverable
                    with-buttons
                    class="table table-striped table-hover table-sm m-0"
                >
                    @foreach($users as $user)
                        <tr>
                            <!-- Columna ID/RUT -->
                            <td class="text-right">{{ $user->rut }}</td>

                            <!-- Nombre -->
                            <td class="align-middle">
                                {{ $user->name ?? '(Sin datos)' }} {{ $user->surname_1 ?? '' }}
                            </td>

                            <!-- Departamento (Roles) -->
                            <td class="{{ !$user->roles->isNotEmpty() ? 'text-danger' : '' }}">
                                {{ $user->roles->isNotEmpty() ? $user->roles->pluck('name')->join(', ') : 'N/A' }}
                            </td>

                            <!-- Cargo -->
                            <td class="{{ !$user->role_description ? 'text-danger' : '' }}">
                                {{ $user->role_description ?? 'N/A' }}
                            </td>

                            <!-- Anexo -->
                            <td class="{{ !$user->annex ? 'text-danger' : '' }}">
                                {{ $user->annex ?? 'N/A' }}
                            </td>

                            <!-- Teléfono -->
                            <td class="{{ !$user->phone ? 'text-danger' : '' }}">
                                @if($user->phone)
                                    @php
                                        $visiblePhone = (substr($user->phone, 0, 2) === '56') ? substr($user->phone, 2) : $user->phone;
                                    @endphp
                                    <a href="https://wa.me/{{ $user->phone }}" target="_blank">
                                        {{ $visiblePhone }}
                                    </a>
                                @else
                                    N/A
                                @endif
                            </td>

                            <!-- Correo -->
                            <td class="{{ !$user->email ? 'text-danger' : '' }}">
                                {{ $user->email ?? 'N/A' }}
                            </td>

                            <!-- Sucursal -->
                            <td class="{{ !$user->local ? 'text-danger' : '' }}">
                                {{ $user->local ?? 'N/A' }}
                            </td>

                            <!-- Antigüedad -->
                            <td class="{{ !$user->date_admission ? 'text-danger' : '' }}" data-fecha="{{ $user->date_admission }}">
                            </td>

                            <!-- Cumpleaños -->
                            <td class="{{ !$user->date_birthday ? 'text-danger' : '' }}" data-birthday="{{ $user->date_birthday }}">
                            </td>


                            @can('usuarios_editar')
                            <!-- Acciones -->
                            <td class="text-center">
                                @if(!$user->trashed())
                                    <!-- Usuario activo: Editar y Desactivar -->
                                    <a href="{{ route('admin.users.edit', $user->rut) }}" class="btn btn-sm btn-primary btn-table" title="Editar">
                                        <i class="fa fa-edit"></i>
                                    </a>
                                    <form action="{{ route('admin.users.destroy', $user->rut) }}" method="POST" class="d-inline" onsubmit="confirmAction(event, this)">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-warning btn-table" title="Desactivar">
                                            <i class="fa fa-user-slash"></i>
                                        </button>
                                    </form>
                                @else
                                    <!-- Usuario inactivo: Reactivar y Eliminar Permanentemente -->
                                    <form action="{{ route('admin.users.restore', $user->rut) }}" method="POST" class="d-inline" onsubmit="confirmAction(event, this, 'activar')">
                                        @csrf
                                        <button type="submit" class="btn btn-sm btn-success btn-table" title="Reactivar">
                                            <i class="fa fa-user-check"></i>
                                        </button>
                                    </form>

                                    <form action="{{ route('admin.users.destroy', $user->rut) }}" method="POST" class="d-inline" onsubmit="confirmDelete(event, this)">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-danger btn-table" title="Eliminar Permanentemente">
                                            <i class="fa fa-trash-alt"></i>
                                        </button>
                                    </form>
                                @endif
                            </td>
                            @endcan
                        </tr>
                    @endforeach
                </x-adminlte-datatable>
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
            // Obtenemos la instancia del DataTable generado por el componente
            var table = $('#users-table').DataTable();
            table.on('processing.dt', function (e, settings, processing) {
                if (processing) {
                    $('#custom-loading').fadeIn();
                } else {
                    $('#custom-loading').fadeOut();
                }
            });

            // Mover el buscador al contenedor personalizado
            const searchContainer = $('#custom-search-container');
            searchContainer.append($('.dataTables_filter'));
            searchContainer.find('label').contents().filter(function() {
                return this.nodeType === 3;
            }).remove();
            const searchInput = searchContainer.find('input');
            searchInput.attr('placeholder', 'Buscar');
            searchInput.on('input keyup', function () {
                table.search($(this).val()).draw();
            });

            // Procesar las columnas para Antigüedad y Cumpleaños
            // Luego de llamar a processColumns(), actualizamos el dato interno
            function updateExportData() {
                var table = $('#users-table').DataTable();
                table.rows().every(function () {
                    var rowNode = this.node();
                    // Se obtiene el texto actualizado (ya procesado) de cada celda
                    var seniority = $(rowNode).find('td[data-fecha]').text();
                    var birthday = $(rowNode).find('td[data-birthday]').text();
                    // Actualizamos la información interna para que exporte el valor visible
                    this.cell(this.index(), 8).data(seniority);
                    this.cell(this.index(), 9).data(birthday);
                });
            }

// Llamamos a la función después de procesar las columnas
            processColumns();
            updateExportData();

        });

        function processColumns() {
            document.querySelectorAll("td[data-fecha]").forEach(el => {
                const date = el.dataset.fecha;
                el.textContent = calculateSeniority(date);
            });
            document.querySelectorAll("td[data-birthday]").forEach(el => {
                const birthday = el.dataset.birthday;
                el.textContent = calculateBirthday(birthday);
            });
        }

        function calculateSeniority(admissionDate) {
            if (!admissionDate || admissionDate === "null") return "(Sin fecha)";
            const startDate = new Date(admissionDate);
            if (isNaN(startDate.getTime())) return "(Sin fecha)";
            const now = new Date();
            let years = now.getFullYear() - startDate.getFullYear();
            let months = now.getMonth() - startDate.getMonth();
            let days = now.getDate() - startDate.getDate();
            if (days < 0) {
                months -= 1;
                const lastMonth = new Date(now.getFullYear(), now.getMonth(), 0);
                days += lastMonth.getDate();
            }
            if (months < 0) {
                years -= 1;
                months += 12;
            }
            if (years === 0 && months === 0) {
                return `${days} días`;
            } else if (years === 0) {
                return `${months} ${months === 1 ? "mes" : "meses"}`;
            } else if (months === 0) {
                return `${years} ${years === 1 ? "año" : "años"}`;
            }
            return `${years} ${years === 1 ? "año" : "años"}, ${months} ${months === 1 ? "mes" : "meses"}`;
        }

        function calculateBirthday(birthday) {
            if (!birthday || birthday === "null") return "(Sin fecha)";
            const parts = birthday.split("-");
            if (parts.length !== 3) return "(Sin fecha)";
            const month = parseInt(parts[1], 10);
            const day = parseInt(parts[2], 10);
            const currentYear = new Date().getFullYear();
            const birthdayThisYear = new Date(currentYear, month - 1, day);
            if (isNaN(birthdayThisYear.getTime())) return "(Sin fecha)";
            const dayOfWeek = birthdayThisYear.toLocaleDateString("es-ES", { weekday: 'long' });
            const monthName = birthdayThisYear.toLocaleDateString("es-ES", { month: 'long' });
            const zodiacSign = getZodiacSign(birthdayThisYear.getDate(), birthdayThisYear.getMonth() + 1);
            return `${capitalizeFirstLetter(dayOfWeek)} ${birthdayThisYear.getDate()} de ${capitalizeFirstLetter(monthName)} - ${zodiacSign}`;
        }

        function getZodiacSign(day, month) {
            const zodiacSigns = [
                { name: "Capricornio", start: [12, 22], end: [1, 19] },
                { name: "Acuario",    start: [1, 20],  end: [2, 18] },
                { name: "Piscis",     start: [2, 19],  end: [3, 20] },
                { name: "Aries",      start: [3, 21],  end: [4, 19] },
                { name: "Tauro",      start: [4, 20],  end: [5, 20] },
                { name: "Géminis",    start: [5, 21],  end: [6, 20] },
                { name: "Cáncer",     start: [6, 21],  end: [7, 22] },
                { name: "Leo",        start: [7, 23],  end: [8, 22] },
                { name: "Virgo",      start: [8, 23],  end: [9, 22] },
                { name: "Libra",      start: [9, 23],  end: [10, 22] },
                { name: "Escorpio",   start: [10, 23], end: [11, 21] },
                { name: "Sagitario",  start: [11, 22], end: [12, 21] },
                { name: "Capricornio",start: [12, 22], end: [1, 19] }
            ];
            for (const sign of zodiacSigns) {
                const [startMonth, startDay] = sign.start;
                const [endMonth, endDay] = sign.end;
                if ((month === startMonth && day >= startDay) || (month === endMonth && day <= endDay)) {
                    return sign.name;
                }
            }
            return "Desconocido";
        }

        function capitalizeFirstLetter(string) {
            return string.charAt(0).toUpperCase() + string.slice(1);
        }
    </script>
@endsection
