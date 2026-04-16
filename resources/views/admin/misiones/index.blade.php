@extends('layouts.admin')

@section('title', 'Misiones - Admin')

@section('content')
    <div style="padding: 0;">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 30px;">
            <h1 style="font-size: 2rem; font-weight: bold; margin: 0;">MISIONES</h1>
            <a href="{{ route('admin.misiones.crear') }}" style="background: #8FA8A6; color: white; padding: 12px 24px; border-radius: 6px; text-decoration: none; font-weight: 600; transition: all 0.2s;" onmouseover="this.style.background='#7a9a98'" onmouseout="this.style.background='#8FA8A6'">
                CREAR MISIÓN
            </a>
        </div>

        @if(session('success'))
            <div style="background: #d4edda; border: 1px solid #c3e6cb; color: #155724; padding: 12px 16px; border-radius: 6px; margin-bottom: 20px;">
                {{ session('success') }}
            </div>
        @endif

        @if($misiones->isEmpty())
            <p style="text-align: center; color: #999; padding: 40px;">No hay misiones registradas.</p>
        @else
            <div style="overflow-x: auto;">
                <table style="width: 100%; border-collapse: collapse;">
                    <thead>
                        <tr style="background: #8FA8A6; color: white;">
                            <th style="border: 1px solid #ddd; padding: 12px; text-align: left; font-weight: 600;">Nombre</th>
                            <th style="border: 1px solid #ddd; padding: 12px; text-align: left; font-weight: 600;">Descripción</th>
                            <th style="border: 1px solid #ddd; padding: 12px; text-align: left; font-weight: 600;">Evento</th>
                            <th style="border: 1px solid #ddd; padding: 12px; text-align: center; font-weight: 600;">Metros</th>
                            <th style="border: 1px solid #ddd; padding: 12px; text-align: center; font-weight: 600;">Puntos</th>
                            <th style="border: 1px solid #ddd; padding: 12px; text-align: center; font-weight: 600;">Premium</th>
                            <th style="border: 1px solid #ddd; padding: 12px; text-align: center; font-weight: 600;">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($misiones as $mision)
                            <tr style="border-bottom: 1px solid #ddd;">
                                <td style="border: 1px solid #ddd; padding: 12px;">{{ $mision->nombre }}</td>
                                <td style="border: 1px solid #ddd; padding: 12px;">{{ substr($mision->descripcion, 0, 40) }}...</td>
                                <td style="border: 1px solid #ddd; padding: 12px;">{{ $mision->evento->nombre ?? 'N/A' }}</td>
                                <td style="border: 1px solid #ddd; padding: 12px; text-align: center;">{{ $mision->metros_requeridos }}</td>
                                <td style="border: 1px solid #ddd; padding: 12px; text-align: center;">{{ $mision->puntos }}</td>
                                <td style="border: 1px solid #ddd; padding: 12px; text-align: center;">
                                    @if($mision->premium)
                                        <span style="background: #ffc107; color: #000; padding: 4px 8px; border-radius: 3px; font-size: 11px; font-weight: 600;">SÍ</span>
                                    @else
                                        <span style="background: #e0e0e0; color: #666; padding: 4px 8px; border-radius: 3px; font-size: 11px;">NO</span>
                                    @endif
                                </td>
                                <td style="border: 1px solid #ddd; padding: 12px; text-align: center;">
                                    <a href="{{ route('admin.misiones.editar', $mision) }}" style="color: #0066cc; text-decoration: none; margin-right: 12px;">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <a href="{{ route('admin.misiones.eliminar', $mision) }}" style="color: #cc0000; text-decoration: none;">
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
