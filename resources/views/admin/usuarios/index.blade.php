@extends('layouts.admin')

@section('title', 'Usuarios - Admin')

@section('content')
    <div style="padding: 0;">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 30px;">
            <h1 style="font-size: 2rem; font-weight: bold; margin: 0;">USUARIOS</h1>
            <a href="{{ route('admin.usuarios.crear') }}" style="background: #8FA8A6; color: white; padding: 12px 24px; border-radius: 6px; text-decoration: none; font-weight: 600; transition: all 0.2s;" onmouseover="this.style.background='#7a9a98'" onmouseout="this.style.background='#8FA8A6'">
                CREAR USUARIO
            </a>
        </div>

        @if(session('success'))
            <div style="background: #d4edda; border: 1px solid #c3e6cb; color: #155724; padding: 12px 16px; border-radius: 6px; margin-bottom: 20px;">
                {{ session('success') }}
            </div>
        @endif

        @if(session('error'))
            <div style="background: #fee; border: 1px solid #f5c2c7; color: #842029; padding: 12px 16px; border-radius: 6px; margin-bottom: 20px;">
                {{ session('error') }}
            </div>
        @endif

        @if($usuarios->isEmpty())
            <p style="text-align: center; color: #999; padding: 40px;">No hay usuarios registrados.</p>
        @else
            <div style="overflow-x: auto;">
                <table style="width: 100%; border-collapse: collapse;">
                    <thead>
                        <tr style="background: #8FA8A6; color: white;">
                            <th style="border: 1px solid #ddd; padding: 12px; text-align: left; font-weight: 600;">Nombre</th>
                            <th style="border: 1px solid #ddd; padding: 12px; text-align: left; font-weight: 600;">Correo</th>
                            <th style="border: 1px solid #ddd; padding: 12px; text-align: left; font-weight: 600;">Fecha de nacimiento</th>
                            <th style="border: 1px solid #ddd; padding: 12px; text-align: left; font-weight: 600;">DNI</th>
                            <th style="border: 1px solid #ddd; padding: 12px; text-align: left; font-weight: 600;">Teléfono</th>
                            <th style="border: 1px solid #ddd; padding: 12px; text-align: center; font-weight: 600;">Premium</th>
                            <th style="border: 1px solid #ddd; padding: 12px; text-align: center; font-weight: 600;">Estado</th>
                            <th style="border: 1px solid #ddd; padding: 12px; text-align: center; font-weight: 600;">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($usuarios as $usuario)
                            <tr style="border-bottom: 1px solid #ddd;">
                                <td style="border: 1px solid #ddd; padding: 12px;">{{ $usuario->name }}</td>
                                <td style="border: 1px solid #ddd; padding: 12px;">{{ $usuario->email }}</td>
                                <td style="border: 1px solid #ddd; padding: 12px;">
                                    {{ $usuario->nacimiento ? $usuario->nacimiento->format('d/m/Y') : 'N/A' }}
                                </td>
                                <td style="border: 1px solid #ddd; padding: 12px;">{{ $usuario->dni }}</td>
                                <td style="border: 1px solid #ddd; padding: 12px;">{{ $usuario->telefono }}</td>
                                <td style="border: 1px solid #ddd; padding: 12px; text-align: center;">
                                    @if($usuario->premium)
                                        <span style="background: #d4edda; color: #155724; padding: 5px 10px; border-radius: 999px; font-size: 12px; font-weight: 700;">Sí</span>
                                    @else
                                        <span style="background: #f3f3f3; color: #666; padding: 5px 10px; border-radius: 999px; font-size: 12px; font-weight: 700;">No</span>
                                    @endif
                                </td>
                                <td style="border: 1px solid #ddd; padding: 12px; text-align: center;">
                                    @if($usuario->is_blocked)
                                        <span style="background: #f8d7da; color: #842029; padding: 5px 10px; border-radius: 999px; font-size: 12px; font-weight: 700;">Bloqueado</span>
                                    @else
                                        <span style="background: #d1e7dd; color: #0f5132; padding: 5px 10px; border-radius: 999px; font-size: 12px; font-weight: 700;">Activo</span>
                                    @endif
                                </td>
                                <td style="border: 1px solid #ddd; padding: 12px; text-align: center;">
                                    <a href="{{ route('admin.usuarios.editar', $usuario) }}" style="color: #0066cc; text-decoration: none; margin-right: 12px;">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    @if(auth()->id() !== $usuario->id)
                                        <form method="POST" action="{{ route('admin.usuarios.toggle-bloqueo', $usuario) }}" style="display: inline-block; margin-right: 12px;" data-swal-confirm data-swal-confirm-title="Confirmar bloqueo" data-swal-confirm-message="{{ $usuario->is_blocked ? '¿Seguro que quieres desbloquear a este usuario?' : '¿Seguro que quieres bloquear a este usuario?' }}">
                                            @csrf
                                            @method('PATCH')
                                            <button type="submit" style="border: none; background: transparent; color: {{ $usuario->is_blocked ? '#0f5132' : '#842029' }}; cursor: pointer;" title="{{ $usuario->is_blocked ? 'Desbloquear usuario' : 'Bloquear usuario' }}">
                                                <i class="fas {{ $usuario->is_blocked ? 'fa-unlock' : 'fa-ban' }}"></i>
                                            </button>
                                        </form>
                                    @endif
                                    <a href="{{ route('admin.usuarios.eliminar', $usuario) }}" style="color: #cc0000; text-decoration: none;">
                                        <i class="fas fa-trash"></i>
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>
@endsection
