<?php

namespace App\Services;

use App\Models\UserNotification;

class NotificationService
{
    public function notify(int $userId, string $type, string $title, ?string $body = null, ?string $actionUrl = null): void
    {
        UserNotification::create([
            'user_id' => $userId,
            'type' => $type,
            'title' => $title,
            'body' => $body,
            'action_url' => $actionUrl,
        ]);
    }
}
