@extends('layouts.admin')

@section('title', 'Eliminar Producto')

@section('content')
    <div style="display: flex; flex-direction: column; align-items: center; width: 100%; padding: 20px 0;">
        <div style="width: 90%; max-width: 700px;">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 30px; gap: 16px; flex-wrap: wrap;">
                <h1 style="font-size: 2rem; font-weight: bold; margin: 0;">ELIMINAR PRODUCTO</h1>
                <a href="{{ route('admin.tienda') }}" style="background: #8FA8A6; color: white; padding: 12px 24px; border-radius: 6px; text-decoration: none; font-weight: 600; transition: all 0.2s;" onmouseover="this.style.background='#7a9a98'" onmouseout="this.style.background='#8FA8A6'">
                    VOLVER ATRÁS
                </a>
            </div>

            <div style="background: #f8f1f1; border: 2px solid #cc0000; padding: 25px; border-radius: 8px;">
                <p style="margin-top: 0; font-size: 15px; color: #600;">Vas a eliminar este producto de la tienda:</p>
                <div style="background: white; padding: 16px; border-radius: 6px; margin-bottom: 18px;">
                    <div style="font-weight: 700; font-size: 18px; margin-bottom: 8px;">{{ $producto->nombre }}</div>
                    <div style="color: #666; margin-bottom: 6px;">{{ $producto->descripcion }}</div>
                    <div style="color: #333; font-size: 13px;">Puntos: {{ $producto->puntos_necesarios }} | Nivel: {{ $producto->nivel_necesario }} | Premium: {{ $producto->premium ? 'Sí' : 'No' }}</div>
                </div>

                <form action="{{ route('admin.tienda.confirmar-eliminar', $producto) }}" method="POST">
                    @csrf
                    @method('DELETE')
                    <button type="submit" style="width: 100%; background: #cc0000; color: white; padding: 12px 20px; border: none; border-radius: 6px; font-weight: 700; cursor: pointer; font-size: 14px;" onmouseover="this.style.background='#990000'" onmouseout="this.style.background='#cc0000'">
                        Sí, eliminar producto
                    </button>
                </form>
            </div>
        </div>
    </div>
@endsection