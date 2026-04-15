<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>@yield('title', 'Moveet')</title>

    {{--
        ⚠️  ORDEN IMPORTANTE:
        1. Tailwind primero → así el preflight/reset se aplica antes que tus estilos
        2. Leaflet CSS
        3. Tu CSS (tiene prioridad sobre ambos)
    --}}

    {{-- 1. Tailwind (movido al <head> para que no machaque estilos de Leaflet) --}}
    <script src="https://cdn.tailwindcss.com"></script>

    {{-- 2. Leaflet CSS --}}
    <link rel="stylesheet"
          href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css"/>

    {{-- 3. Estilos de la página home (sobreescribe Tailwind y Leaflet) --}}
    <link rel="stylesheet" href="{{ asset('css/home.css') }}">
    
    {{-- Estilos adicionales de subsecciones --}}
    @stack('styles')
    
    <meta name="csrf-token" content="{{ csrf_token() }}">

</head>
<body>

    @include('layouts.navbar')

    <main class="page-content">
        @yield('content')
    </main>

    {{-- Leaflet JS --}}
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>

    @stack('scripts')

</body>
</html>