<?php

namespace App\Http\Controllers;

use App\Models\PaseDePaseo;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Storage;

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
            'ruta_imagen' => 'required|image|mimes:jpg,jpeg,png,webp|max:2048',
        ], [
            'nombre.required' => 'El nombre es obligatorio',
            'nombre.min' => 'El nombre debe tener al menos 3 caracteres',
            'nombre.max' => 'El nombre no puede exceder 255 caracteres',
            'ruta_imagen.required' => 'La imagen del pase es obligatoria',
            'ruta_imagen.image' => 'El archivo seleccionado debe ser una imagen valida',
            'ruta_imagen.mimes' => 'La imagen debe estar en formato jpg, jpeg, png o webp',
            'ruta_imagen.max' => 'La imagen no puede superar los 2MB',
        ]);

        $validated['ruta_imagen'] = 'storage/' . $request->file('ruta_imagen')->store('pase-paseo', 'public');

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
            'ruta_imagen' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
        ], [
            'nombre.required' => 'El nombre es obligatorio',
            'nombre.min' => 'El nombre debe tener al menos 3 caracteres',
            'nombre.max' => 'El nombre no puede exceder 255 caracteres',
            'ruta_imagen.image' => 'El archivo seleccionado debe ser una imagen valida',
            'ruta_imagen.mimes' => 'La imagen debe estar en formato jpg, jpeg, png o webp',
            'ruta_imagen.max' => 'La imagen no puede superar los 2MB',
        ]);

        if ($request->hasFile('ruta_imagen')) {
            if ($pasedepaseo->ruta_imagen && str_starts_with($pasedepaseo->ruta_imagen, 'storage/')) {
                $oldPath = substr($pasedepaseo->ruta_imagen, strlen('storage/'));
                Storage::disk('public')->delete($oldPath);
            }

            $validated['ruta_imagen'] = 'storage/' . $request->file('ruta_imagen')->store('pase-paseo', 'public');
        } else {
            unset($validated['ruta_imagen']);
        }

        $pasedepaseo->update($validated);

        return redirect()->route('admin.pase_paseo')->with('success', 'Pase de Paseo actualizado exitosamente');
    }

    public function eliminar(PaseDePaseo $pasedepaseo): View
    {
        return view('admin.pase_de_paseo.eliminar', ['pasedepaseo' => $pasedepaseo]);
    }

    public function confirmarEliminar(PaseDePaseo $pasedepaseo): RedirectResponse
    {
        if ($pasedepaseo->ruta_imagen && str_starts_with($pasedepaseo->ruta_imagen, 'storage/')) {
            $oldPath = substr($pasedepaseo->ruta_imagen, strlen('storage/'));
            Storage::disk('public')->delete($oldPath);
        }

        $pasedepaseo->delete();
        return redirect()->route('admin.pase_paseo')->with('success', 'Pase de Paseo eliminado exitosamente');
    }
}
