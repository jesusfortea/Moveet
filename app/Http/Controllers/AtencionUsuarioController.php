<?php

namespace App\Http\Controllers;

use App\Mail\AtencionUsuarioMail;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Illuminate\View\View;

class AtencionUsuarioController extends Controller
{
    public function create(): View
    {
        return view('atencion.create', [
            'isAuthenticated' => Auth::check(),
            'defaultName' => Auth::user()?->name ?? '',
            'defaultEmail' => Auth::user()?->email ?? '',
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $user = Auth::user();

        $validated = $request->validate([
            'nombre' => [$user ? 'nullable' : 'required', 'string', 'max:120'],
            'email' => [$user ? 'nullable' : 'required', 'email', 'max:255'],
            'asunto' => ['required', 'string', 'max:150'],
            'mensaje' => ['required', 'string', 'min:10', 'max:4000'],
        ], [
            'nombre.required' => 'Tu nombre es obligatorio.',
            'email.required' => 'Tu correo es obligatorio.',
            'email.email' => 'Introduce un correo valido.',
            'asunto.required' => 'El asunto es obligatorio.',
            'mensaje.required' => 'Escribe tu duda o consulta.',
            'mensaje.min' => 'El mensaje debe tener al menos 10 caracteres.',
            'mensaje.max' => 'El mensaje no puede exceder 4000 caracteres.',
        ]);

        if ($user) {
            $validated['nombre'] = $user->name;
            $validated['email'] = $user->email;
        }

        Mail::to('moveetrun@gmail.com')->send(new AtencionUsuarioMail($validated));

        return redirect()
            ->route('atencion.create')
            ->with('success', 'Tu mensaje se ha enviado correctamente. Te responderemos por correo.');
    }
}
