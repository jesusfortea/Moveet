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
            grid-template-rows: 15vh 1fr auto;
            min-height: 100vh;
        }
        
        /* ── Navbar ── */
        .admin-navbar {
            background: #8FA8A6;
            display: flex;
            align-items: center;
            padding: 0 20px;
            height: 15vh;
            min-height: 72px;
            position: relative;
            z-index: 50;
            gap: 16px;
        }
        
        .admin-navbar-logo {
            flex: 0 0 auto;
        }
        
        .admin-navbar-logo img {
            height: 90px;
            width: auto;
            display: block;
        }
        
        .admin-navbar-search {
            flex: 1;
            min-width: 0;
            display: flex;
            justify-content: center;
        }
        
        .admin-navbar-search input {
            width: 100%;
            max-width: 560px;
            padding: 10px 16px;
            border: none;
            border-radius: 6px;
            font-size: 14px;
            color: #555;
            font-family: 'Nunito', sans-serif;
        }
        
        .admin-navbar-user {
            flex: 0 0 auto;
            display: flex;
            align-items: center;
            gap: 14px;
            color: white;
        }
        
        .admin-navbar-avatar {
            width: 54px;
            height: 54px;
            background: white;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 800;
            color: #8FA8A6;
            font-size: 20px;
            flex-shrink: 0;
        }
        
        .admin-navbar-info p {
            margin: 0;
            line-height: 1.25;
        }
        
        .admin-navbar-info p:first-child {
            font-weight: 700;
            font-size: 15px;
        }
        
        .admin-navbar-info p:last-child {
            font-size: 12px;
            opacity: 0.85;
        }

        .admin-navbar-logout {
            flex: 0 0 auto;
            color: white;
            font-size: 14px;
            background: none;
            border: none;
            cursor: pointer;
            padding: 10px 18px;
            font-weight: 700;
            font-family: 'Nunito', sans-serif;
            transition: background 0.2s;
            border-radius: 6px;
        }
        
        .admin-navbar-logout:hover {
            background: rgba(255, 255, 255, 0.15);
        }
        
        /* ── Main grid ── */
        .admin-main {
            display: grid;
            grid-template-columns: 240px 1fr;
            overflow: hidden;
        }
        
        /* ── Sidebar ── */
        .admin-sidebar {
            background: #c5d8d6;
            overflow-y: auto;
            padding: 18px 14px;
            display: flex;
            flex-direction: column;
            gap: 10px;
        }
        
        .admin-sidebar-item {
            padding: 14px 18px;
            background: white;
            border: none;
            border-radius: 6px;
            color: #333;
            cursor: pointer;
            font-family: 'Nunito', sans-serif;
            font-size: 14px;
            font-weight: 600;
            transition: all 0.2s;
            text-align: center;
            display: block;
            text-decoration: none;
        }
        
        .admin-sidebar-item:hover {
            background: #eef2f1;
            color: #1E2A28;
        }
        
        .admin-sidebar-item.active {
            background: #8FA8A6;
            color: white;
            font-weight: 700;
        }
        
        /* ── Content area ── */
        .admin-content {
            background: white;
            overflow-y: auto;
            padding: 36px 40px;
            display: flex;
            flex-direction: column;
        }
        
        /* ── Stats (dashboard) ── */
        .admin-stats-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 24px;
        }
        
        .admin-stat-card {
            background: #d0dbd9;
            border-radius: 8px;
            padding: 36px 24px;
            text-align: center;
            transition: all 0.3s;
        }
        
        .admin-stat-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(0,0,0,0.1);
        }
        
        .admin-stat-number {
            font-size: 2.8rem;
            font-weight: 900;
            color: #1E2A28;
            margin-bottom: 14px;
        }
        
        .admin-stat-label {
            font-size: 14px;
            font-weight: 600;
            color: #1E2A28;
        }
        
        /* ── Footer ── */
        .admin-footer {
            background: #8FA8A6;
            padding: 12px 20px;
            text-align: center;
            color: white;
            font-size: 13px;
            font-weight: 600;
            line-height: 1.5;
        }

        @stack('page-styles')
    </style>
    @stack('styles')
