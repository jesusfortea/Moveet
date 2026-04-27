<?php

namespace App\Http\Controllers;

use App\Models\UserNotification;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class NotificationController extends Controller
{
    public function index(): View|RedirectResponse
    {
        $user = Auth::user();

        if (!$user) {
            return redirect()->route('login');
        }

        $notifications = UserNotification::query()
            ->where('user_id', $user->id)
            ->latest('created_at')
            ->paginate(20);

        return view('usuario.notificaciones', [
            'notifications' => $notifications,
        ]);
    }

    public function markAsRead(UserNotification $notification): RedirectResponse
    {
        $user = Auth::user();

        if (!$user) {
            return redirect()->route('login');
        }

        if ((int) $notification->user_id !== (int) $user->id) {
            abort(403);
        }

        if (!$notification->read_at) {
            $notification->update(['read_at' => now()]);
        }

        return redirect()->route('usuario.notificaciones');
    }

    public function markAllAsRead(Request $request): RedirectResponse
    {
        $user = Auth::user();

        if (!$user) {
            return redirect()->route('login');
        }

        UserNotification::query()
            ->where('user_id', $user->id)
            ->whereNull('read_at')
            ->update(['read_at' => now()]);

        return redirect()->route('usuario.notificaciones')
            ->with('status', 'Notificaciones marcadas como leidas.');
    }
}
