<?php

namespace App\Http\Controllers;

use App\Models\Lugar;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;

class AdminLugarController extends Controller
{
    public function index(): View
    {
        $lugares = Lugar::orderBy('nombre')->get();
        return view('admin.lugares.index', compact('lugares'));
    }

    public function crear(): View
    {
        return view('admin.lugares.crear');
    }

    public function guardar(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'nombre' => 'required|string|min:3|max:255',
            'ejeX' => 'required|numeric|between:-180,180',
            'ejeY' => 'required|numeric|between:-90,90',
        ], [
            'nombre.required' => 'El nombre es obligatorio',
            'nombre.min' => 'El nombre debe tener al menos 3 caracteres',
            'nombre.max' => 'El nombre no puede exceder 255 caracteres',
            'ejeX.required' => 'La coordenada X es obligatoria',
            'ejeX.numeric' => 'La coordenada X debe ser un número',
            'ejeX.between' => 'La coordenada X debe estar entre -180 y 180',
            'ejeY.required' => 'La coordenada Y es obligatoria',
            'ejeY.numeric' => 'La coordenada Y debe ser un número',
            'ejeY.between' => 'La coordenada Y debe estar entre -90 y 90',
        ]);

        Lugar::create($validated);

        return redirect()->route('admin.lugares')->with('success', 'Lugar creado exitosamente');
    }

    public function editar(Lugar $lugar): View
    {
        return view('admin.lugares.editar', compact('lugar'));
    }

    public function actualizar(Request $request, Lugar $lugar): RedirectResponse
    {
        $validated = $request->validate([
            'nombre' => 'required|string|min:3|max:255',
            'ejeX' => 'required|numeric|between:-180,180',
            'ejeY' => 'required|numeric|between:-90,90',
        ], [
            'nombre.required' => 'El nombre es obligatorio',
            'nombre.min' => 'El nombre debe tener al menos 3 caracteres',
            'nombre.max' => 'El nombre no puede exceder 255 caracteres',
            'ejeX.required' => 'La coordenada X es obligatoria',
            'ejeX.numeric' => 'La coordenada X debe ser un número',
            'ejeX.between' => 'La coordenada X debe estar entre -180 y 180',
            'ejeY.required' => 'La coordenada Y es obligatoria',
            'ejeY.numeric' => 'La coordenada Y debe ser un número',
            'ejeY.between' => 'La coordenada Y debe estar entre -90 y 90',
        ]);

        $lugar->update($validated);

        return redirect()->route('admin.lugares')->with('success', 'Lugar actualizado exitosamente');
    }

    public function eliminar(Lugar $lugar): View
    {
        return view('admin.lugares.eliminar', compact('lugar'));
    }

    public function confirmarEliminar(Lugar $lugar): RedirectResponse
    {
        $lugar->delete();
        return redirect()->route('admin.lugares')->with('success', 'Lugar eliminado exitosamente');
    }
}
