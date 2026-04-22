@extends('layouts.admin')

@section('title', 'Pase de Paseo - Admin')

@section('content')
    <div style="display: flex; flex-direction: column; align-items: center; width: 100%; padding: 20px 0;">
        <div style="width: 90%; max-width: 1000px;">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 30px;">
                <h1 style="font-size: 2rem; font-weight: bold; margin: 0;">PASE DE PASEO</h1>
                <a href="{{ route('admin.pase_paseo.crear') }}" style="background: #8FA8A6; color: white; padding: 12px 24px; border-radius: 6px; text-decoration: none; font-weight: 600; transition: all 0.2s;" onmouseover="this.style.background='#7a9a98'" onmouseout="this.style.background='#8FA8A6'">
                    + CREAR PASE
                </a>
            </div>

            @if (session('success'))
                <div style="background: #d4edda; border: 2px solid #28a745; color: #155724; padding: 15px; border-radius: 6px; margin-bottom: 20px; font-size: 13px; font-weight: 600;">
                  {{ session('success') }}
                </div>
            @endif

            @if ($pasespaseo->count() > 0)
                <div style="background: white; border-radius: 6px; overflow: hidden; box-shadow: 0 2px 4px rgba(0,0,0,0.1);">
                    <table style="width: 100%; border-collapse: collapse;">
                        <thead>
                            <tr style="background: #8FA8A6; color: white;">
                                <th style="padding: 15px 12px; text-align: left; font-weight: 600; font-size: 13px; border-bottom: 2px solid #7a9a98;">Nombre</th>
                                <th style="padding: 15px 12px; text-align: left; font-weight: 600; font-size: 13px; border-bottom: 2px solid #7a9a98;">Ruta de Imagen</th>
                                <th style="padding: 15px 12px; text-align: center; font-weight: 600; font-size: 13px; border-bottom: 2px solid #7a9a98;">Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($pasespaseo as $pase)
                                <tr style="border-bottom: 1px solid #ddd;">
                                    <td style="padding: 15px 12px; font-weight: 600; font-size: 13px; color: #333;">{{ $pase->nombre }}</td>
                                    <td style="padding: 15px 12px; font-size: 13px; color: #666;">{{ Str::limit($pase->ruta_imagen, 40, '...') }}</td>
                                    <td style="padding: 15px 12px; text-align: center;">
                                        <a href="{{ route('admin.pase_paseo.editar', $pase) }}" style="color: #8FA8A6; text-decoration: none; margin-right: 12px; font-size: 14px;" title="Editar">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <a href="{{ route('admin.pase_paseo.eliminar', $pase) }}" style="color: #c00; text-decoration: none; font-size: 14px;" title="Eliminar">
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
                    <p style="margin: 0; font-size: 14px;">No hay pases de paseo registrados.</p>
                </div>
            @endif
        </div>
    </div>
@endsection
