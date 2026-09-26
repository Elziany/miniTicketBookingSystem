<?php

namespace App\Filament\Resources\Orders\Pages;

use App\Filament\Resources\Orders\OrderResource;
use App\Services\OrderService;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Database\Eloquent\Model;

class CreateOrder extends CreateRecord
{
    protected static string $resource = OrderResource::class;

   protected function handleRecordCreation(array $data): Model
    {
        return app(OrderService::class)->createOrder(
            userId: $data['user_id'],
            eventId: $data['event_id'],
            seatIds: $data['seat_ids'],
        );
    }
}
