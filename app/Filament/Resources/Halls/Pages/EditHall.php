<?php

namespace App\Filament\Resources\Halls\Pages;

use App\Filament\Resources\Halls\HallResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Support\Facades\DB;

class EditHall extends EditRecord
{
    protected static string $resource = HallResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }

   protected function mutateFormDataBeforeFill(array $data): array
{
    $data['manager'] = DB::table('hall_agent')
        ->where('hall_id', $this->record->id)
        ->where('is_manager', true)
        ->value('user_id');

    $data['seat_labels'] = $this->record->seats
        ->mapWithKeys(fn ($seat) => [
            ($seat->position_y - 1) . '_' . ($seat->position_x - 1) => $seat->label,
        ])
        ->all();

    return $data;
}
    protected function afterSave(): void
    {
        $state = $this->form->getRawState();

        $this->record->syncSeatGrid($state['seat_labels'] ?? []);

        $managerId = $state['manager'] ?? null;

        // Reset everyone first, so a removed or changed manager is cleared
        DB::table('hall_agent')
            ->where('hall_id', $this->record->id)
            ->update(['is_manager' => false]);

        if ($managerId) {
            DB::table('hall_agent')
                ->where('hall_id', $this->record->id)
                ->where('user_id', $managerId)
                ->update(['is_manager' => true]);
        }
    }
}
