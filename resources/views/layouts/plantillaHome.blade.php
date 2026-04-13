<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-Auto-Compatible" content="ie=edge">
    <title>@yield('title', 'Moveet')</title>
</head>
<body>

    @include('layouts.navbar')

    <main class="page-content pt-30">
        @yield('content')
    </main>


    <script src="https://cdn.tailwindcss.com"></script>

</body>
</html>