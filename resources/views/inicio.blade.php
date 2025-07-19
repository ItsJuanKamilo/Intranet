@extends('adminlte::page')

@section('title', 'Dashboard Artilec')

@section('content_header')
    <h1>Dashboard Artilec</h1>
@stop

@section('content')
    {{-- Resumen de KPIs en Small Boxes --}}
    <div class="row">
        <!-- Usuarios Registrados -->
        <div class="col-lg-3 col-6">
            <div class="small-box bg-info">
                <div class="inner">
                    <h3>150</h3>
                    <p>Usuarios Registrados</p>
                </div>
                <div class="icon">
                    <i class="fas fa-users"></i>
                </div>
                <a href="#" class="small-box-footer">Ver detalles <i class="fas fa-arrow-circle-right"></i></a>
            </div>
        </div>
        <!-- Ventas del Mes -->
        <div class="col-lg-3 col-6">
            <div class="small-box bg-success">
                <div class="inner">
                    <h3>$25k</h3>
                    <p>Ventas del Mes</p>
                </div>
                <div class="icon">
                    <i class="fas fa-dollar-sign"></i>
                </div>
                <a href="#" class="small-box-footer">Ver detalles <i class="fas fa-arrow-circle-right"></i></a>
            </div>
        </div>
        <!-- Productos en Stock -->
        <div class="col-lg-3 col-6">
            <div class="small-box bg-warning">
                <div class="inner">
                    <h3>500</h3>
                    <p>Productos en Stock</p>
                </div>
                <div class="icon">
                    <i class="fas fa-box"></i>
                </div>
                <a href="#" class="small-box-footer">Ver detalles <i class="fas fa-arrow-circle-right"></i></a>
            </div>
        </div>
        <!-- Pedidos Pendientes -->
        <div class="col-lg-3 col-6">
            <div class="small-box bg-danger">
                <div class="inner">
                    <h3>35</h3>
                    <p>Pedidos Pendientes</p>
                </div>
                <div class="icon">
                    <i class="fas fa-shopping-cart"></i>
                </div>
                <a href="#" class="small-box-footer">Ver detalles <i class="fas fa-arrow-circle-right"></i></a>
            </div>
        </div>
    </div>

    {{-- Métricas Avanzadas con Info Boxes --}}
    <div class="row">
        <!-- Tasa de Conversión -->
        <div class="col-lg-3 col-6">
            <div class="info-box bg-secondary">
                <span class="info-box-icon"><i class="fas fa-chart-line"></i></span>
                <div class="info-box-content">
                    <span class="info-box-text">Tasa de Conversión</span>
                    <span class="info-box-number">3.5%</span>
                    <div class="progress">
                        <div class="progress-bar" style="width: 35%"></div>
                    </div>
                    <span class="progress-description">
                      +0.5% vs. mes anterior
                    </span>
                </div>
            </div>
        </div>
        <!-- Usuarios Activos Hoy -->
        <div class="col-lg-3 col-6">
            <div class="info-box bg-primary">
                <span class="info-box-icon"><i class="fas fa-user-check"></i></span>
                <div class="info-box-content">
                    <span class="info-box-text">Usuarios Activos Hoy</span>
                    <span class="info-box-number">120</span>
                    <div class="progress">
                        <div class="progress-bar" style="width: 80%"></div>
                    </div>
                    <span class="progress-description">
                      80% de la base registrada
                    </span>
                </div>
            </div>
        </div>
        <!-- Tiempo de Respuesta del Sistema -->
        <div class="col-lg-3 col-6">
            <div class="info-box bg-info">
                <span class="info-box-icon"><i class="fas fa-clock"></i></span>
                <div class="info-box-content">
                    <span class="info-box-text">Tiempo de Respuesta</span>
                    <span class="info-box-number">350ms</span>
                    <div class="progress">
                        <div class="progress-bar" style="width: 70%"></div>
                    </div>
                    <span class="progress-description">
                      Excelente rendimiento
                    </span>
                </div>
            </div>
        </div>
        <!-- Tickets de Soporte Abiertos -->
        <div class="col-lg-3 col-6">
            <div class="info-box bg-danger">
                <span class="info-box-icon"><i class="fas fa-life-ring"></i></span>
                <div class="info-box-content">
                    <span class="info-box-text">Tickets de Soporte</span>
                    <span class="info-box-number">8</span>
                    <div class="progress">
                        <div class="progress-bar" style="width: 40%"></div>
                    </div>
                    <span class="progress-description">
                      Atención requerida
                    </span>
                </div>
            </div>
        </div>
    </div>

    {{-- Sección de Gráficos Interactivos --}}
    <div class="row mt-4">
        <!-- Gráfico: Ventas por Categoría -->
        <div class="col-md-6">
            <div class="card">
                <div class="card-header bg-secondary text-white">
                    <h3 class="card-title">Ventas por Categoría</h3>
                </div>
                <div class="card-body">
                    <canvas id="ventasPorCategoria"></canvas>
                </div>
            </div>
        </div>
        <!-- Gráfico: Tendencia de Ventas -->
        <div class="col-md-6">
            <div class="card">
                <div class="card-header bg-info text-white">
                    <h3 class="card-title">Tendencia de Ventas</h3>
                </div>
                <div class="card-body">
                    <canvas id="tendenciaDeVentas"></canvas>
                </div>
            </div>
        </div>
    </div>

    {{-- Tabla de Pedidos Recientes con Estado y Mejor Formato --}}
    <div class="row mt-4">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header bg-primary text-white">
                    <h3 class="card-title">Pedidos Recientes</h3>
                </div>
                <div class="card-body table-responsive">
                    <table class="table table-bordered table-hover">
                        <thead>
                        <tr>
                            <th>#</th>
                            <th>Cliente</th>
                            <th>Producto</th>
                            <th>Cantidad</th>
                            <th>Total</th>
                            <th>Estado</th>
                        </tr>
                        </thead>
                        <tbody>
                        <tr>
                            <td>1</td>
                            <td>Juan Pérez</td>
                            <td>Laptop</td>
                            <td>1</td>
                            <td>$1,000</td>
                            <td><span class="badge badge-warning">Pendiente</span></td>
                        </tr>
                        <tr>
                            <td>2</td>
                            <td>María López</td>
                            <td>Smartphone</td>
                            <td>2</td>
                            <td>$1,200</td>
                            <td><span class="badge badge-success">Entregado</span></td>
                        </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
