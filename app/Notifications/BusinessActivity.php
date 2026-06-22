<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\SlackMessage;
use Illuminate\Notifications\Notification;

class BusinessActivity extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        public string $action,
        public ?string $actor = null,
        public ?string $detail = null,
    ) {}

    public function via(object $notifiable): array
    {
        return ['slack'];
    }

    public function toSlack(object $notifiable): SlackMessage
    {
        return (new SlackMessage)
            ->from('Tuker.in', ':recycle:')
            ->success()
            ->content("*{$this->action}*")
            ->attachment(function ($attachment) {
                $attachment
                    ->fields([
                        'Aksi' => $this->action,
                        'Oleh' => $this->actor ?? '-',
                        'Detail' => $this->detail ?? '-',
                    ])
                    ->footer('Tuker.in | ' . now()->timezone('Asia/Jakarta')->format('d M Y H:i'))
                    ->footerIcon('https://www.gstatic.com/images/branding/product/2x/recycling_48dp.png');
            });
    }
}
