<?php

namespace App\Http\Controllers;

use App\Models\Pregunta;
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
            abort(403, 'Debes iniciar sesion para escribir una resena.');
        }

        return view('preguntas.create');
    }

    public function store(Request $request): RedirectResponse
    {
        if (!Auth::check()) {
            abort(403, 'Debes iniciar sesion para escribir una resena.');
        }

        $validated = $request->validate([
            'titulo' => 'required|string|max:255',
            'contenido' => 'required|string|min:10|max:2000',
        ], [
            'titulo.required' => 'El titulo es obligatorio.',
            'titulo.max' => 'El titulo no puede exceder 255 caracteres.',
            'contenido.required' => 'La resena es obligatoria.',
            'contenido.min' => 'La resena debe tener al menos 10 caracteres.',
            'contenido.max' => 'La resena no puede exceder 2000 caracteres.',
        ]);

        Pregunta::create([
            'user_id' => Auth::id(),
            'titulo' => $validated['titulo'],
            'contenido' => $validated['contenido'],
        ]);

        return redirect()->route('preguntas.index')
            ->with('success', 'Resena enviada correctamente. La revisaremos antes de publicarla.');
    }

    public function show(Pregunta $pregunta): View
    {
        $isAdminView = request()->routeIs('admin.*');
        return view('preguntas.show', [
            'pregunta' => $pregunta,
            'isAdminView' => $isAdminView,
        ]);
    }

    public function edit(Pregunta $pregunta): View
    {
        $user = Auth::user();

        if (!$user || ($pregunta->user_id !== $user->id && !$user->is_admin)) {
            abort(403, 'No tienes permiso para editar esta resena.');
        }

        return view('preguntas.edit', [
            'pregunta' => $pregunta,
        ]);
    }

    public function update(Request $request, Pregunta $pregunta): RedirectResponse
    {
        $user = Auth::user();

        if (!$user || ($pregunta->user_id !== $user->id && !$user->is_admin)) {
            abort(403, 'No tienes permiso para editar esta resena.');
        }

        if ($pregunta->estaRespondida() && $pregunta->user_id === $user->id) {
            abort(403, 'No puedes editar una resena que ya ha sido publicada.');
        }

        $validated = $request->validate([
            'titulo' => 'required|string|max:255',
            'contenido' => 'required|string|min:10|max:2000',
        ], [
            'titulo.required' => 'El titulo es obligatorio.',
            'titulo.max' => 'El titulo no puede exceder 255 caracteres.',
            'contenido.required' => 'La resena es obligatoria.',
            'contenido.min' => 'La resena debe tener al menos 10 caracteres.',
            'contenido.max' => 'La resena no puede exceder 2000 caracteres.',
        ]);

        $pregunta->update($validated);

        return redirect()->route('preguntas.show', $pregunta)
            ->with('success', 'Resena actualizada correctamente.');
    }

    public function adminPanel(): View
    {
        $user = Auth::user();

        if (!$user || !$user->is_admin) {
            abort(403, 'Solo administradores pueden acceder a esta pagina.');
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
            abort(403, 'Solo administradores pueden revisar y publicar resenas.');
        }

        $validated = $request->validate([
            'respuesta' => 'required|string|min:10|max:3000',
        ], [
            'respuesta.required' => 'El comentario interno es obligatorio.',
            'respuesta.min' => 'El comentario debe tener al menos 10 caracteres.',
            'respuesta.max' => 'El comentario no puede exceder 3000 caracteres.',
        ]);

        $pregunta->marcarRespondida($user, $validated['respuesta']);

        return redirect()->route('preguntas.show', $pregunta)
            ->with('success', 'Resena publicada correctamente.');
    }

    public function destroy(Pregunta $pregunta): RedirectResponse
    {
        $user = Auth::user();

        if (!$user || ($pregunta->user_id !== $user->id && !$user->is_admin)) {
            abort(403, 'No tienes permiso para eliminar esta resena.');
        }

        $pregunta->delete();

        return redirect()->route('preguntas.index')
            ->with('success', 'Resena eliminada correctamente.');
    }
}