@stop

{{-- IMPORTANTE: No mostrar el preloader para evitar duplicados --}}
@section('preloader')
    <style>
        .preloader {
            display: none !important;
        }
    </style>
@endsection

@section('js')
    <script>
        // Gráfico de Ventas por Categoría (Pie Chart)
        const ctx1 = document.getElementById('ventasPorCategoria').getContext('2d');
        new Chart(ctx1, {
            type: 'pie',
            data: {
                labels: ['Electrónica', 'Ropa', 'Hogar', 'Otros'],
                datasets: [{
                    data: [5000, 3000, 2000, 1500],
                    backgroundColor: ['#007bff', '#28a745', '#ffc107', '#6c757d']
                }]
            }
        });

        // Gráfico de Tendencia de Ventas (Line Chart)
        const ctx2 = document.getElementById('tendenciaDeVentas').getContext('2d');
        new Chart(ctx2, {
            type: 'line',
            data: {
                labels: ['Enero', 'Febrero', 'Marzo', 'Abril', 'Mayo', 'Junio'],
                datasets: [{
                    label: 'Ventas',
                    data: [2000, 3000, 4000, 5000, 4500, 5500],
                    backgroundColor: 'rgba(0, 123, 255, 0.2)',
                    borderColor: '#007bff',
                    fill: true,
                    tension: 0.3
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: {
                        display: true
                    }
                }
            }
        });



    </script>
@stop
