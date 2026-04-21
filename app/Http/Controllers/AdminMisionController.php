<?php

namespace App\Http\Controllers;

use App\Models\Mision;
use App\Models\Evento;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;

class AdminMisionController extends Controller
{
    public function index(): View
    {
        $misiones = Mision::with('evento')->get();
        return view('admin.misiones.index', compact('misiones'));
    }

    public function crear(): View
    {
        $eventos = Evento::all();
        return view('admin.misiones.crear', compact('eventos'));
    }

    public function guardar(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'evento_id' => ['required', 'exists:eventos,id'],
            'nombre' => ['required', 'string', 'regex:/^[\pL\d\s\-]+$/u', 'min:3', 'max:255'],
            'descripcion' => ['required', 'string', 'min:10', 'max:1000'],
            'metros_requeridos' => ['required', 'integer', 'min:10', 'max:50000'],
            'ejeX' => ['required', 'numeric', 'regex:/^-?\d+(\.\d+)?$/', 'between:-180,180'],
            'ejeY' => ['required', 'numeric', 'regex:/^-?\d+(\.\d+)?$/', 'between:-90,90'],
            'direccion' => ['required', 'string', 'min:5', 'max:500'],
            'puntos' => ['required', 'integer', 'min:1', 'max:2000'],
            'premium' => ['nullable', 'boolean'],
            'semanal' => ['nullable', 'boolean'],
        ], [
            'evento_id.required' => 'Debes seleccionar un evento',
            'evento_id.exists' => 'El evento seleccionado no existe',
            'nombre.required' => 'El nombre de la misión es obligatorio',
            'nombre.min' => 'El nombre debe tener al menos 3 caracteres',
            'nombre.max' => 'El nombre no puede exceder 255 caracteres',
            'descripcion.required' => 'La descripción es obligatoria',
            'descripcion.min' => 'La descripción debe tener al menos 10 caracteres',
            'descripcion.max' => 'La descripción no puede exceder 1000 caracteres',
            'metros_requeridos.required' => 'Los metros requeridos son obligatorios',
            'metros_requeridos.integer' => 'Los metros deben ser un número entero',
            'metros_requeridos.min' => 'Los metros deben ser al menos 1',
            'metros_requeridos.max' => 'Los metros no pueden exceder 99.999',
            'ejeX.required' => 'La coordenada X es obligatoria',
            'ejeX.numeric' => 'La coordenada X debe ser un número',
            'ejeX.between' => 'La coordenada X debe estar entre -180 y 180',
            'ejeY.required' => 'La coordenada Y es obligatoria',
            'ejeY.numeric' => 'La coordenada Y debe ser un número',
            'ejeY.between' => 'La coordenada Y debe estar entre -90 y 90',
            'direccion.required' => 'La dirección es obligatoria',
            'direccion.min' => 'La dirección debe tener al menos 5 caracteres',
            'direccion.max' => 'La dirección no puede exceder 500 caracteres',
            'puntos.required' => 'Los puntos son obligatorios',
            'puntos.integer' => 'Los puntos deben ser un número entero',
            'puntos.min' => 'Los puntos deben ser al menos 1',
            'puntos.max' => 'Los puntos no pueden exceder 2.000',
            'metros_requeridos.min' => 'Los metros deben ser al menos 10',
            'metros_requeridos.max' => 'Los metros no pueden exceder 50.000',
            'nombre.regex' => 'El nombre solo puede contener letras, números, espacios y guiones',
            'ejeX.regex' => 'La coordenada X debe ser un número decimal válido',
            'ejeY.regex' => 'La coordenada Y debe ser un número decimal válido',
        ]);

        Mision::create($validated);
        return redirect()->route('admin.misiones')->with('success', 'Misión creada exitosamente');
    }

    public function editar(Mision $mision): View
    {
        $eventos = Evento::all();
        return view('admin.misiones.editar', compact('mision', 'eventos'));
    }

    public function actualizar(Request $request, Mision $mision): RedirectResponse
    {
        $validated = $request->validate([
            'evento_id' => ['required', 'exists:eventos,id'],
            'nombre' => ['required', 'string', 'regex:/^[\pL\d\s\-]+$/u', 'min:3', 'max:255'],
            'descripcion' => ['required', 'string', 'min:10', 'max:1000'],
            'metros_requeridos' => ['required', 'integer', 'min:10', 'max:50000'],
            'ejeX' => ['required', 'numeric', 'regex:/^-?\d+(\.\d+)?$/', 'between:-180,180'],
            'ejeY' => ['required', 'numeric', 'regex:/^-?\d+(\.\d+)?$/', 'between:-90,90'],
            'direccion' => ['required', 'string', 'min:5', 'max:500'],
            'puntos' => ['required', 'integer', 'min:1', 'max:2000'],
            'premium' => ['nullable', 'boolean'],
            'semanal' => ['nullable', 'boolean'],
        ], [
            'evento_id.required' => 'Debes seleccionar un evento',
            'evento_id.exists' => 'El evento seleccionado no existe',
            'nombre.required' => 'El nombre de la misión es obligatorio',
            'nombre.min' => 'El nombre debe tener al menos 3 caracteres',
            'nombre.max' => 'El nombre no puede exceder 255 caracteres',
            'descripcion.required' => 'La descripción es obligatoria',
            'descripcion.min' => 'La descripción debe tener al menos 10 caracteres',
            'descripcion.max' => 'La descripción no puede exceder 1000 caracteres',
            'metros_requeridos.required' => 'Los metros requeridos son obligatorios',
            'metros_requeridos.integer' => 'Los metros deben ser un número entero',
            'metros_requeridos.min' => 'Los metros deben ser al menos 1',
            'metros_requeridos.max' => 'Los metros no pueden exceder 99.999',
            'ejeX.required' => 'La coordenada X es obligatoria',
            'ejeX.numeric' => 'La coordenada X debe ser un número',
            'ejeX.between' => 'La coordenada X debe estar entre -180 y 180',
            'ejeY.required' => 'La coordenada Y es obligatoria',
            'ejeY.numeric' => 'La coordenada Y debe ser un número',
            'ejeY.between' => 'La coordenada Y debe estar entre -90 y 90',
            'direccion.required' => 'La dirección es obligatoria',
            'direccion.min' => 'La dirección debe tener al menos 5 caracteres',
            'direccion.max' => 'La dirección no puede exceder 500 caracteres',
            'puntos.required' => 'Los puntos son obligatorios',
            'puntos.integer' => 'Los puntos deben ser un número entero',
            'puntos.min' => 'Los puntos deben ser al menos 1',
            'puntos.max' => 'Los puntos no pueden exceder 2.000',
            'metros_requeridos.min' => 'Los metros deben ser al menos 10',
            'metros_requeridos.max' => 'Los metros no pueden exceder 50.000',
            'nombre.regex' => 'El nombre solo puede contener letras, números, espacios y guiones',
            'ejeX.regex' => 'La coordenada X debe ser un número decimal válido',
            'ejeY.regex' => 'La coordenada Y debe ser un número decimal válido',
        ]);

        $mision->update($validated);
        return redirect()->route('admin.misiones')->with('success', 'Misión actualizada exitosamente');
    }

    public function eliminar(Mision $mision): View
    {
        return view('admin.misiones.eliminar', compact('mision'));
    }

    public function confirmarEliminar(Mision $mision): RedirectResponse
    {
        $mision->delete();
        return redirect()->route('admin.misiones')->with('success', 'Misión eliminada exitosamente');
    }
}
