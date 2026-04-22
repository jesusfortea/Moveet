<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Attachment;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

use App\Models\Factura;

class FacturaPagoMail extends Mailable
{
    use Queueable, SerializesModels;

    public $factura;
    public $pdfContent;

    /**
     * Create a new message instance.
     */
    public function __construct(Factura $factura, $pdfContent)
    {
        $this->factura = $factura;
        $this->pdfContent = $pdfContent;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Factura de Pago - Moveet',
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.factura',
        );
    }

    /**
     * Get the attachments for the message.
     *
     * @return array<int, Attachment>
     */
    public function attachments(): array
    {
        return [
            Attachment::fromData(fn () => $this->pdfContent, 'Factura_Moveet_'.str_pad($this->factura->id, 6, '0', STR_PAD_LEFT).'.pdf')
                ->withMime('application/pdf'),
        ];
    }
}
