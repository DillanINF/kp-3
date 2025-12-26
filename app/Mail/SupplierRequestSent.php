<?php

namespace App\Mail;

use App\Models\SupplierRequest;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class SupplierRequestSent extends Mailable
{
    use Queueable, SerializesModels;

    public SupplierRequest $request;

    public function __construct(SupplierRequest $request)
    {
        $this->request = $request;
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Permintaan Barang - ' . ($this->request->request_no ?? ''),
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.supplier_request_sent',
            with: [
                'request' => $this->request,
            ],
        );
    }
}
