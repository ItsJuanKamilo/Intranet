@extends('adminlte::page')

@section('title', 'Editar Evento')

@section('content')
    @include('components._page')
    <div class="row">
        <div class="col-12">
            <!-- Card principal con tabs -->
            <div class="card card-navy card-tabs mt-4">
                <div class="card-header p-0 pt-1">
                    <div class="d-flex justify-content-between align-items-center">
                        <!-- Título a la izquierda -->
                        <div class="pl-3">
                            <h3 class="card-title m-0">Editar Evento</h3>
                        </div>
                        <!-- Nav items a la derecha -->
                        <div>
                            <ul class="nav nav-tabs" id="custom-tabs-two-tab" role="tablist">
                                <li class="nav-item">
                                    <a class="nav-link active" id="evento-tab" data-toggle="pill" href="#evento" role="tab" aria-controls="evento" aria-selected="true">Evento</a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link" id="asistencia-tab" data-toggle="pill" href="#asistencia" role="tab" aria-controls="asistencia" aria-selected="false">Ver Asistentes</a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link" id="respuestas-tab" data-toggle="pill" href="#respuestas" role="tab" aria-controls="respuestas" aria-selected="false">Ver Respuestas</a>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div> <!-- /.card-header -->

                <div class="card-body">
                    <!-- Contenedor de contenido de las tabs -->
                    <div class="tab-content" id="custom-tabs-two-tabContent">

                        <!-- Pestaña Evento -->
                        <div class="tab-pane fade show active" id="evento" role="tabpanel" aria-labelledby="evento-tab">
                            <form action="{{ route('marketing.events.update', $event->id) }}"
                                  method="POST"
                                  enctype="multipart/form-data">
                                @csrf
                                @method('PUT')
                                <div class="card card-navy card-outline">
                                    <div class="card-header">
                                        <h3 class="card-title">Editar Evento / {{ $event->id }}</h3>
                                    </div>
                                    <div class="card-body pb-0">
                                        <div class="row">
                                            <div class="col-md-9">
                                                @include('marketing.events.components.form_edit')
                                            </div>
                                            <div class="col-md-3 text-center">
                                                <h5 class="mb-3">Portada</h5>
                                                <img id="promoPreview"
                                                     src="{{ $event->getImagePromoUrl() }}"
                                                     alt="Vista Previa de la Imagen"
                                                     class="img-fluid img-thumbnail mb-3"
                                                     style="max-width: 100%; object-fit: contain;">
                                                <div>
                                                    <label class="btn btn-primary bg-navy btn-sm" for="image_promo" style="height: 38px; line-height: 38px; padding: 0 12px;">
                                                        <i class="fas fa-upload"></i> Actualizar Imagen
                                                    </label>
                                                    <input type="file" id="image_promo" name="image_promo" class="d-none" accept="image/*">
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="card-footer d-flex justify-content-between align-items-center">
                                        <button type="submit" class="btn btn-primary ml-auto">
                                            Guardar Cambios <i class="far fa-save"></i>
                                        </button>
                                    </div>
                                </div>
                            </form>
                        </div>

                        <!-- Pestaña Asistencia -->
                        <div class="tab-pane fade" id="asistencia" role="tabpanel" aria-labelledby="asistencia-tab">
                            <div class="row">
                                <div class="col-12">
                                    @include('marketing.events.components.tab_attendees')
                                </div>
                            </div>
                        </div>

                        <!-- Pestaña Respuestas -->
                        <div class="tab-pane fade" id="respuestas" role="tabpanel" aria-labelledby="respuestas-tab">
                            <div class="row">
                                <div class="col-12">
                                    @include('marketing.events.components.tab_responses')
                                </div>
                            </div>
                        </div>

                    </div> <!-- /.tab-content -->
                </div> <!-- /.card-body -->

                <!-- Card Footer que estará visible en todas las pestañas -->
                <div class="card-footer d-flex justify-content-between align-items-center">
                    <a href="{{ route('marketing.events.index') }}" class="btn btn-dark">
                        <i class="fas fa-undo-alt"></i> Volver atrás
                    </a>
                </div>
            </div> <!-- /.card -->
        </div>
    </div>
    <!-- Script para actualizar la vista previa de la imagen al seleccionarla -->
    <script>
        document.getElementById('image_promo').addEventListener('change', function(e) {
            const [file] = this.files;
            if (file) {
                const preview = document.getElementById('promoPreview');
                preview.src = URL.createObjectURL(file);
            }
        });
    </script>
@endsection
