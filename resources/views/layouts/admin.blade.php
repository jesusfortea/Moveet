<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>@yield('title', 'Admin - Moveet')</title>

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="{{ asset('css/admin.css') }}">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Nunito:wght@400;600;700;800;900&display=swap');

        *, *::before, *::after { box-sizing: border-box; }

        body {
            margin: 0;
            padding: 0;
            background: #f5f5f5;
            font-family: 'Nunito', sans-serif;
        }

        .admin-page {
            display: grid;
            grid-template-rows: 78px 1fr 22px;
            min-height: 100vh;
        }

        .admin-navbar {
            background: #8FA8A6;
            display: flex;
            align-items: center;
            padding: 0 16px;
            gap: 12px;
            min-height: 78px;
        }

        .admin-navbar-logo img {
            height: 54px;
            width: auto;
            display: block;
        }

        .admin-navbar-search {
            flex: 1;
            min-width: 0;
            display: flex;
            justify-content: center;
        }

        .admin-search-shell {
            position: relative;
            width: 100%;
            max-width: 500px;
        }

        .admin-search-shell i {
            position: absolute;
            left: 12px;
            top: 50%;
            transform: translateY(-50%);
            color: #8a9594;
            font-size: 12px;
        }

        .admin-navbar-search input {
            width: 100%;
            border: 2px solid #2f2f2f;
            border-radius: 8px;
            background: #fff;
            padding: 8px 16px 8px 32px;
            font-size: 12px;
            color: #1E2A28;
            font-family: 'Nunito', sans-serif;
        }

        .admin-navbar-user {
            flex: 0 0 auto;
            display: flex;
            align-items: center;
            gap: 12px;
            color: white;
        }

        .admin-navbar-avatar {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background: white;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #8FA8A6;
            font-weight: 800;
            font-size: 13px;
        }

        .admin-navbar-info p {
            margin: 0;
            line-height: 1.2;
        }

        .admin-navbar-info p:first-child {
            font-size: 13px;
            font-weight: 700;
        }

        .admin-navbar-info p:last-child {
            font-size: 10px;
            opacity: 0.95;
        }

        .admin-navbar-logout {
            color: white;
            background: none;
            border: none;
            cursor: pointer;
            font-size: 12px;
            font-weight: 700;
            font-family: 'Nunito', sans-serif;
        }

        .admin-main {
            display: grid;
            grid-template-columns: 184px 1fr;
            min-height: 0;
        }

        .admin-sidebar {
            background: #c5d8d6;
            min-height: 0;
        }

        .admin-sidebar-scroll {
            height: 100%;
            overflow-y: auto;
            padding: 12px;
            display: flex;
            flex-direction: column;
            gap: 12px;
        }

        .admin-sidebar-item {
            display: block;
            padding: 12px;
            border-radius: 8px;
            background: white;
            color: #1E2A28;
            text-decoration: none;
            text-align: center;
            font-size: 12px;
            font-weight: 700;
            line-height: 1.25;
            transition: background 0.2s;
        }

        .admin-sidebar-item:hover {
            background: #f0f0f0;
        }

        .admin-sidebar-item.active {
            background: #9fb2b0;
            color: white;
        }

        .admin-menu-empty {
            display: none;
            border-radius: 8px;
            background: white;
            color: #58706e;
            text-align: center;
            font-size: 11px;
            padding: 12px;
        }

        .admin-content {
            min-height: 0;
            overflow-y: auto;
            background: white;
            padding: 16px;
        }

        .admin-stats-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 16px;
        }

        .admin-stat-card {
            background: #d0dbd9;
            border-radius: 8px;
            padding: 28px 18px;
            text-align: center;
            transition: all 0.3s;
        }

        .admin-stat-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
        }

        .admin-stat-number {
            font-size: 2.3rem;
            font-weight: 900;
            color: #1E2A28;
            margin-bottom: 10px;
        }

        .admin-stat-label {
            font-size: 13px;
            font-weight: 600;
            color: #1E2A28;
        }

        .admin-footer {
            background: #8FA8A6;
            color: white;
            text-align: center;
            font-size: 10px;
            font-weight: 700;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        @stack('page-styles')
    </style>
    @stack('styles')
</head>
<body>
    @php
        $adminUser = \Illuminate\Support\Facades\Auth::user();
        $currentRoute = \Illuminate\Support\Facades\Route::currentRouteName();
        $adminLinks = [
            ['label' => 'Dashboard', 'route' => 'admin.dashboard', 'keywords' => 'inicio panel principal'],
            ['label' => 'Usuarios', 'route' => 'admin.usuarios', 'keywords' => 'clientes cuentas personas'],
            ['label' => 'Misiones', 'route' => 'admin.misiones', 'keywords' => 'retos tareas'],
            ['label' => 'Eventos', 'route' => 'admin.eventos', 'keywords' => 'actividades calendario'],
            ['label' => 'Reseñas de usuarios', 'route' => 'admin.preguntas', 'keywords' => 'preguntas faq opiniones comentarios valoraciones resenas reseñas'],
            ['label' => 'Pase de paseo', 'route' => 'admin.pase_paseo', 'keywords' => 'pases paseo'],
            ['label' => 'Lugares', 'route' => 'admin.lugares', 'keywords' => 'sitios ubicaciones'],
            ['label' => 'Recompensas', 'route' => 'admin.recompensas', 'keywords' => 'premios regalos'],
            ['label' => 'Tienda', 'route' => 'admin.tienda', 'keywords' => 'productos compras'],
            ['label' => 'Historial puntos', 'route' => 'admin.historial_puntos', 'keywords' => 'puntos movimientos historial'],
            ['label' => 'Reportes', 'route' => 'admin.reportes.index', 'keywords' => 'incidencias moderacion reportar'],
        ];
    @endphp

    <div class="admin-page">
        <nav class="admin-navbar">
            <div class="admin-navbar-logo">
                <a href="{{ route('admin.dashboard') }}">
                    <img src="{{ asset('img/LogoUsarDiaDia.png') }}" alt="Logo Moveet">
                </a>
            </div>

            <div class="admin-navbar-search">
                <div class="admin-search-shell">
                    <i class="fas fa-search"></i>
                    <input id="admin-menu-search" type="text" placeholder="Buscar secciones del panel...">
                </div>
            </div>

            <div class="admin-navbar-user">
                <div class="admin-navbar-avatar">{{ strtoupper(substr($adminUser->name ?? 'A', 0, 1)) }}</div>
                <div class="admin-navbar-info">
                    <p>{{ $adminUser->name ?? 'Admin' }}</p>
                    <p>usuario administrador</p>
                </div>
                <form action="{{ route('logout') }}" method="POST">
                    @csrf
                    <button type="submit" class="admin-navbar-logout">Salir</button>
                </form>
            </div>
        </nav>

        <div class="admin-main">
            <aside class="admin-sidebar">
                <div class="admin-sidebar-scroll">
                    @foreach ($adminLinks as $link)
                        @php
                            $isActive = $currentRoute === $link['route'] || str_starts_with($currentRoute ?? '', $link['route'] . '.');
                        @endphp
                        <a
                            href="{{ route($link['route']) }}"
                            data-menu-label="{{ mb_strtolower($link['label'] . ' ' . ($link['keywords'] ?? '')) }}"
                            class="admin-sidebar-item admin-menu-link {{ $isActive ? 'active' : '' }}"
                        >
                            {{ $link['label'] }}
                        </a>
                    @endforeach

                    <p id="admin-menu-empty" class="admin-menu-empty">No hay coincidencias para esa búsqueda.</p>
                </div>
            </aside>

            <div class="admin-content">
                @yield('content')
            </div>
        </div>

        <footer class="admin-footer">
            Moveet &copy; {{ date('Y') }} · www.moveet.es
        </footer>

        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
        <script>
            window.showAppAlert = function (message, icon = 'info', title = 'Aviso') {
                if (window.Swal) {
                    return Swal.fire({
                        title,
                        text: message,
                        icon,
                        confirmButtonColor: '#8FA8A6'
                    });
                }

                return Promise.resolve();
            };

            window.showAppConfirm = function (message, title = 'Confirmar acción') {
                if (window.Swal) {
                    return Swal.fire({
                        title,
                        text: message,
                        icon: 'question',
                        showCancelButton: true,
                        confirmButtonText: 'Sí, continuar',
                        cancelButtonText: 'Cancelar',
                        confirmButtonColor: '#8FA8A6',
                        cancelButtonColor: '#9ca3af'
                    }).then(function (result) {
                        return result.isConfirmed;
                    });
                }

                return Promise.resolve(false);
            };

            document.addEventListener('submit', function (event) {
                const form = event.target.closest('form[data-swal-confirm]');
                if (!form || form.dataset.swalConfirmed === '1') {
                    return;
                }

                event.preventDefault();

                const message = form.getAttribute('data-swal-confirm-message') || '¿Confirmas esta acción?';
                const title = form.getAttribute('data-swal-confirm-title') || 'Confirmar acción';

                window.showAppConfirm(message, title).then(function (ok) {
                    if (!ok) {
                        return;
                    }

                    form.dataset.swalConfirmed = '1';
                    form.submit();
                });
            });

            (function () {
                const searchInput = document.getElementById('admin-menu-search');
                const links = Array.from(document.querySelectorAll('.admin-menu-link'));
                const emptyState = document.getElementById('admin-menu-empty');

                if (!searchInput || !links.length || !emptyState) {
                    return;
                }

                const normalize = (value) => value
                    .toLowerCase()
                    .normalize('NFD')
                    .replace(/[\u0300-\u036f]/g, '')
                    .trim();

                const filterLinks = () => {
                    const query = normalize(searchInput.value);
                    let visibleCount = 0;

                    links.forEach((link) => {
                        const label = normalize(link.dataset.menuLabel || link.textContent || '');
                        const isVisible = query === '' || label.includes(query);

                        link.classList.toggle('hidden', !isVisible);

                        if (isVisible) {
                            visibleCount += 1;
                        }
                    });

                    emptyState.style.display = visibleCount > 0 ? 'none' : 'block';
                };

                searchInput.addEventListener('input', filterLinks);
                searchInput.addEventListener('keydown', function (event) {
                    if (event.key !== 'Enter') {
                        return;
                    }

                    event.preventDefault();

                    const firstVisibleLink = links.find((link) => !link.classList.contains('hidden'));

                    if (firstVisibleLink) {
                        window.location.href = firstVisibleLink.href;
                    }
                });
            })();
        </script>

        @stack('scripts')
    </div>
</body>
</html>
