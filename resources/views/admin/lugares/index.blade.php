@extends('layouts.admin')

@section('title', 'Lugares - Admin')

@section('content')
    <div style="padding: 0;">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 30px;">
            <h1 style="font-size: 2rem; font-weight: bold; margin: 0;">LUGARES</h1>
            <a href="{{ route('admin.lugares.crear') }}" style="background: #8FA8A6; color: white; padding: 12px 24px; border-radius: 6px; text-decoration: none; font-weight: 600; transition: all 0.2s;" onmouseover="this.style.background='#7a9a98'" onmouseout="this.style.background='#8FA8A6'">
                CREAR LUGAR
            </a>
        </div>

        @if (session('success'))
            <div style="background: #d4edda; border: 1px solid #c3e6cb; color: #155724; padding: 12px 16px; border-radius: 6px; margin-bottom: 20px;">
                {{ session('success') }}
            </div>
        @endif

        @if ($lugares->count() > 0)
            <div class="admin-responsive-table" style="overflow-x: auto;">
                <table style="width: 100%; border-collapse: collapse;">
                    <thead>
                        <tr style="background: #8FA8A6; color: white;">
                            <th style="border: 1px solid #ddd; padding: 12px; text-align: left; font-weight: 600;">Nombre</th>
                            <th style="border: 1px solid #ddd; padding: 12px; text-align: left; font-weight: 600;">Coordenada X</th>
                            <th style="border: 1px solid #ddd; padding: 12px; text-align: left; font-weight: 600;">Coordenada Y</th>
                            <th style="border: 1px solid #ddd; padding: 12px; text-align: center; font-weight: 600;">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($lugares as $lugar)
                            <tr style="border-bottom: 1px solid #ddd;">
                                <td data-label="Nombre" style="border: 1px solid #ddd; padding: 12px;">{{ $lugar->nombre }}</td>
                                <td data-label="Coordenada X" style="border: 1px solid #ddd; padding: 12px;">{{ number_format($lugar->ejeX, 2) }}</td>
                                <td data-label="Coordenada Y" style="border: 1px solid #ddd; padding: 12px;">{{ number_format($lugar->ejeY, 2) }}</td>
                                <td data-label="Acciones" style="border: 1px solid #ddd; padding: 12px; text-align: center;">
                                    <a href="{{ route('admin.lugares.editar', $lugar) }}" style="color: #0066cc; text-decoration: none; margin-right: 12px;" title="Editar">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <a href="{{ route('admin.lugares.eliminar', $lugar) }}" style="color: #cc0000; text-decoration: none;" title="Eliminar">
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
                <p style="margin: 0; font-size: 14px;">No hay lugares registrados.</p>
            </div>
        @endif
    </div>
@endsection
