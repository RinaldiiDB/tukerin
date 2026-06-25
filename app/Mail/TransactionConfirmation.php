<?php

namespace App\Mail;

use App\Models\ExchangeTransaction;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Attachment;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class TransactionConfirmation extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public User $user;
    public ExchangeTransaction $transaction;

    public function __construct(User $user, ExchangeTransaction $transaction)
    {
        $this->user = $user;
        $this->transaction = $transaction;
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Konfirmasi Transaksi Penukaran Botol',
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.transaction-confirmation',
        );
    }

    public function attachments(): array
    {
        return [];
    }
}
