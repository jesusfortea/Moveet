<?php

namespace App\Http\Controllers;

use App\Models\ReporteContenido;
use App\Models\User;
use App\Services\NotificationService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class ReporteController extends Controller
{
    public function __construct(private NotificationService $notificationService)
    {
    }

    public function reportUser(Request $request, User $reportedUser): RedirectResponse
    {
        $reporter = Auth::user();

        if (!$reporter) {
            return redirect()->route('login');
        }

        if ((int) $reporter->id === (int) $reportedUser->id) {
            return back()->with('status', 'No puedes reportarte a ti mismo.');
        }

        // Evitar reportes duplicados pendientes del mismo usuario
        $yaReportado = ReporteContenido::query()
            ->where('reporter_user_id', $reporter->id)
            ->where('reported_user_id', $reportedUser->id)
            ->where('status', 'pending')
            ->exists();

        if ($yaReportado) {
            return back()->with('status', 'Ya tienes un reporte pendiente sobre este usuario. El equipo lo revisará pronto.');
        }

        $validated = $request->validate([
            'reason'  => ['required', 'string', 'max:120'],
            'details' => ['nullable', 'string', 'max:2000'],
        ]);

        ReporteContenido::create([
            'reporter_user_id' => $reporter->id,
            'reported_user_id' => $reportedUser->id,
            'target_type'      => 'profile',
            'target_id'        => $reportedUser->id,
            'reason'           => $validated['reason'],
            'details'          => $validated['details'] ?? null,
            'status'           => 'pending',
        ]);

        // Notificar al usuario que reportó (confirmación)
        $this->notificationService->notify(
            $reporter->id,
            'report',
            'Reporte enviado correctamente',
            'Tu reporte sobre ' . $reportedUser->name . ' ha sido recibido. El equipo de administración lo revisará en breve.',
            null
        );

        return back()->with('status', '✅ Reporte enviado al equipo administrador. Te notificaremos cuando sea revisado.');
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
            'status'   => $status,
        ]);
    }

    public function adminResolve(Request $request, ReporteContenido $reporte): RedirectResponse
    {
        $admin = Auth::user();

        if (!$admin) {
            return redirect()->route('login');
        }

        $validated = $request->validate([
            'status'          => ['required', 'in:resolved,dismissed,blocked'],
            'resolution_note' => ['nullable', 'string', 'max:2000'],
        ]);

        $reporte->update([
            'status'              => $validated['status'],
            'resolution_note'     => $validated['resolution_note'] ?? null,
            'resolved_by_user_id' => $admin->id,
            'resolved_at'         => now(),
        ]);

        if ($validated['status'] === 'blocked' && $reporte->reported_user_id) {
            User::query()
                ->whereKey($reporte->reported_user_id)
                ->update([
                    'is_blocked' => true,
                    'blocked_at' => now(),
                    'blocked_reason' => $validated['resolution_note'] ?? 'Bloqueado por resolución administrativa de reporte.',
                ]);
        }

        // ── Notificar al usuario que hizo el reporte ──────────────────────────
        $msgReporter = match ($validated['status']) {
            'resolved'  => 'Tu reporte ha sido revisado y se han tomado las medidas oportunas. Gracias por ayudar a mantener Moveet seguro.',
            'dismissed' => 'Tu reporte ha sido revisado. Tras la investigación, no se encontraron infracciones que justifiquen una sanción.',
            'blocked'   => 'Tu reporte ha sido revisado. El usuario ha sido bloqueado por incumplimiento de las normas de la comunidad.',
            default     => 'Tu reporte ha sido actualizado.',
        };

        $titleReporter = match ($validated['status']) {
            'resolved'  => '✅ Reporte resuelto',
            'dismissed' => '📋 Reporte descartado',
            'blocked'   => '🚫 Usuario bloqueado',
            default     => 'Reporte actualizado',
        };

        if ($reporte->reporter_user_id) {
            $this->notificationService->notify(
                $reporte->reporter_user_id,
                'report',
                $titleReporter,
                $msgReporter,
                null
            );
        }

        // ── Notificar al usuario reportado si se toma acción ─────────────────
        if ($validated['status'] === 'blocked' && $reporte->reported_user_id) {
            $this->notificationService->notify(
                $reporte->reported_user_id,
                'report',
                '⚠️ Aviso de la administración',
                'Tu cuenta ha recibido una sanción por incumplir las normas de la comunidad Moveet. Si crees que es un error, contacta con el soporte.',
                null
            );
        } elseif ($validated['status'] === 'resolved' && $reporte->reported_user_id) {
            $this->notificationService->notify(
                $reporte->reported_user_id,
                'report',
                '⚠️ Aviso de la administración',
                'Hemos recibido y revisado un reporte sobre tu comportamiento en Moveet. Te pedimos que revises las normas de la comunidad.',
                null
            );
        }

        return redirect()->route('admin.reportes.index')
            ->with('status', 'Reporte actualizado y usuarios notificados correctamente.');
    }
}
