<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use NotificationChannels\WebPush\WebPushChannel;
use NotificationChannels\WebPush\WebPushMessage;

class TagihanBaruNotification extends Notification
{
    use Queueable;

    private $tagihan;

    public function __construct($tagihan)
    {
        $this->tagihan = $tagihan;
    }

    public function via($notifiable)
    {
        return [WebPushChannel::class];

    }

    public function toWebPush($notifiable, $notification)
    {
        return (new WebPushMessage)
            ->title('Tagihan Baru')
            ->body('Tagihan Rp '.number_format($this->tagihan->paket->harga ?? 0, 0, ',', '.').' telah masuk.')
            ->icon('/images/icons/icon-192x192.png')
            ->badge('/images/icons/badge.png')
            ->vibrate([200, 100, 200])
            ->data(['url' => route('customer.tagihan')]);
    }
}
