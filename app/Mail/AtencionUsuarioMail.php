<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class AtencionUsuarioMail extends Mailable
{
    use Queueable;
    use SerializesModels;

    public function __construct(public array $datos)
    {
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Atencion al usuario: ' . $this->datos['asunto'],
            replyTo: [
                new \Illuminate\Mail\Mailables\Address(
                    $this->datos['email'],
                    $this->datos['nombre']
                ),
            ],
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.atencion-usuario',
            with: [
                'datos' => $this->datos,
            ],
        );
    }
}
