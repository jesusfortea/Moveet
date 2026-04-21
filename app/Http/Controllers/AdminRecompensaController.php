<?php

namespace App\Http\Controllers;

use App\Models\Recompensa;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AdminRecompensaController extends Controller
{
    public function index(): View
    {
        $recompensas = Recompensa::query()
            ->orderBy('tipo')
            ->orderBy('puntos_necesarios')
            ->get();

        return view('admin.recompensas.index', compact('recompensas'));
    }

    public function crear(): View
    {
        return view('admin.recompensas.crear');
    }

    public function guardar(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'nombre' => ['required', 'string', 'max:255'],
            'descripcion' => ['required', 'string', 'max:255'],
            'tipo' => ['required', 'in:tienda,pase_de_paseo'],
            'puntos_necesarios' => ['required', 'integer', 'min:0'],
            'nivel_necesario' => ['required', 'integer', 'min:1'],
            'ruta_imagen' => ['required', 'string', 'max:255'],
        ]);

        Recompensa::create([
            'nombre' => $validated['nombre'],
            'descripcion' => $validated['descripcion'],
            'tipo' => $validated['tipo'],
            'puntos_necesarios' => $validated['puntos_necesarios'],
            'nivel_necesario' => $validated['nivel_necesario'],
            'ruta_imagen' => $validated['ruta_imagen'],
            'premium' => $request->boolean('premium'),
            'visible_en_tienda' => $request->boolean('visible_en_tienda'),
        ]);

        return redirect()->route('admin.recompensas')->with('success', 'Recompensa creada correctamente.');
    }

    public function editar(Recompensa $recompensa): View
    {
        return view('admin.recompensas.editar', compact('recompensa'));
    }

    public function actualizar(Request $request, Recompensa $recompensa): RedirectResponse
    {
        $validated = $request->validate([
            'nombre' => ['required', 'string', 'max:255'],
            'descripcion' => ['required', 'string', 'max:255'],
            'tipo' => ['required', 'in:tienda,pase_de_paseo'],
            'puntos_necesarios' => ['required', 'integer', 'min:0'],
            'nivel_necesario' => ['required', 'integer', 'min:1'],
            'ruta_imagen' => ['required', 'string', 'max:255'],
        ]);

        $recompensa->update([
            'nombre' => $validated['nombre'],
            'descripcion' => $validated['descripcion'],
            'tipo' => $validated['tipo'],
            'puntos_necesarios' => $validated['puntos_necesarios'],
            'nivel_necesario' => $validated['nivel_necesario'],
            'ruta_imagen' => $validated['ruta_imagen'],
            'premium' => $request->boolean('premium'),
            'visible_en_tienda' => $request->boolean('visible_en_tienda'),
        ]);

        return redirect()->route('admin.recompensas')->with('success', 'Recompensa actualizada correctamente.');
    }

    public function eliminar(Recompensa $recompensa): View
    {
        return view('admin.recompensas.eliminar', compact('recompensa'));
    }

    public function confirmarEliminar(Recompensa $recompensa): RedirectResponse
    {
        $recompensa->delete();

        return redirect()->route('admin.recompensas')->with('success', 'Recompensa eliminada correctamente.');
    }
}