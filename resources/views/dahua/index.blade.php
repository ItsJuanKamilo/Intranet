@extends('adminlte::page')

@section('title', 'Test')

@section('content_header')
    <h1>Vista de Test</h1>
@stop

@section('content')
    <div class="container">
        <h2>Fecha de hoy:</h2>
        <p id="fechaHoy">Cargando...</p>
        <button id="cargarFechaBtn" class="btn btn-primary">Cargar Fecha</button>
    </div>
@stop

@section('js')
    <script>
        // Función AJAX para obtener la fecha de hoy desde el controlador
        $('#cargarFechaBtn').click(function() {
            $.ajax({
                url: '/test/testJleiton',  // La URL que definimos en las rutas
                type: 'GET',   // Metodo de la solicitud
                success: function(response) {
                    // Actualizamos el contenido del elemento con la id 'fechaHoy'
                    $('#fechaHoy').text(response.hoy);
                },
                error: function() {
                    alert('Error al cargar los datos.');
                }
            });
        });
    </script>
@stop
