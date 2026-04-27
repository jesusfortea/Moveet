<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

$misiones = \App\Models\Mision::where('evento_id', null)
    ->orderBy('semanal')
    ->orderBy('metros_requeridos')
    ->get(['nombre', 'semanal', 'metros_requeridos', 'puntos']);

echo "\n=== MISIONES DIARIAS (" . $misiones->where('semanal', false)->count() . ") ===\n";
foreach ($misiones->where('semanal', false) as $m) {
    echo "  · " . $m->nombre . " (" . $m->metros_requeridos . "m, " . $m->puntos . " pts)\n";
}

echo "\n=== MISIONES SEMANALES (" . $misiones->where('semanal', true)->count() . ") ===\n";
foreach ($misiones->where('semanal', true) as $m) {
    echo "  · " . $m->nombre . " (" . $m->metros_requeridos . "m, " . $m->puntos . " pts)\n";
}
echo "\n";
