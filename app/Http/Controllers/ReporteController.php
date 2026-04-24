<?php

namespace App\Http\Controllers;

use App\Models\ReporteContenido;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class ReporteController extends Controller
{
    public function reportUser(Request $request, User $reportedUser): RedirectResponse
    {
        $reporter = Auth::user();

        if (!$reporter) {
            return redirect()->route('login');
        }

        if ((int) $reporter->id === (int) $reportedUser->id) {
            return back()->with('status', 'No puedes reportarte a ti mismo.');
        }

        $validated = $request->validate([
            'reason' => ['required', 'string', 'max:120'],
            'details' => ['nullable', 'string', 'max:2000'],
        ]);

        ReporteContenido::create([
            'reporter_user_id' => $reporter->id,
            'reported_user_id' => $reportedUser->id,
            'target_type' => 'profile',
            'target_id' => $reportedUser->id,
            'reason' => $validated['reason'],
            'details' => $validated['details'] ?? null,
            'status' => 'pending',
        ]);

        return back()->with('status', 'Reporte enviado al equipo administrador.');
    }

    public function adminIndex(Request $request): View
    {
        $status = $request->string('status')->toString();

        $query = ReporteContenido::query()
            ->with(['reporter', 'reportedUser', 'resolvedBy'])
            ->latest('created_at');

        if ($status !== '') {
            $query->where('status', $status);
        }

        $reportes = $query->paginate(25)->withQueryString();

        return view('admin.reportes.index', [
            'reportes' => $reportes,
            'status' => $status,
        ]);
    }

    public function adminResolve(Request $request, ReporteContenido $reporte): RedirectResponse
    {
        $admin = Auth::user();

        if (!$admin) {
            return redirect()->route('login');
        }

        $validated = $request->validate([
            'status' => ['required', 'in:resolved,dismissed,blocked'],
            'resolution_note' => ['nullable', 'string', 'max:2000'],
        ]);

        $reporte->update([
            'status' => $validated['status'],
            'resolution_note' => $validated['resolution_note'] ?? null,
            'resolved_by_user_id' => $admin->id,
            'resolved_at' => now(),
        ]);

        return redirect()->route('admin.reportes.index')
            ->with('status', 'Reporte actualizado correctamente.');
    }
}
