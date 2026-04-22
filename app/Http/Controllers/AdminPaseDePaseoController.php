<?php

namespace App\Http\Controllers;

use App\Models\PaseDePaseo;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;

class AdminPaseDePaseoController extends Controller
{
    public function index(): View
    {
        $pasespaseo = PaseDePaseo::orderBy('nombre')->get();
        return view('admin.pase_de_paseo.index', compact('pasespaseo'));
    }

    public function crear(): View
    {
        return view('admin.pase_de_paseo.crear');
    }

    public function guardar(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'nombre' => 'required|string|min:3|max:255',
            'ruta_imagen' => 'required|string|min:5|max:500',
        ], [
            'nombre.required' => 'El nombre es obligatorio',
            'nombre.min' => 'El nombre debe tener al menos 3 caracteres',
            'nombre.max' => 'El nombre no puede exceder 255 caracteres',
            'ruta_imagen.required' => 'La ruta de imagen es obligatoria',
            'ruta_imagen.min' => 'La ruta de imagen debe tener al menos 5 caracteres',
            'ruta_imagen.max' => 'La ruta de imagen no puede exceder 500 caracteres',
        ]);

        PaseDePaseo::create($validated);

        return redirect()->route('admin.pase_paseo')->with('success', 'Pase de Paseo creado exitosamente');
    }

    public function editar(PaseDePaseo $pasedepaseo): View
    {
        return view('admin.pase_de_paseo.editar', ['pasedepaseo' => $pasedepaseo]);
    }

    public function actualizar(Request $request, PaseDePaseo $pasedepaseo): RedirectResponse
    {
        $validated = $request->validate([
            'nombre' => 'required|string|min:3|max:255',
            'ruta_imagen' => 'required|string|min:5|max:500',
        ], [
            'nombre.required' => 'El nombre es obligatorio',
            'nombre.min' => 'El nombre debe tener al menos 3 caracteres',
            'nombre.max' => 'El nombre no puede exceder 255 caracteres',
            'ruta_imagen.required' => 'La ruta de imagen es obligatoria',
            'ruta_imagen.min' => 'La ruta de imagen debe tener al menos 5 caracteres',
            'ruta_imagen.max' => 'La ruta de imagen no puede exceder 500 caracteres',
        ]);

        $pasedepaseo->update($validated);

        return redirect()->route('admin.pase_paseo')->with('success', 'Pase de Paseo actualizado exitosamente');
    }

    public function eliminar(PaseDePaseo $pasedepaseo): View
    {
        return view('admin.pase_de_paseo.eliminar', ['pasedepaseo' => $pasedepaseo]);
    }

    public function confirmarEliminar(PaseDePaseo $pasedepaseo): RedirectResponse
    {
        $pasedepaseo->delete();
        return redirect()->route('admin.pase_paseo')->with('success', 'Pase de Paseo eliminado exitosamente');
    }
}
