<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\Hash;
use Illuminate\View\View;

class PasswordRecoveryController extends Controller
{
    /**
     * Mostrar formulario de olvid é contraseña
     */
    public function showForgotPasswordForm(): View
    {
        return view('auth.forgot_password');
    }

    /**
     * Enviar enlace de recuperación
     */
    public function sendResetLink(Request $request): RedirectResponse
    {
        $request->validate([
            'email' => ['required', 'email', 'exists:users,email'],
        ], [
            'email.exists' => 'No encontramos una cuenta con ese email.',
        ]);

        $status = Password::sendResetLink(
            $request->only('email')
        );

        return $status == Password::RESET_LINK_SENT
            ? back()->with('status', 'Hemos enviado un enlace para recuperar tu contraseña.')
            : back()->withErrors(['email' => __($status)]);
    }

    /**
     * Mostrar formulario de nueva contraseña
     */
    public function showResetPasswordForm(string $token): View|RedirectResponse
    {
        return view('auth.reset_password', ['token' => $token]);
    }

    /**
     * Procesar cambio de contraseña
     */
    public function resetPassword(Request $request): RedirectResponse
    {
        $request->validate([
            'token' => ['required'],
            'email' => ['required', 'email', 'exists:users,email'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);

        $status = Password::reset(
            $request->only('email', 'password', 'password_confirmation', 'token'),
            function (User $user, string $password) {
                $user->update([
                    'password' => Hash::make($password),
                ]);
            }
        );

        return $status == Password::PASSWORD_RESET
            ? redirect()->route('login')->with('status', 'Contraseña restablecida correctamente.')
            : back()->withErrors(['email' => __($status)]);
    }
}
