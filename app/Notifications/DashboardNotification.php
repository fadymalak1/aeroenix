<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class DashboardNotification extends Notification
{
    use Queueable;

    public function __construct(
        public string $title,
        public string $body,
        public ?string $actionUrl = null,
        public ?string $icon = null,
    ) {}

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    /**
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'title' => $this->title,
            'body' => $this->body,
            'action_url' => $this->actionUrl,
            'icon' => $this->icon,
        ];
    }
}
