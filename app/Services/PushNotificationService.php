<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Throwable;

class PushNotificationService
{
    public function __construct(
        protected UserDeviceService $userDeviceService,
        protected FirebaseService $firebaseService
    ) {}

    public function sendToUser(User|int $user, string $title, string $body, array $data = [], ?string $emailSubject = null): void
    {
        $userModel = $user instanceof User ? $user : User::find($user);

        if (!$userModel) {
            return;
        }

        $devices = $this->userDeviceService->getUserDevices($userModel->id);

        if ($devices->isEmpty()) {
            $this->sendEmailFallback($userModel, $emailSubject ?? $title, $body);
            return;
        }

        $sentCount = 0;

        foreach ($devices as $device) {
            try {
                $this->firebaseService->sendToToken(
                    token: $device->token,
                    title: $title,
                    body: $body,
                    data: $data
                );
                $sentCount++;
            } catch (Throwable $e) {
                Log::warning("Failed sending push notification to device token {$device->token}: {$e->getMessage()}");
                $this->userDeviceService->deleteDevice($device);
            }
        }

        if ($sentCount === 0) {
            $this->sendEmailFallback($userModel, $emailSubject ?? $title, $body);
        }
    }

    private function sendEmailFallback(User $user, string $subject, string $body): void
    {
        try {
            Mail::raw($body, function ($message) use ($user, $subject) {
                $message->to($user->email)->subject($subject);
            });
        } catch (Throwable $e) {
            Log::error("Failed sending fallback email to user {$user->id}: {$e->getMessage()}");
        }
    }
}