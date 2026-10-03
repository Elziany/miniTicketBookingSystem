<?php

namespace App\Filament\Pages;

use App\Services\AttendanceService;
use BackedEnum;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Support\Icons\Heroicon;
use Illuminate\Validation\ValidationException;
use UnitEnum;

class CheckIn extends Page
{
    protected string $view = 'filament.pages.check-in';
    protected static ?string $title = 'Guest Check-in';
    protected static ?string $navigationLabel = 'Check-in';
    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;
    protected static string|UnitEnum|null $navigationGroup = "Order managment";
    protected static ?int $navigationSort = 4;

    public string $reservationReference = '';
    public string $manualNote = '';

    public function checkInByQr(string $reservationRef): void
    {
        try {
            app(AttendanceService::class)->checkInByQr(
                $reservationRef,
                (int) auth()->id()
            );

            Notification::make()
                ->title('Guest checked in')
                ->success()
                ->send();
        } catch (ValidationException $exception) {
            Notification::make()
                ->title(collect($exception->errors())->flatten()->first() ?: 'Check-in failed')
                ->danger()
                ->send();
        }
    }

    public function checkInManually(): void
    {
        $this->validate([
            'reservationReference' => 'required|string',
            'manualNote' => 'required|string',
        ]);

        try {
            app(AttendanceService::class)->checkInManually(
                $this->reservationReference,
                (int) auth()->id(),
                $this->manualNote
            );

            $this->reset(['reservationReference', 'manualNote']);

            Notification::make()
                ->title('Manual check-in recorded')
                ->success()
                ->send();
        } catch (ValidationException $exception) {
            Notification::make()
                ->title(collect($exception->errors())->flatten()->first() ?: 'Check-in failed')
                ->danger()
                ->send();
        }
    }
}
