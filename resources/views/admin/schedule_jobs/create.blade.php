@extends('adminlte::page')

@section('title', 'Crear Trabajo Programado')

@section('content')
    @include('components._page')

    <div class="card mt-4 card-navy card-outline">
        <div class="card-header">
            <h3 class="card-title">Crear Trabajo Programado</h3>
            <div class="card-tools">
                <button type="button" class="btn btn-tool" data-card-widget="collapse" title="Collapse">
                    <i class="fas fa-minus"></i>
                </button>
            </div>
        </div>

        <form action="{{ route('admin.schedule_jobs.store') }}" method="POST">
            @csrf
            <div class="card-body">
                <div class="row">

                    {{-- Nombre del Job --}}
                    <div class="col-md-4">
                        <div class="form-group">
                            <label>Nombre del Job</label>
                            <input type="text" name="name" class="form-control @error('name') is-invalid @enderror"
                                   value="{{ old('name') }}" required>
                            @error('name')
                            <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>

                    {{-- Rol Asociado --}}
                    <div class="col-md-4">
                        <div class="form-group">
                            <label>Rol Asociado</label>
                            <select name="role_id" class="form-control select2bs4  @error('role_id') is-invalid @enderror">
                                <option value="">Seleccione un Rol</option>
                                @foreach($roles as $role)
                                    <option value="{{ $role->id }}" {{ old('role_id') == $role->id ? 'selected' : '' }}>
                                        {{ $role->name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('role_id')
                            <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>

                    {{-- Tipo --}}
                    <div class="col-md-4">
                        <div class="form-group">
                            <label>Tipo</label>
                            <select name="type" class="form-control @error('type') is-invalid @enderror">
                                <option value="Report" {{ old('type') == 'Report' ? 'selected' : '' }}>Report</option>
                                <option value="Update" {{ old('type') == 'Update' ? 'selected' : '' }}>Update</option>
                                <option value="Check" {{ old('type') == 'Check' ? 'selected' : '' }}>Check</option>
                                <option value="Sync" {{ old('type') == 'Sync' ? 'selected' : '' }}>Sync</option>
                            </select>
                            @error('type')
                            <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>

                </div>

                <div class="row">


                    {{-- Schedule --}}
                    <div class="col-md-4">
                        <div class="form-group">
                            <label>Schedule (Expresión Cron)</label>
                            <select name="schedule" id="scheduleSelect" class="form-control @error('schedule') is-invalid @enderror">
                                <option value="">Seleccione un horario</option>
                                <option value="* * * * *" {{ old('schedule') == '* * * * *' ? 'selected' : '' }}>Cada Minuto</option>
                                <option value="*/5 * * * *" {{ old('schedule') == '*/5 * * * *' ? 'selected' : '' }}>Cada 5 Minutos</option>
                                <option value="*/15 * * * *" {{ old('schedule') == '*/15 * * * *' ? 'selected' : '' }}>Cada 15 Minutos</option>
                                <option value="*/30 * * * *" {{ old('schedule') == '*/30 * * * *' ? 'selected' : '' }}>Cada 30 Minutos</option>
                                <option value="0 * * * *" {{ old('schedule') == '0 * * * *' ? 'selected' : '' }}>Cada Hora</option>
                                <option value="0 0 * * *" {{ old('schedule') == '0 0 * * *' ? 'selected' : '' }}>Diario a Medianoche</option>
                                <option value="0 9 * * *" {{ old('schedule') == '0 9 * * *' ? 'selected' : '' }}>Diario a las 9 AM</option>
                                <option value="0 4 * * *" {{ old('schedule') == '0 4 * * *' ? 'selected' : '' }}>Diario a las 4 AM</option>
                                <option value="custom">Personalizado</option>
                            </select>
                            <input type="text" name="schedule_custom" id="scheduleCustom"
                                   class="form-control mt-2 d-none @error('schedule') is-invalid @enderror"
                                   placeholder="Ingrese expresión cron personalizada" value="{{ old('schedule') }}">
                            @error('schedule')
                            <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>

                    {{-- Días --}}
                    <div class="col-md-4">
                        <div class="form-group">
                            <label>Días</label>
                            <select name="days" id="daysSelect" class="form-control @error('days') is-invalid @enderror">
                                <option value="everyday" {{ old('days') == 'everyday' ? 'selected' : '' }}>Todos los días</option>
                                <option value="weekdays" {{ old('days') == 'weekdays' ? 'selected' : '' }}>Días de la semana</option>
                                <option value="weekly" {{ old('days') == 'weekly' ? 'selected' : '' }}>Semanalmente</option>
                                <option value="monthly" {{ old('days') == 'monthly' ? 'selected' : '' }}>Mensual</option>
                                <option value="custom">Elegir días</option>
                            </select>
                            <div id="customDays" class="mt-2 d-none">
                                <label>Días Personalizados</label>
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="custom_days[]" value="0" id="sunday">
                                    <label class="form-check-label" for="sunday">Domingo</label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="custom_days[]" value="1" id="monday">
                                    <label class="form-check-label" for="monday">Lunes</label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="custom_days[]" value="2" id="tuesday">
                                    <label class="form-check-label" for="tuesday">Martes</label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="custom_days[]" value="3" id="wednesday">
                                    <label class="form-check-label" for="wednesday">Miércoles</label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="custom_days[]" value="4" id="thursday">
                                    <label class="form-check-label" for="thursday">Jueves</label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="custom_days[]" value="5" id="friday">
                                    <label class="form-check-label" for="friday">Viernes</label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="custom_days[]" value="6" id="saturday">
                                    <label class="form-check-label" for="saturday">Sábado</label>
                                </div>
                            </div>
                            @error('days')
                            <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>

                </div>

                {{-- Descripción Opcional --}}
                <div class="form-group">
                    <label>Descripción (Opcional)</label>
                    <textarea name="description" class="form-control @error('description') is-invalid @enderror">{{ old('description') }}</textarea>
                    @error('description')
                    <span class="invalid-feedback">{{ $message }}</span>
                    @enderror
                </div>

            </div>
            <div class="card-footer">
                <div class="float-right">
                    <button type="submit" class="btn btn-primary">Guardar <i class="far fa-save"></i></button>
                </div>
                <a href="{{ route('admin.schedule_jobs.index') }}" class="btn btn-dark">
                    <i class="fas fa-undo-alt"></i> Volver atrás
                </a>
            </div>
        </form>
    </div>
@endsection

@section('js')
    <script>
        $(document).ready(function () {
            $('.select2bs4').select2({
                theme: 'bootstrap4'
            });

            $('#scheduleSelect').on('change', function () {
                if ($(this).val() === 'custom') {
                    $('#scheduleCustom').removeClass('d-none').focus();
                } else {
                    $('#scheduleCustom').addClass('d-none').val($(this).val());
                }
            });

            $('#daysSelect').on('change', function () {
                if ($(this).val() === 'custom' || $(this).val() === 'weekly') {
                    $('#customDays').removeClass('d-none');
                } else {
                    $('#customDays').addClass('d-none');
                }

                if ($(this).val() === 'monthly') {
                    $('#scheduleSelect').prop('disabled', true);
                } else {
                    $('#scheduleSelect').prop('disabled', false);
                }
            });
        });
    </script>
@endsection
