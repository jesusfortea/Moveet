@extends('layouts.admin')

@section('title', 'Recompensas - Admin')

@section('content')
    <div style="display: flex; flex-direction: column; align-items: center; width: 100%; padding: 20px 0;">
        <div style="width: 90%; max-width: 1200px;">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 30px;">
                <h1 style="font-size: 2rem; font-weight: bold; margin: 0;">RECOMPENSAS</h1>
                <a href="{{ route('admin.recompensas.crear') }}" style="background: #8FA8A6; color: white; padding: 12px 24px; border-radius: 6px; text-decoration: none; font-weight: 600; transition: all 0.2s;" onmouseover="this.style.background='#7a9a98'" onmouseout="this.style.background='#8FA8A6'">
                    + CREAR RECOMPENSA
                </a>
            </div>

            @if (session('success'))
                <div style="background: #d4edda; border: 2px solid #28a745; color: #155724; padding: 15px; border-radius: 6px; margin-bottom: 20px; font-size: 13px; font-weight: 600;">
                  {{ session('success') }}
                </div>
            @endif

            @if ($recompensas->count() > 0)
                <div style="background: white; border-radius: 6px; overflow: hidden; box-shadow: 0 2px 4px rgba(0,0,0,0.1); overflow-x: auto;">
                    <table style="width: 100%; border-collapse: collapse;">
                        <thead>
                            <tr style="background: #8FA8A6; color: white;">
                                <th style="padding: 15px 12px; text-align: left; font-weight: 600; font-size: 13px; border-bottom: 2px solid #7a9a98; white-space: nowrap;">Nombre</th>
                                <th style="padding: 15px 12px; text-align: left; font-weight: 600; font-size: 13px; border-bottom: 2px solid #7a9a98; white-space: nowrap;">Pase</th>
                                <th style="padding: 15px 12px; text-align: left; font-weight: 600; font-size: 13px; border-bottom: 2px solid #7a9a98; white-space: nowrap;">Puntos</th>
                                <th style="padding: 15px 12px; text-align: left; font-weight: 600; font-size: 13px; border-bottom: 2px solid #7a9a98; white-space: nowrap;">Nivel</th>
                                <th style="padding: 15px 12px; text-align: left; font-weight: 600; font-size: 13px; border-bottom: 2px solid #7a9a98; white-space: nowrap;">Tipo</th>
                                <th style="padding: 15px 12px; text-align: center; font-weight: 600; font-size: 13px; border-bottom: 2px solid #7a9a98; white-space: nowrap;">Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($recompensas as $recompensa)
                                <tr style="border-bottom: 1px solid #ddd;">
                                    <td style="padding: 15px 12px; font-weight: 600; font-size: 13px; color: #333;">{{ $recompensa->nombre }}</td>
                                    <td style="padding: 15px 12px; font-size: 13px; color: #666;">{{ $recompensa->paseDePaseo->nombre ?? 'N/A' }}</td>
                                    <td style="padding: 15px 12px; font-size: 13px; color: #666;">{{ number_format($recompensa->puntos_necesarios) }}</td>
                                    <td style="padding: 15px 12px; font-size: 13px; color: #666;">{{ $recompensa->nivel_necesario }}</td>
                                    <td style="padding: 15px 12px; font-size: 13px;">
                                        <span style="padding: 4px 8px; border-radius: 3px; font-weight: 600; 
                                            @if($recompensa->tipo === 'legendaria') background: #ffd700; color: #333; 
                                            @elseif($recompensa->tipo === 'especial') background: #9370db; color: white; 
                                            @else background: #999; color: white; @endif">
                                            {{ ucfirst($recompensa->tipo) }}
                                        </span>
                                    </td>
                                    <td style="padding: 15px 12px; text-align: center;">
                                        <a href="{{ route('admin.recompensas.editar', $recompensa) }}" style="color: #8FA8A6; text-decoration: none; margin-right: 12px; font-size: 14px;" title="Editar">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <a href="{{ route('admin.recompensas.eliminar', $recompensa) }}" style="color: #c00; text-decoration: none; font-size: 14px;" title="Eliminar">
                                            <i class="fas fa-trash"></i>
                                        </a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <div style="background: white; padding: 40px; border-radius: 6px; text-align: center; color: #666;">
                    <p style="margin: 0; font-size: 14px;">No hay recompensas registradas.</p>
                </div>
            @endif
        </div>
    </div>
@endsection
