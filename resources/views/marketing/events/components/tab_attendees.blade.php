<div class="tab-pane fade show active" id="asistencia" role="tabpanel" aria-labelledby="asistencia-tab">
    <div class="card card-navy card-outline mt-3 ajax-datatable-compact">
        <div class="card-header text-center">
            <h3 class="card-title mb-0">Asistentes</h3>
        </div>
        <div class="row g-3 mt-1">
            <!-- KPI: Confirmados -->
            <div class="col-md-2 mt-3 ml-3">
                <div class="info-box">
            <span class="info-box-icon bg-success elevation-1">
                <i class="fas fa-check-circle"></i>
            </span>
                    <div class="info-box-content">
                        <span class="info-box-text">Confirmados</span>
                        <span class="info-box-number">
                    {{ $emailConfirmedCount ?? 0 }} / {{ $totalAttendees ?? 0 }}
                </span>
                    </div>
                </div>
            </div>

            <!-- KPI: Encuestas -->
            <div class="col-md-2 mt-3">
                <div class="info-box">
            <span class="info-box-icon bg-info elevation-1">
                <i class="fas fa-poll-h"></i>
            </span>
                    <div class="info-box-content">
                        <span class="info-box-text">Encuestas</span>
                        <span class="info-box-number">
                    {{ $surveyCount ?? 0 }} / {{ $totalAttendees ?? 0 }}
                </span>
                    </div>
                </div>
            </div>

            <!-- KPI: Recordatorios -->
            <div class="col-md-2 mt-3">
                <div class="info-box">
            <span class="info-box-icon bg-warning elevation-1">
                <i class="fas fa-bell"></i>
            </span>
                    <div class="info-box-content">
                        <span class="info-box-text">Recordatorios</span>
                        <span class="info-box-number">
                    {{ $reminderCount ?? 0 }} / {{ $totalAttendees ?? 0 }}
                </span>
                    </div>
                </div>
            </div>

            <!-- KPI: Asistidos -->
            <div class="col-md-2 mt-3">
                <div class="info-box">
            <span class="info-box-icon bg-primary elevation-1">
                <i class="fas fa-users"></i>
            </span>
                    <div class="info-box-content">
                        <span class="info-box-text">Asistieron</span>
                        <span class="info-box-number">
                    {{ $confirmedCount ?? 0 }} / {{ $totalAttendees ?? 0 }}
                </span>
                    </div>
                </div>
            </div>
        </div>

        <div class="card-body">
            @php
                // Encabezados para la tabla de asistentes con anchos
                $headsAttendees = [
                    ['label' => 'ID', 'width' => 5],
                    ['label' => 'Rut', 'width' => 10],
                    ['label' => 'Nombre', 'width' => 15],
                    ['label' => 'Email', 'width' => 15],
                    ['label' => 'Teléfono', 'width' => 10],
                    ['label' => 'Rut/Empresa', 'width' => 8],
                    ['label' => 'Empresa', 'width' => 15],
                    ['label' => 'Vendedor', 'width' => 15], // Nueva columna para el vendedor
                    ['label' => 'Confirmados', 'width' => 5],
                    ['label' => 'Encuesta', 'width' => 5],
                    ['label' => 'Recordatorio', 'width' => 5],
                    ['label' => 'Asistió', 'width' => 5],
                    ['label' => 'Acciones', 'no-export' => true, 'width' => 5],
                ];

            // Configuración de DataTables para asistentes
                $configAttendees = [
                    'paging'     => false,
                    'searching'  => true,
                    'ordering'   => true,
                    'columnDefs' => [
                        // Desactivar el orden en la columna de Acciones
                        ['orderable' => false, 'targets' => 10],
                    ],
                    'language'   => [
                        'url' => 'https://cdn.datatables.net/plug-ins/1.13.6/i18n/es-ES.json',
                    ],
                    'dom' => '<"top d-flex justify-content-between align-items-center"<"dt-export"B><"dt-search"f>>rt<"d-flex justify-content-end"p>',
                    'pageLength' => 100,
                    'order'      => [[0, 'desc']],

                ];
            @endphp

            <x-adminlte-datatable
                id="attendees-table"
                :heads="$headsAttendees"
                :config="$configAttendees"
                striped
                hoverable
                with-buttons
                class="table table-striped table-hover table-sm m-0 "
            >
                @forelse ($attendees as $attendee)
                    <tr>
                        <td>{{ $attendee->id }}</td>
                        <td class="text-nowrap">{{ $attendee->vat }}</td>
                        <td class="text-nowrap">{{ $attendee->name }}</td>
                        <td>{{ $attendee->email }}</td>
                        <td>{{ $attendee->phone ?? '-' }}</td>
                        <td>{{ $attendee->tax_vat }}</td>
                        <td>{{ Str::limit($attendee->tax_name, 40) }}</td>
                        <td>{{ $attendee->vendor_name ?? 'No asignado' }}</td>


                        <!-- Columna Confirmado -->
                        <td class="text-center">
                            <span class="d-none export-text">
                                {{ $attendee->email_reminder_confirm_at ? 'Sí' : 'No' }}
                            </span>
                            {!! $attendee->email_reminder_confirm_at
                                ? '<i class="fas fa-check text-success"></i>'
                                : '<i class="fas fa-times text-danger"></i>' !!}
                        </td>
                        <!-- Columna Encuesta -->
                        <td class="text-center">
                            <span class="d-none export-text">
                                {{ $attendee->email_survey ? 'Sí' : 'No' }}
                            </span>
                            {!! $attendee->email_survey
                                ? '<i class="fas fa-check text-success"></i>'
                                : '<i class="fas fa-times text-danger"></i>' !!}
                        </td>
                        <!-- Columna Recordatorio -->
                        <td class="text-center">
                            <span class="d-none export-text">
                                {{ $attendee->email_reminder ? 'Sí' : 'No' }}
                            </span>
                            {!! $attendee->email_reminder
                                ? '<i class="fas fa-check text-success"></i>'
                                : '<i class="fas fa-times text-danger"></i>' !!}
                        </td>
                        <!-- Columna Asistió -->
                        <td class="text-center">
                            <span class="d-none export-text">
                                {{ $attendee->confirmed ? 'Sí' : 'No' }}
                            </span>
                            {!! $attendee->confirmed
                                ? '<i class="fas fa-check text-success"></i>'
                                : '<i class="fas fa-times text-danger"></i>' !!}
                        </td>
                        <!-- Columna Acciones -->
                        <td class="text-center text-nowrap">
                            <!-- Ver QR -->
                            @if($attendee->qr_image_url)
                                <a href="{{ $attendee->qr_image_url }}" target="_blank" class="btn btn-sm btn-secondary" title="Ver QR">
                                    <i class="fas fa-qrcode"></i>
                                </a>
                            @else
                                <span class="text-danger">N/A</span>
                            @endif
                            <!-- Sobre encuestas -->
                            @if($attendee->email_survey_url)
                                <a href="{{ $attendee->email_survey_url }}" target="_blank" class="btn btn-sm btn-dark" title="Ver Encuesta">
                                    <i class="fas fa-poll-h"></i>
                                </a>
                            @else
                                <span class="text-danger">N/A</span>
                            @endif

                            <!-- Botón enviar QR -->
                            <form id="sendQrForm" action="{{ route('marketing.events.attendees.sendqr', ['event' => $event->id, 'attendee' => $attendee->id]) }}" method="POST" class="d-inline">
                                @csrf
                                <button type="button" class="btn btn-sm btn-primary" title="Enviar EMAIL QR" onclick="sendQrToEmail({{$attendee->id}})">
                                    <i class="fas fa-envelope-open-text"></i>
                                </button>
                            </form>

                            @can('marketing_editar')
                                <form action="{{ route('marketing.events.attendees.confirm', ['event' => $event->id, 'attendee' => $attendee->id]) }}"
                                      method="POST"
                                      class="d-inline"
                                      onsubmit="return confirmAssist(event, this)">
                                    @csrf
                                    @method('PUT')
                                    <button type="submit" class="btn btn-sm btn-success" title="Asistió?">
                                        <i class="fas fa-clipboard-check"></i>
                                    </button>
                                </form>
                            @endcan
                            <form action="{{ route('marketing.events.attendees.destroy', ['event' => $event->id, 'attendee' => $attendee->id]) }}"
                                  method="POST"
                                  class="d-inline"
                                  onsubmit="return confirmDelete(event, this)">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-danger btn-sm" title="Eliminar">
                                    <i class="fas fa-trash-alt"></i>
                                </button>
                            </form>
                        </td>

                    </tr>
                @empty
                    <tr>
                        <td colspan="11" class="text-center">No hay asistentes registrados.</td>
                    </tr>
                @endforelse
            </x-adminlte-datatable>
            <script>
                function sendQrToEmail($attendeeId) {

                    var event = window.event;
                    event.preventDefault();
                    var eventId = {{ $event->id }};  // ID del evento

                    var url = 'marketing/events/' + eventId + '/attendees/' + $attendeeId + '/sendqr';


                    var filters = {
                        _token: document.querySelector('input[name="_token"]').value
                    };


                    ajaxRequestAlert('POST', url, filters);
                }
            </script>
        </div>
        <div class="card-footer">
            <!-- Aquí puedes agregar paginación o información adicional -->
        </div>
    </div>
</div>
