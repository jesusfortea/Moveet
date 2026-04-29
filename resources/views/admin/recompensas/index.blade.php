@extends('layouts.admin')

@section('title', 'Recompensas - Admin')

@section('content')
    <div style="padding: 0;">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 30px; gap: 10px; flex-wrap: wrap;">
            <h1 style="font-size: 2rem; font-weight: bold; margin: 0;">RECOMPENSAS</h1>
            <div style="display: flex; gap: 10px;">
                <a href="{{ route('admin.tienda') }}" style="background: white; color: #8FA8A6; padding: 12px 24px; border-radius: 6px; text-decoration: none; font-weight: 600; border: 1px solid #8FA8A6;">
                    TIENDA
                </a>
                <a href="{{ route('admin.recompensas.crear') }}" style="background: #8FA8A6; color: white; padding: 12px 24px; border-radius: 6px; text-decoration: none; font-weight: 600; transition: all 0.2s;" onmouseover="this.style.background='#7a9a98'" onmouseout="this.style.background='#8FA8A6'">
                    CREAR RECOMPENSA
                </a>
            </div>
        </div>

        @if(session('success'))
            <div style="background: #d4edda; border: 1px solid #c3e6cb; color: #155724; padding: 12px 16px; border-radius: 6px; margin-bottom: 20px;">
                {{ session('success') }}
            </div>
        @endif

        @if($recompensas->isEmpty())
            <p style="text-align: center; color: #999; padding: 40px;">No hay recompensas registradas.</p>
        @else
            <div class="admin-responsive-table" style="overflow-x: auto;">
                <table style="width: 100%; border-collapse: collapse;">
                    <thead>
                        <tr style="background: #8FA8A6; color: white;">
                            <th style="border: 1px solid #ddd; padding: 12px; text-align: left; font-weight: 600;">Nombre</th>
                            <th style="border: 1px solid #ddd; padding: 12px; text-align: left; font-weight: 600;">Descripción</th>
                            <th style="border: 1px solid #ddd; padding: 12px; text-align: left; font-weight: 600;">Tipo</th>
                            <th style="border: 1px solid #ddd; padding: 12px; text-align: center; font-weight: 600;">Premium</th>
                            <th style="border: 1px solid #ddd; padding: 12px; text-align: center; font-weight: 600;">Visible tienda</th>
                            <th style="border: 1px solid #ddd; padding: 12px; text-align: center; font-weight: 600;">Puntos</th>
                            <th style="border: 1px solid #ddd; padding: 12px; text-align: center; font-weight: 600;">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($recompensas as $recompensa)
                            <tr style="border-bottom: 1px solid #ddd;">
                                <td data-label="Nombre" style="border: 1px solid #ddd; padding: 12px;">{{ $recompensa->nombre }}</td>
                                <td data-label="Descripcion" style="border: 1px solid #ddd; padding: 12px;">{{ Str::limit($recompensa->descripcion, 40, '...') }}</td>
                                <td data-label="Tipo" style="border: 1px solid #ddd; padding: 12px; text-transform: capitalize;">{{ str_replace('_', ' ', $recompensa->tipo) }}</td>
                                <td data-label="Premium" style="border: 1px solid #ddd; padding: 12px; text-align: center;">
                                    @if($recompensa->premium)
                                        <span style="background: #ffc107; color: #000; padding: 4px 8px; border-radius: 3px; font-size: 11px; font-weight: 600;">Sí</span>
                                    @else
                                        <span style="background: #e0e0e0; color: #666; padding: 4px 8px; border-radius: 3px; font-size: 11px;">NO</span>
                                    @endif
                                </td>
                                <td data-label="Visible tienda" style="border: 1px solid #ddd; padding: 12px; text-align: center;">
                                    @if($recompensa->visible_en_tienda)
                                        <span style="background: #d4edda; color: #155724; padding: 4px 8px; border-radius: 3px; font-size: 11px; font-weight: 600;">VISIBLE</span>
                                    @else
                                        <span style="background: #f3f3f3; color: #666; padding: 4px 8px; border-radius: 3px; font-size: 11px;">OCULTA</span>
                                    @endif
                                </td>
                                <td data-label="Puntos" style="border: 1px solid #ddd; padding: 12px; text-align: center;">{{ $recompensa->puntos_necesarios }}</td>
                                <td data-label="Acciones" style="border: 1px solid #ddd; padding: 12px; text-align: center; white-space: nowrap;">
                                    <a href="{{ route('admin.recompensas.editar', $recompensa) }}" style="color: #0066cc; text-decoration: none; margin-right: 12px;">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <a href="{{ route('admin.recompensas.eliminar', $recompensa) }}" style="color: #cc0000; text-decoration: none;">
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
