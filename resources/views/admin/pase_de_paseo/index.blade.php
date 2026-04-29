@extends('layouts.admin')

@section('title', 'Pase de Paseo - Admin')

@section('content')
    <div style="padding: 0;">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 30px;">
            <h1 style="font-size: 2rem; font-weight: bold; margin: 0;">PASE DE PASEO</h1>
            <a href="{{ route('admin.pase_paseo.crear') }}" style="background: #8FA8A6; color: white; padding: 12px 24px; border-radius: 6px; text-decoration: none; font-weight: 600; transition: all 0.2s;" onmouseover="this.style.background='#7a9a98'" onmouseout="this.style.background='#8FA8A6'">
                CREAR PASE
            </a>
        </div>

        @if (session('success'))
            <div style="background: #d4edda; border: 1px solid #c3e6cb; color: #155724; padding: 12px 16px; border-radius: 6px; margin-bottom: 20px;">
                {{ session('success') }}
            </div>
        @endif

        @if ($pasespaseo->count() > 0)
            <div class="admin-responsive-table" style="overflow-x: auto;">
                <table style="width: 100%; border-collapse: collapse;">
                    <thead>
                        <tr style="background: #8FA8A6; color: white;">
                            <th style="border: 1px solid #ddd; padding: 12px; text-align: left; font-weight: 600;">Nombre</th>
                            <th style="border: 1px solid #ddd; padding: 12px; text-align: left; font-weight: 600;">Ruta de Imagen</th>
                            <th style="border: 1px solid #ddd; padding: 12px; text-align: center; font-weight: 600;">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($pasespaseo as $pase)
                            <tr style="border-bottom: 1px solid #ddd;">
                                <td data-label="Nombre" style="border: 1px solid #ddd; padding: 12px;">{{ $pase->nombre }}</td>
                                <td data-label="Ruta de Imagen" style="border: 1px solid #ddd; padding: 12px;">{{ Str::limit($pase->ruta_imagen, 40, '...') }}</td>
                                <td data-label="Acciones" style="border: 1px solid #ddd; padding: 12px; text-align: center;">
                                    <a href="{{ route('admin.pase_paseo.editar', $pase) }}" style="color: #0066cc; text-decoration: none; margin-right: 12px;" title="Editar">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <a href="{{ route('admin.pase_paseo.eliminar', $pase) }}" style="color: #cc0000; text-decoration: none;" title="Eliminar">
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
@endsection
