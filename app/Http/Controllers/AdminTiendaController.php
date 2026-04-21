<?php

namespace App\Http\Controllers;

use App\Models\Recompensa;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AdminTiendaController extends Controller
{
    private function assertProductoTienda(Recompensa $recompensa): Recompensa
    {
        abort_unless($recompensa->tipo === 'tienda', 404);

        return $recompensa;
    }

    public function index(): View
    {
        $productos = Recompensa::query()
            ->where('tipo', 'tienda')
            ->orderBy('puntos_necesarios')
            ->get();

        return view('admin.tienda.index', compact('productos'));
    }

    public function actualizar(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'visible_en_tienda' => ['nullable', 'array'],
            'visible_en_tienda.*' => ['integer'],
        ]);

        $productos = Recompensa::query()
            ->where('tipo', 'tienda')
            ->get();

        $productosVisibles = array_map('intval', $request->input('visible_en_tienda', []));

        foreach ($productos as $producto) {
            $producto->update([
                'visible_en_tienda' => in_array($producto->id, $productosVisibles, true),
            ]);
        }

        return redirect()->route('admin.tienda')->with('success', 'Productos actualizados correctamente.');
    }
}