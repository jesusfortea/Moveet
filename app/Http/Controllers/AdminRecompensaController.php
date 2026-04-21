<?php

namespace App\Http\Controllers;

use App\Models\Recompensa;
use App\Models\PaseDePaseo;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;

class AdminRecompensaController extends Controller
{
    public function index(): View
    {
        $recompensas = Recompensa::with('paseDePaseo')->orderBy('nombre')->get();
        return view('admin.recompensas.index', compact('recompensas'));
    }

    public function crear(): View
    {
        $pases = PaseDePaseo::orderBy('nombre')->get();
        return view('admin.recompensas.crear', compact('pases'));
    }

    public function guardar(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'pase_de_paseo_id' => 'required|exists:pase_de_paseo,id',
            'nombre' => 'required|string|min:3|max:255',
            'descripcion' => 'required|string|min:10|max:1000',
            'premium' => 'nullable|boolean',
            'puntos_necesarios' => 'required|integer|min:1|max:999999',
            'nivel_necesario' => 'required|integer|min:1|max:100',
            'ruta_imagen' => 'required|string|min:5|max:500',
            'tipo' => 'required|string|in:normal,especial,legendaria',
        ], [
            'pase_de_paseo_id.required' => 'El pase de paseo es obligatorio',
            'pase_de_paseo_id.exists' => 'El pase de paseo seleccionado no existe',
            'nombre.required' => 'El nombre es obligatorio',
            'nombre.min' => 'El nombre debe tener al menos 3 caracteres',
            'nombre.max' => 'El nombre no puede exceder 255 caracteres',
            'descripcion.required' => 'La descripción es obligatoria',
            'descripcion.min' => 'La descripción debe tener al menos 10 caracteres',
            'descripcion.max' => 'La descripción no puede exceder 1000 caracteres',
            'puntos_necesarios.required' => 'Los puntos necesarios son obligatorios',
            'puntos_necesarios.integer' => 'Los puntos deben ser un número entero',
            'puntos_necesarios.min' => 'Los puntos deben ser al menos 1',
            'puntos_necesarios.max' => 'Los puntos no pueden exceder 999999',
            'nivel_necesario.required' => 'El nivel necesario es obligatorio',
            'nivel_necesario.integer' => 'El nivel debe ser un número entero',
            'nivel_necesario.min' => 'El nivel debe ser al menos 1',
            'nivel_necesario.max' => 'El nivel no puede exceder 100',
            'ruta_imagen.required' => 'La ruta de imagen es obligatoria',
            'ruta_imagen.min' => 'La ruta de imagen debe tener al menos 5 caracteres',
            'ruta_imagen.max' => 'La ruta de imagen no puede exceder 500 caracteres',
            'tipo.required' => 'El tipo de recompensa es obligatorio',
            'tipo.in' => 'El tipo debe ser: normal, especial o legendaria',
        ]);

        Recompensa::create($validated);

        return redirect()->route('admin.recompensas')->with('success', 'Recompensa creada exitosamente');
    }

    public function editar(Recompensa $recompensa): View
    {
        $pases = PaseDePaseo::orderBy('nombre')->get();
        return view('admin.recompensas.editar', compact('recompensa', 'pases'));
    }

    public function actualizar(Request $request, Recompensa $recompensa): RedirectResponse
    {
        $validated = $request->validate([
            'pase_de_paseo_id' => 'required|exists:pase_de_paseo,id',
            'nombre' => 'required|string|min:3|max:255',
            'descripcion' => 'required|string|min:10|max:1000',
            'premium' => 'nullable|boolean',
            'puntos_necesarios' => 'required|integer|min:1|max:999999',
            'nivel_necesario' => 'required|integer|min:1|max:100',
            'ruta_imagen' => 'required|string|min:5|max:500',
            'tipo' => 'required|string|in:normal,especial,legendaria',
        ], [
            'pase_de_paseo_id.required' => 'El pase de paseo es obligatorio',
            'pase_de_paseo_id.exists' => 'El pase de paseo seleccionado no existe',
            'nombre.required' => 'El nombre es obligatorio',
            'nombre.min' => 'El nombre debe tener al menos 3 caracteres',
            'nombre.max' => 'El nombre no puede exceder 255 caracteres',
            'descripcion.required' => 'La descripción es obligatoria',
            'descripcion.min' => 'La descripción debe tener al menos 10 caracteres',
            'descripcion.max' => 'La descripción no puede exceder 1000 caracteres',
            'puntos_necesarios.required' => 'Los puntos necesarios son obligatorios',
            'puntos_necesarios.integer' => 'Los puntos deben ser un número entero',
            'puntos_necesarios.min' => 'Los puntos deben ser al menos 1',
            'puntos_necesarios.max' => 'Los puntos no pueden exceder 999999',
            'nivel_necesario.required' => 'El nivel necesario es obligatorio',
            'nivel_necesario.integer' => 'El nivel debe ser un número entero',
            'nivel_necesario.min' => 'El nivel debe ser al menos 1',
            'nivel_necesario.max' => 'El nivel no puede exceder 100',
            'ruta_imagen.required' => 'La ruta de imagen es obligatoria',
            'ruta_imagen.min' => 'La ruta de imagen debe tener al menos 5 caracteres',
            'ruta_imagen.max' => 'La ruta de imagen no puede exceder 500 caracteres',
            'tipo.required' => 'El tipo de recompensa es obligatorio',
            'tipo.in' => 'El tipo debe ser: normal, especial o legendaria',
        ]);

        $recompensa->update($validated);

        return redirect()->route('admin.recompensas')->with('success', 'Recompensa actualizada exitosamente');
    }

    public function eliminar(Recompensa $recompensa): View
    {
        return view('admin.recompensas.eliminar', compact('recompensa'));
    }

    public function confirmarEliminar(Recompensa $recompensa): RedirectResponse
    {
        $recompensa->delete();
        return redirect()->route('admin.recompensas')->with('success', 'Recompensa eliminada exitosamente');
    }
}
