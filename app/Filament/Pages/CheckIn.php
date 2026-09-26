<?php

namespace App\Filament\Pages;

use App\Services\AttendanceService;
use BackedEnum;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Support\Icons\Heroicon;
use Illuminate\Validation\ValidationException;

class CheckIn extends Page
{
    protected string $view = 'filament.pages.check-in';
    protected static ?string $title = 'Guest Check-in';
    protected static ?string $navigationLabel = 'Check-in';
    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    public string $reservationReference = '';
    public string $manualNote = '';

 
    public static function canAccess(): bool
    {
        $permission = config('roles_permissions.permissions.scan_attendance');

        return $permission && auth()->user()?->can($permission);
    }

    public function checkInByQr(string $reservationRef): void
    {
            $attendance = app(AttendanceService::class)->checkInByQr(
                $reservationRef,
                auth()->user()
            );
    }

    public function checkInManually(): void
    {
        $this->validate([
            'reservationReference' => 'required|string',
            'manualNote' => 'required|string',
        ]);

            $attendance = app(AttendanceService::class)->checkInManually(
                $this->reservationReference,
                auth()->user(),
                $this->manualNote
            );

            $this->reset(['reservationReference', 'manualNote']);
       
    }
}