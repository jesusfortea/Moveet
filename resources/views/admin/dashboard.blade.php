<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Admin - Moveet</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif;
            background-color: #f5f5f5;
        }

        .container {
            display: flex;
            height: 100vh;
        }

        .sidebar {
            width: 200px;
            background: linear-gradient(135deg, #a8c5d6 0%, #7fa3b8 100%);
            padding: 20px;
            display: flex;
            flex-direction: column;
        }

        .logo {
            width: 60px;
            height: 60px;
            background: white;
            border-radius: 8px;
            margin-bottom: 30px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: bold;
            color: #7fa3b8;
        }

        .nav-menu {
            display: flex;
            flex-direction: column;
            gap: 10px;
            flex: 1;
        }

        .nav-item {
            padding: 12px 15px;
            background: rgba(255, 255, 255, 0.2);
            border: none;
            border-radius: 6px;
            color: white;
            cursor: pointer;
            text-align: left;
            font-size: 14px;
            transition: all 0.3s ease;
        }

        .nav-item:hover {
            background: rgba(255, 255, 255, 0.4);
            transform: translateX(5px);
        }

        .nav-item.active {
            background: white;
            color: #7fa3b8;
            font-weight: 600;
        }

        .logout-btn {
            padding: 12px 15px;
            background: rgba(255, 100, 100, 0.3);
            border: 1px solid rgba(255, 100, 100, 0.5);
            border-radius: 6px;
            color: white;
            cursor: pointer;
            font-size: 14px;
            transition: all 0.3s ease;
        }

        .logout-btn:hover {
            background: rgba(255, 100, 100, 0.5);
        }

        .main-content {
            flex: 1;
            padding: 30px;
            overflow-y: auto;
        }

        .header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 30px;
            background: white;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }

        .search-box {
            flex: 1;
            max-width: 300px;
        }

        .search-box input {
            width: 100%;
            padding: 10px 15px;
            border: 1px solid #ddd;
            border-radius: 4px;
            font-size: 14px;
        }

        .user-info {
            display: flex;
            align-items: center;
            gap: 15px;
        }

        .avatar {
            width: 40px;
            height: 40px;
            background: #7fa3b8;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-weight: bold;
        }

        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
        }

        .stat-card {
            background: white;
            padding: 25px;
            border-radius: 8px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
            text-align: center;
            transition: transform 0.3s ease;
        }

        .stat-card:hover {
            transform: translateY(-5px);
        }

        .stat-number {
            font-size: 32px;
            font-weight: bold;
            color: #7fa3b8;
            margin-bottom: 10px;
        }

        .stat-label {
            font-size: 14px;
            color: #666;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
    </style>
</head>
<body>
    <div class="container">
        <!-- Sidebar -->
        <div class="sidebar">
            <div class="logo">M</div>
            
            <div class="nav-menu">
                <button class="nav-item active">Dashboard</button>
                <button class="nav-item">Usuarios</button>
                <button class="nav-item">Misiones</button>
                <button class="nav-item">Eventos</button>
                <button class="nav-item">Pase de paseo</button>
                <button class="nav-item">Lugares</button>
                <button class="nav-item">Recompensas</button>
            </div>

            <form action="{{ route('logout') }}" method="POST">
                @csrf
                <button type="submit" class="logout-btn">Salir</button>
            </form>
        </div>

        <!-- Main Content -->
        <div class="main-content">
            <!-- Header -->
            <div class="header">
                <div class="search-box">
                    <input type="text" placeholder="Escribe algo...">
                </div>
                <div class="user-info">
                    <div class="avatar">A</div>
                    <div>
                        <div style="font-weight: 600; font-size: 14px;">{{ Auth::user()->name }}</div>
                        <div style="font-size: 12px; color: #999;">usuario.administrador</div>
                    </div>
                </div>
            </div>

            <!-- Stats Grid -->
            <div class="stats-grid">
                <div class="stat-card">
                    <div class="stat-number">{{ $total_usuarios }}</div>
                    <div class="stat-label">Total usuarios</div>
                </div>
                <div class="stat-card">
                    <div class="stat-number">{{ $total_misiones }}</div>
                    <div class="stat-label">Total misiones</div>
                </div>
                <div class="stat-card">
                    <div class="stat-number">{{ $total_eventos }}</div>
                    <div class="stat-label">Total eventos</div>
                </div>
                <div class="stat-card">
                    <div class="stat-number">{{ $total_paseos }}</div>
                    <div class="stat-label">Pase de paseo</div>
                </div>
                <div class="stat-card">
                    <div class="stat-number">{{ $total_lugares }}</div>
                    <div class="stat-label">Total lugares</div>
                </div>
                <div class="stat-card">
                    <div class="stat-number">{{ $total_recompensas }}</div>
                    <div class="stat-label">Recompensas</div>
                </div>
            </div>
        </div>
    </div>

    <footer style="text-align: center; padding: 20px; color: #999; font-size: 12px;">
        <strong>Moveet</strong><br>
        @2025<br>
        www.moveet.es
    </footer>
</body>
</html>
