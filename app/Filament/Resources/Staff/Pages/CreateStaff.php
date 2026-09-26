<?php

namespace App\Filament\Resources\Staff\Pages;

use App\Enum\UserType;
use App\Filament\Resources\Staff\StaffResource;
use Filament\Resources\Pages\CreateRecord;

class CreateStaff extends CreateRecord
{
    protected static string $resource = StaffResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['type'] = UserType::STAFF;

        return $data;
    }
}
