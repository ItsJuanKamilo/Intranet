<div class="card card-navy card-outline mt-4">
    <div class="card-header text-center">
        <h3 class="card-title mb-0">Respuestas</h3>
    </div>
    <div class="card-body">
        @php
            // Filtrar respuestas vacías
            $responses = $responses->filter(function($response) {
                return !empty(trim($response->answer));
            });

            // Agrupar las respuestas por la pregunta
            $groupedResponses = $responses->groupBy(function($response) {
                return $response->question ? $response->question->question : 'Pregunta no disponible';
            });
        @endphp

        <div class="table-responsive">
            <table class="table table-striped table-bordered table-sm mb-0 text-center">
                <thead class="thead-dark">
                <tr>
                    <th>Pregunta</th>
                    <th>Promedio</th>
                    <th>Cant. Respuestas</th>
                </tr>
                </thead>
                <tbody>
                @foreach ($groupedResponses as $question => $group)
                    @php
                        // Verificar si todas las respuestas son numéricas
                        $allNumeric = $group->every(function($response) {
                            return is_numeric($response->answer);
                        });

                        if ($allNumeric) {
                            $avg = number_format($group->avg(function($response) {
                                return (float)$response->answer;
                            }), 2);
                            $display = ($avg == '0.00') ? '' : $avg;
                        } else {
                            // Si las respuestas son texto, mostrar el enlace "Revisar detalle"
                            $display = '<a href="#group-' . $loop->index . '">Revisar detalle</a>';
                        }

                        $count = $group->count();
                    @endphp
                    <tr>
                        <td>{{ $question }}</td>
                        <td>{!! $display !!}</td>
                        <td>{{ $count }}</td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>



        @php
            // Asegúrate de que $responses exista, sino usa una colección vacía.
            $responses = $responses ?? collect();
            // Agrupar las respuestas por el texto de la pregunta.
            $groupedResponses = $responses->groupBy(function($response) {
                return $response->question ? $response->question->question : 'Pregunta no disponible';
            });
        @endphp

        @forelse ($groupedResponses as $question => $group)
            @php
                // Calcular el promedio (solo para mostrar si es numérico)
                $avg = $group->avg(function($response) {
                    return is_numeric($response->answer) ? (float)$response->answer : 0;
                });
            @endphp
            <div class="mb-4 mt-4" id="group-{{ $loop->index }}">
                <h4 class="mb-2">
                    {{ $question }}
                    @if(number_format($avg, 2) != '0.00')
                        <span class="float-right text-muted" style="font-size: 14px;">
                                                                Promedio: {{ number_format($avg, 2) }}
                                                            </span>
                    @endif
                </h4>
                <table class="table table-striped table-hover table-sm m-0" style="table-layout: fixed; width: 100%;">
                    <thead>
                    <tr>
                        <th style="width: 80px;">ID</th>
                        <th style="width: 80px;">Rut</th>
                        <th style="width: 100px;">Nombre</th>
                        <th style="width: 120px;">Empresa</th>
                        <th style="width: 180px;">Respuesta</th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach ($group as $index => $response)
                        @if ($index < 5)
                            <tr>
                                <td>{{ $response->id }}</td>
                                <td>{{ $response->attendee->vat ?? 'N/A' }}</td>
                                <td>{{ $response->attendee->name ?? 'N/A' }}</td>
                                <td>{{ $response->attendee->tax_name ?? 'N/A' }}</td>
                                <td>{{ $response->answer }}</td>
                            </tr>
                        @endif
                    @endforeach
                    </tbody>
                    @if ($group->count() > 5)
                        <tbody class="collapse" id="collapse-{{ $loop->index }}">
                        @foreach ($group as $index => $response)
                            @if ($index >= 5)
                                <tr>
                                    <td>{{ $response->id }}</td>
                                    <td>{{ $response->attendee->vat ?? 'N/A' }}</td>
                                    <td>{{ $response->attendee->name ?? 'N/A' }}</td>
                                    <td>{{ $response->attendee->tax_name ?? 'N/A' }}</td>
                                    <td>{{ $response->answer }}</td>
                                </tr>
                            @endif
                        @endforeach
                        </tbody>
                    @endif
                </table>
                @if ($group->count() > 5)
                    <button class="btn btn-link btn-sm" type="button" data-toggle="collapse" data-target="#collapse-{{ $loop->index }}" aria-expanded="false" aria-controls="collapse-{{ $loop->index }}">
                        Expandir/Colapsar
                    </button>
                @endif
            </div>
        @empty
            <div class="text-center">No hay respuestas registradas.</div>
        @endforelse

    </div>
    <div class="card-footer">
        <!-- Aquí puedes agregar paginación o información adicional -->
    </div>
</div>
