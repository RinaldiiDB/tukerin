<?php

namespace App\Mail;

use App\Models\RedemptionRequest;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Attachment;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class RedemptionStatus extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public RedemptionRequest $redemption;
    public string $status;

    public function __construct(RedemptionRequest $redemption, string $status)
    {
        $this->redemption = $redemption;
        $this->status = $status;
    }

    public function envelope(): Envelope
    {
        $subject = $this->status === 'approved'
            ? 'Permintaan Pencairan Poin Disetujui'
            : 'Permintaan Pencairan Poin Ditolak';

        return new Envelope(
            subject: $subject,
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.redemption-status',
        );
    }

    public function attachments(): array
    {
        return [];
    }
}
