@extends('layouts.admin')

@section('title', 'Recompensas - Admin')

@section('content')
    <div style="display: flex; flex-direction: column; gap: 22px;">
        <div style="display: flex; justify-content: space-between; align-items: flex-end; gap: 18px; flex-wrap: wrap;">
            <div>
                <h1 style="font-size: 2rem; font-weight: 800; margin: 0; letter-spacing: -0.02em;">RECOMPENSAS</h1>
                <p style="color: #666; font-weight: 600; margin: 8px 0 0; max-width: 780px;">Aquí defines la recompensa completa. Después, en la tienda, decides si se muestra al público y si exige premium.</p>
            </div>
            <div style="display: flex; gap: 10px; flex-wrap: wrap;">
                <a href="{{ route('admin.tienda') }}" style="background: white; color: #8FA8A6; padding: 12px 24px; border-radius: 10px; text-decoration: none; font-weight: 700; border: 1px solid #8FA8A6;">TIENDA</a>
                <a href="{{ route('admin.recompensas.crear') }}" style="background: linear-gradient(135deg, #8FA8A6, #7a9a98); color: white; padding: 12px 24px; border-radius: 10px; text-decoration: none; font-weight: 800; box-shadow: 0 8px 18px rgba(143,168,166,.25);">CREAR RECOMPENSA</a>
            </div>
        </div>

        @if(session('success'))
            <div style="background: #e6f4ea; border: 1px solid #c5e1ca; color: #1e5b36; padding: 12px 16px; border-radius: 10px; font-weight: 600;">
                {{ session('success') }}
            </div>
        @endif

        @if($recompensas->isEmpty())
            <div style="background: #f7f8f8; border: 1px dashed #c8d2d0; border-radius: 14px; padding: 40px; text-align: center; color: #667;">
                <p style="margin: 0; font-weight: 700; font-size: 1.05rem;">No hay recompensas registradas.</p>
                <p style="margin: 8px 0 0;">Crea una nueva recompensa para empezar a poblar la tienda y el pase.</p>
            </div>
        @else
            <div style="display: flex; gap: 12px; align-items: center; justify-content: space-between; flex-wrap: wrap; margin-bottom: 14px; padding: 14px 16px; background: #f7f8f8; border: 1px solid #d8e1df; border-radius: 12px;">
                <div style="display: flex; gap: 8px; align-items: center; flex-wrap: wrap;">
                    <input id="recompensa-search" type="search" placeholder="Buscar recompensa..." style="padding: 10px 14px; min-width: 240px; border: 1px solid #c9d3d1; border-radius: 10px; background: white;">
                    <span style="color: #667; font-size: 13px; font-weight: 600;">Filtra por nombre, tipo o descripción</span>
                </div>
                <button type="button" id="recompensa-clear" style="background: white; color: #1E2A28; padding: 10px 14px; border-radius: 10px; border: 1px solid #c9d3d1; font-weight: 700; cursor: pointer;">Limpiar filtro</button>
            </div>

            <div style="overflow-x: auto; border: 1px solid #d8e1df; border-radius: 14px; overflow: hidden; box-shadow: 0 10px 30px rgba(30,42,40,.06);">
                <table style="width: 100%; border-collapse: collapse; background: white;">
                    <thead>
                        <tr style="background: linear-gradient(135deg, #8FA8A6, #7c9896); color: white;">
                            <th style="padding: 14px; text-align: left; font-weight: 700;">Recompensa</th>
                            <th style="padding: 14px; text-align: left; font-weight: 700; width: 120px;">Tipo</th>
                            <th style="padding: 14px; text-align: center; font-weight: 700; width: 110px;">Premium</th>
                            <th style="padding: 14px; text-align: center; font-weight: 700; width: 130px;">Visible tienda</th>
                            <th style="padding: 14px; text-align: center; font-weight: 700; width: 110px;">Puntos</th>
                            <th style="padding: 14px; text-align: center; font-weight: 700; width: 120px;">Acciones</th>
                        </tr>
                    </thead>
                    <tbody id="recompensa-table-body">
                        @foreach($recompensas as $recompensa)
                            <tr data-search="{{ strtolower($recompensa->nombre . ' ' . $recompensa->descripcion . ' ' . $recompensa->tipo) }}" style="border-bottom: 1px solid #e6eceb; transition: background 0.18s ease;">
                                <td style="padding: 14px;">
                                    <div style="font-weight: 800; color: #1E2A28;">{{ $recompensa->nombre }}</div>
                                    <div style="color: #667; font-size: 12px; margin-top: 4px; max-width: 48rem;">{{ $recompensa->descripcion }}</div>
                                </td>
                                <td style="padding: 14px; text-transform: capitalize; font-weight: 700; color: #2d4744;">{{ str_replace('_', ' ', $recompensa->tipo) }}</td>
                                <td style="padding: 14px; text-align: center;">
                                    <span style="display: inline-flex; align-items: center; justify-content: center; min-width: 58px; padding: 7px 10px; border-radius: 999px; background: {{ $recompensa->premium ? '#f2e4b7' : '#eef4f3' }}; color: #1E2A28; font-weight: 800;">{{ $recompensa->premium ? 'Sí, restringida' : 'No' }}</span>
                                </td>
                                <td style="padding: 14px; text-align: center;">
                                    <span style="display: inline-flex; align-items: center; justify-content: center; min-width: 82px; padding: 7px 10px; border-radius: 999px; background: {{ $recompensa->visible_en_tienda ? '#d7e8df' : '#eceff1' }}; color: #1E2A28; font-weight: 800;">{{ $recompensa->visible_en_tienda ? 'Visible' : 'Oculta' }}</span>
                                </td>
                                <td style="padding: 14px; text-align: center; font-weight: 800; color: #1E2A28;">{{ $recompensa->puntos_necesarios }}</td>
                                <td style="padding: 14px; text-align: center; white-space: nowrap;">
                                    <a href="{{ route('admin.recompensas.editar', $recompensa) }}" style="display: inline-flex; align-items: center; justify-content: center; width: 38px; height: 38px; border-radius: 10px; background: #eef4f3; color: #1E2A28; text-decoration: none; margin-right: 8px;" title="Editar">
                                        <i class="fas fa-pen"></i>
                                    </a>
                                    <a href="{{ route('admin.recompensas.eliminar', $recompensa) }}" style="display: inline-flex; align-items: center; justify-content: center; width: 38px; height: 38px; border-radius: 10px; background: #fff1f1; color: #b00020; text-decoration: none;" title="Eliminar">
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

    @push('scripts')
        <script>
            (function () {
                const search = document.getElementById('recompensa-search');
                const clear = document.getElementById('recompensa-clear');
                const rows = Array.from(document.querySelectorAll('#recompensa-table-body tr'));

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