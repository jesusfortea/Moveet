<?php

namespace App\Http\Controllers;

use App\Models\Evento;
use Illuminate\Http\Request;

class AdminEventoController extends Controller
{
    public function index()
    {
        $eventos = Evento::orderBy('nombre')->get();
        return view('admin.eventos.index', compact('eventos'));
    }

    public function crear()
    {
        return view('admin.eventos.crear');
    }

    public function guardar(Request $request)
    {
        $validated = $request->validate([
            'nombre' => 'required|string|regex:/^[\pL\d\s\-]+$/u|min:3|max:255',
            'descripcion' => 'required|string|min:10|max:1000',
            'ejeX' => 'required|numeric|regex:/^-?\d+(\.\d+)?$/|between:-180,180',
            'ejeY' => 'required|numeric|regex:/^-?\d+(\.\d+)?$/|between:-90,90',
            'direccion' => 'required|string|min:5|max:500',
            'fecha_inicio' => 'required|date|date_format:Y-m-d|after:2024-01-01',
            'fecha_fin' => 'required|date|date_format:Y-m-d|after_or_equal:fecha_inicio',
        ], [
            'nombre.required' => 'El nombre es obligatorio',
            'nombre.min' => 'El nombre debe tener al menos 3 caracteres',
            'nombre.max' => 'El nombre no puede exceder 255 caracteres',
            'descripcion.required' => 'La descripción es obligatoria',
            'descripcion.min' => 'La descripción debe tener al menos 10 caracteres',
            'descripcion.max' => 'La descripción no puede exceder 1000 caracteres',
            'ejeX.required' => 'La coordenada X es obligatoria',
            'ejeX.numeric' => 'La coordenada X debe ser un número',
            'ejeX.between' => 'La coordenada X debe estar entre -180 y 180',
            'ejeY.required' => 'La coordenada Y es obligatoria',
            'ejeY.numeric' => 'La coordenada Y debe ser un número',
            'ejeY.between' => 'La coordenada Y debe estar entre -90 y 90',
            'direccion.required' => 'La dirección es obligatoria',
            'direccion.min' => 'La dirección debe tener al menos 5 caracteres',
            'direccion.max' => 'La dirección no puede exceder 500 caracteres',
            'fecha_inicio.required' => 'La fecha de inicio es obligatoria',
            'fecha_inicio.date_format' => 'El formato de fecha de inicio debe ser YYYY-MM-DD',
            'fecha_inicio.after' => 'La fecha de inicio debe ser posterior al 2024-01-01',
            'fecha_fin.required' => 'La fecha de fin es obligatoria',
            'fecha_fin.date_format' => 'El formato de fecha de fin debe ser YYYY-MM-DD',
            'fecha_fin.after_or_equal' => 'La fecha de fin debe ser igual o posterior a la fecha de inicio',
            'nombre.regex' => 'El nombre solo puede contener letras, números, espacios y guiones',
            'ejeX.regex' => 'La coordenada X debe ser un número decimal válido (ej. 2.123)',
            'ejeY.regex' => 'La coordenada Y debe ser un número decimal válido (ej. 41.123)',
        ]);

        Evento::create($validated);

        return redirect()->route('admin.eventos')->with('success', 'Evento creado exitosamente');
    }

    public function editar(Evento $evento)
    {
        return view('admin.eventos.editar', compact('evento'));
    }

    public function actualizar(Request $request, Evento $evento)
    {
        $validated = $request->validate([
            'nombre' => 'required|string|regex:/^[\pL\d\s\-]+$/u|min:3|max:255',
            'descripcion' => 'required|string|min:10|max:1000',
            'ejeX' => 'required|numeric|regex:/^-?\d+(\.\d+)?$/|between:-180,180',
            'ejeY' => 'required|numeric|regex:/^-?\d+(\.\d+)?$/|between:-90,90',
            'direccion' => 'required|string|min:5|max:500',
            'fecha_inicio' => 'required|date|date_format:Y-m-d|after:2024-01-01',
            'fecha_fin' => 'required|date|date_format:Y-m-d|after_or_equal:fecha_inicio',
        ], [
            'nombre.required' => 'El nombre es obligatorio',
            'nombre.min' => 'El nombre debe tener al menos 3 caracteres',
            'nombre.max' => 'El nombre no puede exceder 255 caracteres',
            'descripcion.required' => 'La descripción es obligatoria',
            'descripcion.min' => 'La descripción debe tener al menos 10 caracteres',
            'descripcion.max' => 'La descripción no puede exceder 1000 caracteres',
            'ejeX.required' => 'La coordenada X es obligatoria',
            'ejeX.numeric' => 'La coordenada X debe ser un número',
            'ejeX.between' => 'La coordenada X debe estar entre -180 y 180',
            'ejeY.required' => 'La coordenada Y es obligatoria',
            'ejeY.numeric' => 'La coordenada Y debe ser un número',
            'ejeY.between' => 'La coordenada Y debe estar entre -90 y 90',
            'direccion.required' => 'La dirección es obligatoria',
            'direccion.min' => 'La dirección debe tener al menos 5 caracteres',
            'direccion.max' => 'La dirección no puede exceder 500 caracteres',
            'fecha_inicio.required' => 'La fecha de inicio es obligatoria',
            'fecha_inicio.date_format' => 'El formato de fecha de inicio debe ser YYYY-MM-DD',
            'fecha_inicio.after' => 'La fecha de inicio debe ser posterior al 2024-01-01',
            'fecha_fin.required' => 'La fecha de fin es obligatoria',
            'fecha_fin.date_format' => 'El formato de fecha de fin debe ser YYYY-MM-DD',
            'fecha_fin.after_or_equal' => 'La fecha de fin debe ser igual o posterior a la fecha de inicio',
            'nombre.regex' => 'El nombre solo puede contener letras, números, espacios y guiones',
            'ejeX.regex' => 'La coordenada X debe ser un número decimal válido (ej. 2.123)',
            'ejeY.regex' => 'La coordenada Y debe ser un número decimal válido (ej. 41.123)',
        ]);

        $evento->update($validated);

        return redirect()->route('admin.eventos')->with('success', 'Evento actualizado exitosamente');
    }

    public function eliminar(Evento $evento)
    {
        return view('admin.eventos.eliminar', compact('evento'));
    }

    public function confirmarEliminar(Evento $evento)
    {
        $evento->delete();
        return redirect()->route('admin.eventos')->with('success', 'Evento eliminado exitosamente');
    }
}
