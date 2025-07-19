@extends('adminlte::page')

@section('title', 'Servidor')

@section('content')
    @include('components._page')
    <div class="card mt-4 card-navy card-tabs">

        <div class="card-header p-0 pt-1">
            <div class="d-flex justify-content-between align-items-center">
                <!-- Título a la izquierda -->
                <div class="pl-3">
                    <h3 class="card-title m-0">Listado de Servicios del Servidor</h3>
                </div>
                <!-- Nav items a la derecha -->
                <div>
                    <ul class="nav nav-tabs" id="custom-tabs" role="tablist">
                        <li class="nav-item">
                            <a class="nav-link active" id="tab1-tab" data-toggle="pill" href="#tab-1" role="tab"
                               aria-controls="tab-1" aria-selected="true">
                                Telescope
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" id="tab2-tab" data-toggle="pill" href="#tab-2" role="tab"
                               aria-controls="tab-2" aria-selected="false">
                                Logs Viewer
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" id="tab3-tab" data-toggle="pill" href="#tab-3" role="tab"
                               aria-controls="tab-3" aria-selected="false">
                                AdminLTE
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" id="tab4-tab" data-toggle="pill" href="#tab-4" role="tab"
                               aria-controls="tab-4" aria-selected="false">
                                Horizon
                            </a>
                        </li>
                    </ul>
                </div>
            </div>
        </div>


        <div class="card-body">
            <div class="tab-content" id="custom-tabs-content">
                <div class="tab-pane fade show active" id="tab-1" role="tabpanel" aria-labelledby="tab1-tab">
                    <div class="row">
                        @include('admin.server.components.telescope')
                    </div>
                </div>

                <div class="tab-pane fade" id="tab-2" role="tabpanel" aria-labelledby="tab2-tab">
                    <div class="row">
                        @include('admin.server.components.logviewer')
                    </div>
                </div>

                <div class="tab-pane fade" id="tab-3" role="tabpanel" aria-labelledby="tab3-tab">
                    <div class="row">
                        <div class="col-12">
                            @include('admin.server.components.adminlte')
                        </div>
                    </div>
                </div>
                <div class="tab-pane fade" id="tab-4" role="tabpanel" aria-labelledby="tab4-tab">
                    <div class="row">
                        <div class="col-12">
                            @include('admin.server.components.horizon')
                        </div>
                    </div>
                </div>
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
            // Custom JavaScript code can be added here
        });
    </script>
@endsection
