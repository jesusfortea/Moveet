<?php
require __DIR__ . '/vendor/autoload.php';
$app = require __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\User;
use App\Models\Mision;
use Carbon\Carbon;

$u = User::first();
echo 'user=' . ($u ? $u->id : 'none') . PHP_EOL;
echo 'misiones=' . Mision::count() . PHP_EOL;
echo 'pivot=' . ($u ? $u->misiones()->count() : '0') . PHP_EOL;
$m = $u ? $u->misiones()->get() : null;
echo 'visible=' . ($m ? $m->count() : '0') . PHP_EOL;
if ($m) {
    foreach ($m as $x) {
        echo json_encode([
            'id' => $x->id,
            'nombre' => $x->nombre,
            'semanal' => $x->semanal,
            'pivot' => [
                'completada' => $x->pivot->completada,
                'fecha_limite' => (string) $x->pivot->fecha_limite,
            ],
        ]) . PHP_EOL;
    }
}
