<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Dashboard Admin · Moveet</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <style>
        body {
            background: #f5f5f5;
        }
        
        .admin-page {
            display: grid;
            grid-template-rows: 15vh 1fr auto;
            min-height: 100vh;
        }
        
        /* Navbar */
        .admin-navbar {
            background: #8FA8A6;
            display: flex;
            align-items: center;
            padding: 0 20px;
            height: 15vh;
            position: relative;
            z-index: 50;
        }
        
        .admin-navbar-logo {
            flex: 0 0 auto;
            margin-right: 20px;
        }
        
        .admin-navbar-logo img {
            height: 90px;
            width: auto;
        }
        
        .admin-navbar-search {
            flex: 1;
            min-width: 0;
            display: flex;
            justify-content: center;
        }
        
        .admin-navbar-search input {
            width: 600px;
            padding: 10px 16px;
            border: none;
            border-radius: 6px;
            font-size: 14px;
            color: #999;
        }
        
        .admin-navbar-user {
            flex: 0 0 auto;
            display: flex;
            align-items: center;
            gap: 16px;
            color: white;
            margin-left: 2px
        }
        
        .admin-navbar-logout {
            flex: 0 0 auto;
            color: white;
            font-size: 15px;
            background: none;
            border: none;
            cursor: pointer;
            padding: 12px 20px;
            font-weight: 600;
            transition: all 0.2s;
            border-radius: 6px;
        }
        
        .admin-navbar-logout:hover {
            background: rgba(255, 255, 255, 0.1);
        }
        
        .admin-navbar-avatar {
            width: 60px;
            height: 60px;
            background: white;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: bold;
            color: #8FA8A6;
            font-size: 20px;
            flex-shrink: 0;
        }
        
        .admin-navbar-info p {
            margin: 0;
            line-height: 1.2;
        }
        
        .admin-navbar-info p:first-child {
            font-weight: bold;
            font-size: 15px;
        }
        
        .admin-navbar-info p:last-child {
            font-size: 12px;
            opacity: 0.9;
        }
        .admin-main {
            display: grid;
            grid-template-columns: 260px 1fr;
            flex: 1;
            overflow: hidden;
        }
        
        /* Sidebar */
        .admin-sidebar {
            background: #c5d8d6;
            overflow-y: auto;
            padding: 20px 16px;
            display: flex;
            flex-direction: column;
            gap: 12px;
        }
        
        .admin-sidebar-item {
            padding: 16px 20px;
            background: white;
            border: none;
            border-radius: 6px;
            color: #333;
            cursor: pointer;
            font-family: 'Nunito', sans-serif;
            font-size: 15px;
            font-weight: 600;
            transition: all 0.2s;
            text-align: center;
            display: block;
            text-decoration: none;
        }
        
        .admin-sidebar-item:hover {
            background: #f0f0f0;
        }
        
        .admin-sidebar-item.active {
            background: #9db3b0;
            color: white;
            font-weight: 700;
        }
        
        .admin-sidebar-logout {
            margin-top: auto;
            padding: 16px 20px;
            background: #a8968f;
            border: none;
            border-radius: 6px;
            color: white;
            cursor: pointer;
            font-family: 'Nunito', sans-serif;
            font-weight: 600;
            font-size: 15px;
            transition: all 0.2s;
        }
        
        .admin-sidebar-logout:hover {
            background: #9d8b82;
        }
        
        /* Content area */
        .admin-content {
            background: white;
            overflow-y: auto;
            padding: 40px;
            display: flex;
            flex-direction: column;
        }
        
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
            margin-bottom: 16px;
        }
        
        .admin-stat-label {
            font-size: 14px;
            font-weight: 600;
            color: #1E2A28;
        }
        
        /* Footer */
        .admin-footer {
            background: #8FA8A6;
            padding: 12px 20px;
            text-align: center;
            color: white;
            font-size: 13px;
            font-weight: 600;
            line-height: 1.4;
        }
    </style>
</head>
<body>
    <div class="admin-page">
        
        <!-- Navbar -->
        <nav class="admin-navbar">
            <div class="admin-navbar-logo">
                <img src="{{ asset('img/LogoUsarDiaDia.png') }}" alt="Logo Moveet">
            </div>
            
            <div class="admin-navbar-search">
                <input type="text" placeholder="Escribe algo...">
            </div>
            
            <div class="admin-navbar-user">
                <div class="admin-navbar-avatar">{{ substr(Auth::user()->name ?? 'A', 0, 1) }}</div>
                <div class="admin-navbar-info">
                    <p>{{ Auth::user()->name ?? 'Admin' }}</p>
                    <p>usuario administrador</p>
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
                <a href="{{ route('admin.dashboard') }}" class="admin-sidebar-item @if(Route::currentRouteName() == 'admin.dashboard') active @endif">Dashboard</a>
                <a href="{{ route('admin.usuarios') }}" class="admin-sidebar-item @if(Route::currentRouteName() == 'admin.usuarios' || strpos(Route::currentRouteName(), 'admin.usuarios.') === 0) active @endif">Usuarios</a>
                <a href="{{ route('admin.misiones') }}" class="admin-sidebar-item @if(Route::currentRouteName() == 'admin.misiones' || strpos(Route::currentRouteName(), 'admin.misiones.') === 0) active @endif">Misiones</a>
                <a href="{{ route('admin.eventos') }}" class="admin-sidebar-item @if(Route::currentRouteName() == 'admin.eventos' || strpos(Route::currentRouteName(), 'admin.eventos.') === 0) active @endif">Eventos</a>
                <a href="#" class="admin-sidebar-item">Pase de paseo</a>
                <a href="#" class="admin-sidebar-item">Lugares</a>
                <a href="{{ route('admin.recompensas') }}" class="admin-sidebar-item @if(Route::currentRouteName() == 'admin.recompensas' || strpos(Route::currentRouteName(), 'admin.recompensas.') === 0) active @endif">Recompensas</a>
                <a href="{{ route('admin.tienda') }}" class="admin-sidebar-item @if(Route::currentRouteName() == 'admin.tienda' || strpos(Route::currentRouteName(), 'admin.tienda.') === 0) active @endif">Tienda</a>
            </aside>
            
            <!-- Content -->
            <div class="admin-content">
                <div class="admin-stats-grid">
                    <div class="admin-stat-card">
                        <div class="admin-stat-number">{{ $total_usuarios }}</div>
                        <div class="admin-stat-label">Total usuarios</div>
                    </div>
                    <div class="admin-stat-card">
                        <div class="admin-stat-number">{{ $total_misiones }}</div>
                        <div class="admin-stat-label">Total misiones</div>
                    </div>
                    <div class="admin-stat-card">
                        <div class="admin-stat-number">{{ $total_eventos }}</div>
                        <div class="admin-stat-label">Total eventos</div>
                    </div>
                    <div class="admin-stat-card">
                        <div class="admin-stat-number">{{ $total_paseos }}</div>
                        <div class="admin-stat-label">Pase de paseo</div>
                    </div>
                    <div class="admin-stat-card">
                        <div class="admin-stat-number">{{ $total_lugares }}</div>
                        <div class="admin-stat-label">Total lugares</div>
                    </div>
                    <div class="admin-stat-card">
                        <div class="admin-stat-number">{{ $total_recompensas }}</div>
                        <div class="admin-stat-label">Recompensas</div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Footer -->
        <footer class="admin-footer">
            Moveet<br>
            @2025<br>
            www.moveet.es
        </footer>
        
    </div>
</body>
</html>
