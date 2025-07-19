@extends('adminlte::page', ['iFrameEnabled' => true])

@section('content')
    {{-- Contenido principal de la página, por ejemplo, el listado de clientes --}}
    @yield('page_content')

    {{-- Contenedor para cargar el detalle en un iframe --}}
    <div id="iframeContainer" style="margin-top: 20px;">
        <h2 id="iframeTitle">Detalle del Cliente</h2>
        <iframe id="contenidoIframe" style="width: 100%; height: 700px; border: none;"></iframe>
    </div>
@endsection

@section('js')

    <script
        src="https://js.sentry-cdn.com/e38cef541a8b3797e940c61534255ad8.min.js"
        crossorigin="anonymous"
    ></script>

@endsection
