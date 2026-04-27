<?php

namespace Tests\Feature;

use App\Mail\AtencionUsuarioMail;
use App\Models\User;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class AtencionUsuarioTest extends TestCase
{
    public function test_guest_can_send_customer_support_message(): void
    {
        Mail::fake();

        $response = $this->post(route('atencion.store'), [
            'nombre' => 'Ana Lopez',
            'email' => 'ana@example.com',
            'asunto' => 'Necesito ayuda',
            'mensaje' => 'No puedo acceder a una parte de la app y necesito soporte.',
        ]);

        $response
            ->assertRedirect(route('atencion.create'))
            ->assertSessionHas('success');

        Mail::assertSent(AtencionUsuarioMail::class, function (AtencionUsuarioMail $mail) {
            return $mail->datos['email'] === 'ana@example.com'
                && $mail->datos['asunto'] === 'Necesito ayuda';
        });
    }

    public function test_authenticated_user_can_send_customer_support_message_without_name_and_email(): void
    {
        Mail::fake();

        $user = User::factory()->make([
            'name' => 'Mario Moveet',
            'email' => 'mario@example.com',
        ]);
        $user->id = 999;

        $response = $this
            ->actingAs($user)
            ->post(route('atencion.store'), [
                'asunto' => 'Consulta desde cuenta',
                'mensaje' => 'Estoy dentro de mi cuenta y necesito ayuda con una compra.',
            ]);

        $response
            ->assertRedirect(route('atencion.create'))
            ->assertSessionHas('success');

        Mail::assertSent(AtencionUsuarioMail::class, function (AtencionUsuarioMail $mail) use ($user) {
            return $mail->datos['nombre'] === $user->name
                && $mail->datos['email'] === $user->email
                && $mail->datos['asunto'] === 'Consulta desde cuenta';
        });
    }
}
