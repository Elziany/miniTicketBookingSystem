<?php

namespace App\Services;

use Kreait\Firebase\Factory;
use Kreait\Firebase\Messaging\CloudMessage;
use Kreait\Firebase\Messaging\Notification;

class FirebaseService
{
    private $messaging;

    public function __construct()
    {
        $this->messaging = (new Factory)
            ->withServiceAccount(config('firebase.credentials'))
            ->createMessaging();
    }

    public function sendToToken(
        string $token,
        string $title,
        string $body,
        array $data = []
    ): void {
        $message = CloudMessage::new()
            ->withToken($token)
            ->withNotification(
                Notification::create($title, $body)
            )
            ->withData(
                collect($data)
                    ->map(fn ($value) => (string) $value)
                    ->toArray()
            );

        $this->messaging->send($message);
    }
}