</head>
<body>
    <div class="admin-page">
        
        <!-- Navbar -->
        <nav class="admin-navbar">
            <div class="admin-navbar-logo">
                <a href="{{ route('admin.dashboard') }}">
                    <img src="{{ asset('img/LogoUsarDiaDia.png') }}" alt="Logo Moveet">
                </a>
            </div>
            
            <div class="admin-navbar-search">
                <input type="text" placeholder="Escribe algo...">
            </div>
            
            <div class="admin-navbar-user">
                <div class="admin-navbar-avatar">{{ strtoupper(substr(Auth::user()->name ?? 'A', 0, 1)) }}</div>
                <div class="admin-navbar-info">
                    <p>{{ Auth::user()->name ?? 'Admin' }}</p>
                    <p>Administrador</p>
                </div>
            </div>
            
            <form action="{{ route('logout') }}" method="POST" style="display: inline;">
                @csrf
                <button type="submit" class="admin-navbar-logout">Salir</button>
            </form>
        </nav>
        
        <!-- Main content -->
        <div class="admin-main">
            
            <!-- Sidebar -->
            <aside class="admin-sidebar">
                <a href="{{ route('admin.dashboard') }}"
                   class="admin-sidebar-item @if(Route::currentRouteName() == 'admin.dashboard') active @endif">
                   Dashboard
                </a>
                <a href="{{ route('admin.usuarios') }}"
                   class="admin-sidebar-item @if(Route::currentRouteName() == 'admin.usuarios' || str_starts_with(Route::currentRouteName() ?? '', 'admin.usuarios.')) active @endif">
                   Usuarios
                </a>
                <a href="{{ route('admin.misiones') }}"
                   class="admin-sidebar-item @if(Route::currentRouteName() == 'admin.misiones' || str_starts_with(Route::currentRouteName() ?? '', 'admin.misiones.')) active @endif">
                   Misiones
                </a>
                <a href="{{ route('admin.eventos') }}"
                   class="admin-sidebar-item @if(Route::currentRouteName() == 'admin.eventos' || str_starts_with(Route::currentRouteName() ?? '', 'admin.eventos.')) active @endif">
                   Eventos
                </a>
                <a href="{{ route('admin.pase_paseo') }}"
                   class="admin-sidebar-item @if(Route::currentRouteName() == 'admin.pase_paseo' || str_starts_with(Route::currentRouteName() ?? '', 'admin.pase_paseo.')) active @endif">
                   Pase de paseo
                </a>
                <a href="{{ route('admin.lugares') }}"
                   class="admin-sidebar-item @if(Route::currentRouteName() == 'admin.lugares' || str_starts_with(Route::currentRouteName() ?? '', 'admin.lugares.')) active @endif">
                   Lugares
                </a>
                <a href="{{ route('admin.recompensas') }}"
                   class="admin-sidebar-item @if(Route::currentRouteName() == 'admin.recompensas' || str_starts_with(Route::currentRouteName() ?? '', 'admin.recompensas.')) active @endif">
                   Recompensas
                </a>
                <a href="{{ route('admin.tienda') }}"
                   class="admin-sidebar-item @if(Route::currentRouteName() == 'admin.tienda' || str_starts_with(Route::currentRouteName() ?? '', 'admin.tienda.')) active @endif">
                   Tienda
                </a>
                <a href="{{ route('admin.historial_puntos') }}"
                   class="admin-sidebar-item @if(Route::currentRouteName() == 'admin.historial_puntos') active @endif">
                   Historial puntos
                </a>
                <a href="{{ route('admin.reportes.index') }}"
                   class="admin-sidebar-item @if(str_starts_with(Route::currentRouteName() ?? '', 'admin.reportes')) active @endif">
                   Reportes
                </a>
            </aside>
            
            <!-- Content -->
            <div class="admin-content">
                @yield('content')
            </div>
        </div>
        
        <!-- Footer -->
        <footer class="admin-footer">
            Moveet &copy; {{ date('Y') }} &nbsp;·&nbsp; www.moveet.es
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
        </script>

        @stack('scripts')
    </div>
</body>
</html>
