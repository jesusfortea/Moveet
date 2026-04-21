@extends('layouts.admin')

@section('title', 'Tienda - Admin')

@section('content')
    <div style="display: flex; flex-direction: column; gap: 22px;">
        <div style="display: flex; justify-content: space-between; align-items: flex-end; gap: 18px; flex-wrap: wrap;">
            <div>
                <h1 style="font-size: 2rem; font-weight: 800; margin: 0; letter-spacing: -0.02em;">TIENDA</h1>
                <p style="color: #666; font-weight: 600; margin: 8px 0 0; max-width: 780px;">Usa esta pantalla para decidir qué recompensas se ven en la tienda. La creación y edición completa de recompensas está en su apartado propio.</p>
            </div>
        </div>

        @if(session('success'))
            <div style="background: #e6f4ea; border: 1px solid #c5e1ca; color: #1e5b36; padding: 12px 16px; border-radius: 10px; font-weight: 600;">
                {{ session('success') }}
            </div>
        @endif

        @if($productos->isEmpty())
            <div style="background: #f7f8f8; border: 1px dashed #c8d2d0; border-radius: 14px; padding: 40px; text-align: center; color: #667;">
                <p style="margin: 0; font-weight: 700; font-size: 1.05rem;">No hay productos de tienda registrados.</p>
            </div>
        @else
            <form action="{{ route('admin.tienda.actualizar') }}" method="POST">
                @csrf
                @method('PATCH')

                <div style="display: flex; gap: 12px; align-items: center; justify-content: space-between; flex-wrap: wrap; margin-bottom: 14px;">
                    <input id="tienda-search" type="search" placeholder="Buscar producto..." style="padding: 10px 14px; min-width: 240px; border: 1px solid #c9d3d1; border-radius: 10px; background: white;">
                    <button type="button" id="tienda-clear" style="background: white; color: #1E2A28; padding: 10px 14px; border-radius: 10px; border: 1px solid #c9d3d1; font-weight: 700; cursor: pointer;">Limpiar filtro</button>
                </div>

                <div style="overflow-x: auto; border: 1px solid #d8e1df; border-radius: 14px; overflow: hidden; box-shadow: 0 10px 30px rgba(30,42,40,.06);">
                    <table style="width: 100%; border-collapse: collapse; background: white;">
                        <thead>
                            <tr style="background: linear-gradient(135deg, #8FA8A6, #7c9896); color: white;">
                                <th style="padding: 14px; text-align: left; font-weight: 700;">Producto</th>
                                <th style="padding: 14px; text-align: center; font-weight: 700; width: 120px;">Puntos</th>
                                <th style="padding: 14px; text-align: center; font-weight: 700; width: 110px;">Nivel</th>
                                <th style="padding: 14px; text-align: center; font-weight: 700; width: 150px;">Visible</th>
                            </tr>
                        </thead>
                        <tbody id="tienda-table-body">
                            @foreach($productos as $producto)
                                <tr data-search="{{ strtolower($producto->nombre . ' ' . $producto->descripcion) }}" style="border-bottom: 1px solid #e6eceb; transition: background 0.18s ease;">
                                    <td style="padding: 14px;">
                                        <div style="display: flex; align-items: center; gap: 10px; flex-wrap: wrap;">
                                            <div style="width: 42px; height: 42px; border-radius: 12px; background: #eef4f3; display: flex; align-items: center; justify-content: center; color: #567; font-weight: 800;">R</div>
                                            <div>
                                                <div style="font-weight: 800; color: #1E2A28;">{{ $producto->nombre }}</div>
                                                <div style="color: #667; font-size: 12px; margin-top: 4px; max-width: 46rem;">{{ $producto->descripcion }}</div>
                                            </div>
                                        </div>
                                    </td>
                                    <td style="padding: 14px; text-align: center; font-weight: 800; color: #1E2A28;">{{ $producto->puntos_necesarios }}</td>
                                    <td style="padding: 14px; text-align: center;">
                                        <span style="display: inline-flex; align-items: center; justify-content: center; min-width: 62px; padding: 7px 10px; border-radius: 999px; background: #eef4f3; color: #2d4744; font-weight: 700;">Nvl {{ $producto->nivel_necesario }}</span>
                                    </td>
                                    <td style="padding: 14px; text-align: center;">
                                        <label style="display: inline-flex; align-items: center; gap: 8px; cursor: pointer; font-weight: 700; color: #1E2A28;">
                                            <input type="checkbox" name="visible_en_tienda[]" value="{{ $producto->id }}" {{ $producto->visible_en_tienda ? 'checked' : '' }}>
                                            <span>{{ $producto->visible_en_tienda ? 'Visible' : 'Oculto' }}</span>
                                        </label>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <div style="display: flex; justify-content: flex-end; margin-top: 18px;">
                    <button type="submit" style="background: linear-gradient(135deg, #8FA8A6, #7a9a98); color: white; padding: 12px 24px; border-radius: 10px; border: none; font-weight: 800; cursor: pointer; box-shadow: 0 8px 18px rgba(143,168,166,.25); transition: transform 0.15s ease;" onmouseover="this.style.transform='translateY(-1px)'" onmouseout="this.style.transform='translateY(0)'">
                        Guardar cambios
                    </button>
                </div>
            </form>
        @endif
    </div>

    @push('scripts')
        <script>
            (function () {
                const search = document.getElementById('tienda-search');
                const clear = document.getElementById('tienda-clear');
                const rows = Array.from(document.querySelectorAll('#tienda-table-body tr'));

                if (!search || !clear) {
                    return;
                }

                const applyFilter = () => {
                    const query = search.value.trim().toLowerCase();

                    rows.forEach((row) => {
                        const match = row.dataset.search.includes(query);
                        row.style.display = match ? '' : 'none';
                    });
                };

                search.addEventListener('input', applyFilter);
                clear.addEventListener('click', () => {
                    search.value = '';
                    applyFilter();
                    search.focus();
                });
            })();
        </script>
    @endpush
@endsection