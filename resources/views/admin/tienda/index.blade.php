@extends('layouts.admin')

@section('title', 'Tienda - Admin')

@section('content')
    <div style="padding: 0;">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 30px;">
            <h1 style="font-size: 2rem; font-weight: bold; margin: 0;">TIENDA</h1>
        </div>

        @if(session('success'))
            <div style="background: #d4edda; border: 1px solid #c3e6cb; color: #155724; padding: 12px 16px; border-radius: 6px; margin-bottom: 20px;">
                {{ session('success') }}
            </div>
        @endif

        @if($productos->isEmpty())
            <p style="text-align: center; color: #999; padding: 40px;">No hay productos de tienda registrados.</p>
        @else
            <form action="{{ route('admin.tienda.actualizar') }}" method="POST">
                @csrf
                @method('PATCH')

                <div class="admin-responsive-table" style="overflow-x: auto;">
                    <table style="width: 100%; border-collapse: collapse;">
                        <thead>
                            <tr style="background: #8FA8A6; color: white;">
                                <th style="border: 1px solid #ddd; padding: 12px; text-align: left; font-weight: 600;">Producto</th>
                                <th style="border: 1px solid #ddd; padding: 12px; text-align: left; font-weight: 600;">DescripciÃ³n</th>
                                <th style="border: 1px solid #ddd; padding: 12px; text-align: center; font-weight: 600;">Puntos</th>
                                <th style="border: 1px solid #ddd; padding: 12px; text-align: center; font-weight: 600;">Nivel</th>
                                <th style="border: 1px solid #ddd; padding: 12px; text-align: center; font-weight: 600;">Visible</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($productos as $producto)
                                <tr style="border-bottom: 1px solid #ddd;">
                                    <td data-label="Producto" style="border: 1px solid #ddd; padding: 12px;">{{ $producto->nombre }}</td>
                                    <td data-label="Descripcion" style="border: 1px solid #ddd; padding: 12px;">{{ Str::limit($producto->descripcion, 50, '...') }}</td>
                                    <td data-label="Puntos" style="border: 1px solid #ddd; padding: 12px; text-align: center;">{{ $producto->puntos_necesarios }}</td>
                                    <td data-label="Nivel" style="border: 1px solid #ddd; padding: 12px; text-align: center;">{{ $producto->nivel_necesario }}</td>
                                    <td data-label="Visible" style="border: 1px solid #ddd; padding: 12px; text-align: center;">
                                        <label style="display: inline-flex; align-items: center; gap: 8px; cursor: pointer; font-weight: 600; flex-wrap: wrap; justify-content: center;">
                                            <input type="checkbox" name="visible_en_tienda[]" value="{{ $producto->id }}" {{ $producto->visible_en_tienda ? 'checked' : '' }}>
                                            <span>{{ $producto->visible_en_tienda ? 'Visible' : 'Oculto' }}</span>
                                        </label>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <div style="display: flex; justify-content: flex-end; margin-top: 20px;">
                    <button type="submit" style="background: #8FA8A6; color: white; padding: 12px 24px; border: none; border-radius: 6px; font-weight: 600; cursor: pointer; transition: all 0.2s;" onmouseover="this.style.background='#7a9a98'" onmouseout="this.style.background='#8FA8A6'">
                        GUARDAR CAMBIOS
                    </button>
                </div>
            </form>
        @endif
    </div>
@endsection
