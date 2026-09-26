@php
    $eventId = $get('event_id');
    $seats = collect();

    if ($eventId) {
        $seats = app(\App\Services\SeatService::class)->getSeatsForEventScreen($eventId);
    }

    $statePath = $getStatePath();
@endphp

<style>
    /* Scoped styles to override Filament resets */
    .seat-grid-container {
        display: grid !important;
        gap: 16px !important;
        width: max-content !important;
        margin-left: auto !important;
        margin-right: auto !important;
    }

    .seat-item {
        width: 64px !important;
        height: 64px !important;
        display: flex !important;
        flex-direction: column !important;
        align-items: center !important;
        justify-content: center !important;
        border-radius: 12px !important;
        box-sizing: border-box !important;
        user-select: none !important;
        transition: transform 0.15s ease, box-shadow 0.15s ease !important;
    }

    .seat-item.seat-available {
        background-color: #10b981 !important; /* Emerald Green */
        color: #ffffff !important;
        cursor: pointer !important;
    }

    .seat-item.seat-available:hover {
        background-color: #059669 !important;
        transform: scale(1.05) !important;
    }

    .seat-item.seat-held {
        background-color: #f59e0b !important; /* Amber */
        color: #111827 !important;
        cursor: not-allowed !important;
    }

    .seat-item.seat-pending {
        background-color: #f97316 !important; /* Orange */
        color: #ffffff !important;
        cursor: not-allowed !important;
    }

    .seat-item.seat-reserved {
        background-color: #ef4444 !important; /* Red */
        color: #ffffff !important;
        cursor: not-allowed !important;
    }

    .seat-item.is-selected {
        outline: 4px solid #2563eb !important;
        outline-offset: 2px !important;
        transform: scale(1.08) !important;
        z-index: 10 !important;
        box-shadow: 0 4px 12px rgba(37, 99, 235, 0.4) !important;
    }
</style>

<div
    x-data="{
        selected: $wire.entangle('{{ $statePath }}') ?? [],

        toggleSeat(seatId, status) {
            if (status !== 'available') return;

            seatId = Number(seatId);
            if (! Array.isArray(this.selected)) {
                this.selected = [];
            }

            if (this.selected.includes(seatId)) {
                this.selected = this.selected.filter(id => id !== seatId);
            } else {
                this.selected.push(seatId);
            }
        },

        isSelected(seatId) {
            return Array.isArray(this.selected) && this.selected.includes(Number(seatId));
        }
    }"
    style="margin-top: 1rem; margin-bottom: 1rem;"
>
    @if (! $eventId)
        <div style="border: 1px dashed #d1d5db; background-color: #f9fafb; padding: 1.5rem; border-radius: 0.75rem; text-align: center; color: #6b7280; font-size: 0.875rem;">
            Please select an event above to view and choose available seats.
        </div>
    @else
        {{-- Screen --}}
        <div style="display: flex; justify-content: center; margin-bottom: 1.5rem;">
            <div style="width: 66%; background-color: #1f2937; color: #ffffff; text-align: center; font-weight: 700; font-size: 0.75rem; letter-spacing: 0.1em; padding: 0.625rem; border-radius: 0.5rem; text-transform: uppercase;">
                STAGE / SCREEN
            </div>
        </div>

        {{-- Seats Grid Container --}}
        <div style="overflow-x: auto; padding: 1.5rem; border: 1px solid #e5e7eb; border-radius: 1rem; background-color: #f9fafb;">
            <div
                class="seat-grid-container"
                style="
                    grid-template-columns: repeat({{ $seats->max('position_x') ?: 1 }}, 64px) !important;
                    grid-template-rows: repeat({{ $seats->max('position_y') ?: 1 }}, 64px) !important;
                "
            >
                @foreach ($seats as $seat)
                    @php
                        $reservation = $seat->reservations->first();

                        $status = match ($reservation?->status) {
                            'confirmed' => 'reserved',
                            'held' => 'held',
                            'pending_approval' => 'pending',
                            default => 'available',
                        };

                        $price = $seat->eventSeatPrices->first()?->price;
                        $posX = $seat->position_x ?? 1;
                        $posY = $seat->position_y ?? 1;

                        $statusClass = match ($status) {
                            'reserved' => 'seat-reserved',
                            'held' => 'seat-held',
                            'pending' => 'seat-pending',
                            default => 'seat-available',
                        };
                    @endphp

                    <div
                        wire:key="seat-{{ $seat->id }}"
                        @click="toggleSeat({{ $seat->id }}, '{{ $status }}')"
                        class="seat-item {{ $statusClass }}"
                        :class="{ 'is-selected': isSelected({{ $seat->id }}) }"
                        style="grid-column: {{ $posX }} !important; grid-row: {{ $posY }} !important;"
                    >
                        <span style="font-size: 0.875rem; font-weight: 800; line-height: 1;">{{ $seat->label }}</span>

                        @if ($price !== null)
                            <span style="font-size: 0.625rem; font-weight: 500; opacity: 0.9; margin-top: 3px;">
                                {{ number_format($price, 0) }} EGP
                            </span>
                        @endif
                    </div>
                @endforeach
            </div>
        </div>

        {{-- Legend --}}
        <div style="display: flex; flex-wrap: wrap; justify-content: center; gap: 1.5rem; margin-top: 1.25rem; font-size: 0.875rem; color: #374151;">
            <div style="display: flex; align-items: center; gap: 0.5rem;">
                <span style="height: 1rem; width: 1rem; border-radius: 0.25rem; background-color: #10b981; display: inline-block;"></span> Available
            </div>
            <div style="display: flex; align-items: center; gap: 0.5rem;">
                <span style="height: 1rem; width: 1rem; border-radius: 0.25rem; background-color: #f59e0b; display: inline-block;"></span> Held
            </div>
            <div style="display: flex; align-items: center; gap: 0.5rem;">
                <span style="height: 1rem; width: 1rem; border-radius: 0.25rem; background-color: #f97316; display: inline-block;"></span> Pending
            </div>
            <div style="display: flex; align-items: center; gap: 0.5rem;">
                <span style="height: 1rem; width: 1rem; border-radius: 0.25rem; background-color: #ef4444; display: inline-block;"></span> Reserved
            </div>
        </div>

        {{-- Selected Summary --}}
        <div style="margin-top: 1rem; padding: 0.75rem; border: 1px solid #e5e7eb; border-radius: 0.75rem; text-align: center; font-size: 0.875rem; font-weight: 500; background-color: #ffffff;">
            Selected Seats: <strong style="color: #2563eb; font-weight: 700;" x-text="Array.isArray(selected) ? selected.length : 0"></strong>
        </div>
    @endif
</div>