<?php

namespace App\Http\Controllers;

use App\Models\Pregunta;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class PreguntaController extends Controller
{
    public function index(): View
    {
        $preguntas = Pregunta::orderBy('estado', 'asc')
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return view('preguntas.index', [
            'preguntas' => $preguntas,
        ]);
    }

    public function create(): View
    {
        if (!Auth::check()) {
            abort(403, 'Debes estar logeado para hacer preguntas.');
        }

        return view('preguntas.create');
    }

    public function store(Request $request): RedirectResponse
    {
        if (!Auth::check()) {
            abort(403, 'Debes estar logeado para hacer preguntas.');
        }

        $validated = $request->validate([
            'titulo' => 'required|string|max:255',
            'contenido' => 'required|string|min:10|max:2000',
        ], [
            'titulo.required' => 'El título es obligatorio.',
            'titulo.max' => 'El título no puede exceder 255 caracteres.',
            'contenido.required' => 'El contenido es obligatorio.',
            'contenido.min' => 'La pregunta debe tener al menos 10 caracteres.',
            'contenido.max' => 'La pregunta no puede exceder 2000 caracteres.',
        ]);

        Pregunta::create([
            'user_id' => Auth::id(),
            'titulo' => $validated['titulo'],
            'contenido' => $validated['contenido'],
        ]);

        return redirect()->route('preguntas.index')
            ->with('success', '¡Pregunta enviada! El equipo la responderá pronto.');
    }

    public function show(Pregunta $pregunta): View
    {
        return view('preguntas.show', [
            'pregunta' => $pregunta,
        ]);
    }

    public function edit(Pregunta $pregunta): View
    {
        $user = Auth::user();

        if (!$user || ($pregunta->user_id !== $user->id && !$user->is_admin)) {
            abort(403, 'No tienes permiso para editar esta pregunta.');
        }

        return view('preguntas.edit', [
            'pregunta' => $pregunta,
        ]);
    }

    public function update(Request $request, Pregunta $pregunta): RedirectResponse
    {
        $user = Auth::user();

        if (!$user || ($pregunta->user_id !== $user->id && !$user->is_admin)) {
            abort(403, 'No tienes permiso para editar esta pregunta.');
        }

        if ($pregunta->estaRespondida() && $pregunta->user_id === $user->id) {
            abort(403, 'No puedes editar una pregunta que ya ha sido respondida.');
        }

        $validated = $request->validate([
            'titulo' => 'required|string|max:255',
            'contenido' => 'required|string|min:10|max:2000',
        ]);

        $pregunta->update($validated);

        return redirect()->route('preguntas.show', $pregunta)
            ->with('success', 'Pregunta actualizada correctamente.');
    }

    public function adminPanel(): View
    {
        $user = Auth::user();

        if (!$user || !$user->is_admin) {
            abort(403, 'Solo administradores pueden acceder a esta página.');
        }

        $pendientes = Pregunta::with('usuario')
            ->where('estado', 'pendiente')
            ->orderBy('created_at', 'asc')
            ->paginate(10, ['*'], 'pendientes_page');

        $respondidas = Pregunta::with('usuario', 'respondidaPor')
            ->where('estado', 'respondida')
            ->orderBy('updated_at', 'desc')
            ->paginate(10, ['*'], 'respondidas_page');

        return view('preguntas.admin-panel', compact('pendientes', 'respondidas'));
    }

    public function responder(Request $request, Pregunta $pregunta): RedirectResponse
    {
        $user = Auth::user();

        if (!$user || !$user->is_admin) {
            abort(403, 'Solo administradores pueden responder preguntas.');
        }

        $validated = $request->validate([
            'respuesta' => 'required|string|min:10|max:3000',
        ], [
            'respuesta.required' => 'La respuesta es obligatoria.',
            'respuesta.min' => 'La respuesta debe tener al menos 10 caracteres.',
            'respuesta.max' => 'La respuesta no puede exceder 3000 caracteres.',
        ]);

        $pregunta->marcarRespondida($user, $validated['respuesta']);

        return redirect()->route('preguntas.show', $pregunta)
            ->with('success', '¡Respuesta publicada correctamente!');
    }

    public function destroy(Pregunta $pregunta): RedirectResponse
    {
        $user = Auth::user();

        if (!$user || ($pregunta->user_id !== $user->id && !$user->is_admin)) {
            abort(403, 'No tienes permiso para eliminar esta pregunta.');
        }

        $pregunta->delete();

        return redirect()->route('preguntas.index')
            ->with('success', 'Pregunta eliminada correctamente.');
    }
}
