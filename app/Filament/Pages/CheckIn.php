<?php

namespace App\Filament\Pages;

use App\Services\AttendanceService;
use BackedEnum;
use Filament\Forms\Components\Radio;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\ViewField;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Schemas\Components\Text;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Wizard;
use Filament\Schemas\Components\Wizard\Step;
use Filament\Schemas\Concerns\InteractsWithSchemas;
use Filament\Schemas\Contracts\HasSchemas;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\HtmlString;
use Illuminate\Validation\ValidationException;
use UnitEnum;

class CheckIn extends Page implements HasSchemas
{
    use InteractsWithSchemas;

    protected string $view = 'filament.pages.check-in';
    protected static ?string $title = 'Guest Check-in';
    protected static ?string $navigationLabel = 'Check-in';
    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;
    protected static string|UnitEnum|null $navigationGroup = "Order managment";
    protected static ?int $navigationSort = 4;

    public string $reservationReference = '';
    public string $manualNote = '';

    public string $checkInMethod = 'qr';

    public function form(Schema $schema): Schema
    {
        return $schema->components([
            Wizard::make([
                Step::make('Method')
                    ->description('How will the guest check in?')
                    ->icon(Heroicon::QuestionMarkCircle)
                    ->completedIcon(Heroicon::Check)
                    ->schema([
                        Radio::make('checkInMethod')
                            ->hiddenLabel()
                            ->options([
                                'qr' => 'Scan QR code',
                                'manual' => 'Manual check-in',
                            ])
                            ->descriptions([
                                'qr' => 'Fastest option. Scan the QR code on the guest\'s ticket.',
                                'manual' => 'Use the reservation reference when the QR code is unavailable.',
                            ])
                            ->default('qr')
                            ->live()
                            ->required(),
                    ]),

                Step::make('Scan QR')
                    ->description('Point the camera at the ticket.')
                    ->icon(Heroicon::QrCode)
                    ->completedIcon(Heroicon::Check)
                    ->visible(fn (Get $get) => $get('checkInMethod') === 'qr')
                    ->schema([
                        ViewField::make('qr_scanner')
                            ->hiddenLabel()
                            ->view('filament.pages.qr-scanner')
                            ->dehydrated(false),
                    ]),

                Step::make('Guest details')
                    ->description('Enter the reservation and the reason.')
                    ->icon(Heroicon::Ticket)
                    ->completedIcon(Heroicon::Check)
                    ->visible(fn (Get $get) => $get('checkInMethod') === 'manual')
                    ->schema([
                        TextInput::make('reservationReference')
                            ->label('Reservation reference')
                            ->placeholder('e.g. RES-12345')
                            ->prefixIcon(Heroicon::Ticket)
                            ->required()
                            ->live(onBlur: true),

                        Textarea::make('manualNote')
                            ->label('Manual check-in note')
                            ->placeholder('Why is this guest being checked in manually?')
                            ->rows(4)
                            ->required()
                            ->live(onBlur: true),
                    ]),

                Step::make('Confirm')
                    ->description('Review before checking in.')
                    ->icon(Heroicon::ClipboardDocumentCheck)
                    ->completedIcon(Heroicon::Check)
                    ->visible(fn (Get $get) => $get('checkInMethod') === 'manual')
                    ->schema([
                        Text::make(fn (Get $get): string => 'Reservation: ' . ($get('reservationReference') ?: '-')),
                        Text::make(fn (Get $get): string => 'Note: ' . ($get('manualNote') ?: '-')),
                    ]),
            ])
                ->submitAction(new HtmlString(Blade::render(<<<'BLADE'
                    <x-filament::button type="submit" color="success" icon="heroicon-o-check">
                        Finish
                    </x-filament::button>
                BLADE)))
                ->columnSpanFull(),
        ]);
    }


    public function submit(): void
    {
        if ($this->checkInMethod === 'manual') {
            $this->checkInManually();

            return;
        }

        $this->redirect(static::getUrl());
    }

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

            $this->redirect(static::getUrl());
        } catch (ValidationException $exception) {
            Notification::make()
                ->title(collect($exception->errors())->flatten()->first() ?: 'Check-in failed')
                ->danger()
                ->send();
        }
    }
}