<?php
require_once 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

$evento = App\Models\Evento::where('fecha_inicio', '<=', date('Y-m-d'))
    ->where('fecha_fin', '>=', date('Y-m-d'))
    ->first();

if ($evento) {
    echo "Evento activo: {$evento->nombre}\n";
    echo "Fecha inicio: {$evento->fecha_inicio}\n";
    echo "Fecha fin: {$evento->fecha_fin}\n";
} else {
    echo "No hay evento activo\n";
}
?>