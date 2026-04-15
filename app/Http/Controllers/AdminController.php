<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Mision;
use App\Models\Evento;
use App\Models\Recompensa;
use App\Models\PaseDePaseo;
use App\Models\Lugar;

class AdminController extends Controller
{
    public function dashboard()
    {
        $stats = [
            'total_usuarios' => User::count(),
            'total_misiones' => Mision::count(),
            'total_eventos' => Evento::count(),
            'total_paseos' => PaseDePaseo::count(),
            'total_lugares' => Lugar::count(),
            'total_recompensas' => Recompensa::count(),
        ];

        return view('admin.dashboard', $stats);
    }
}
