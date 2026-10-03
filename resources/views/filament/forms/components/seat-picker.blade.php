<div
    x-data="{
        state: $wire.$entangle('{{ $getStatePath() }}').live,
        has(id) { return (this.state || []).map(Number).includes(id) },
        toggle(id) {
            const s = (this.state || []).map(Number);
            this.state = s.includes(id) ? s.filter(i => i !== id) : [...s, id];
        }
    }"
    class="sp-wrap"
>
    <style>
        .sp-wrap { width: 100%; }
        .sp-screen {
            margin: 0 auto 1.5rem; max-width: 28rem; padding: .35rem 0; text-align: center;
            font-size: .7rem; letter-spacing: .3em; color: #64748b;
            border-top: 4px solid #6366f1; border-radius: 50% 50% 0 0 / 14px 14px 0 0;
            background: linear-gradient(to bottom, rgba(99,102,241,.18), transparent);
        }
        .sp-scroll { overflow-x: auto; padding: .5rem; }
        .sp-grid { display: grid; gap: .4rem; width: max-content; margin: 0 auto; }
        .sp-seat {
            height: 2.5rem; width: 2.5rem; padding: 0; border-radius: .4rem .4rem .6rem .6rem; font-size: .7rem; font-weight: 600;
            border: 1px solid #cbd5e1; background: #f1f5f9; color: #334155; cursor: pointer;
            transition: transform .1s, background .1s;
        }
        .sp-seat:hover:not(:disabled) { transform: translateY(-2px); border-color: #6366f1; }
        .sp-seat.sp-selected { background: #4f46e5; border-color: #4f46e5; color: #fff; }
        .sp-seat:disabled { background: #e5e7eb; color: #9ca3af; cursor: not-allowed; border-style: dashed; text-decoration: line-through; }
        .dark .sp-seat { background: #1e293b; border-color: #334155; color: #e2e8f0; }
        .dark .sp-seat.sp-selected { background: #6366f1; border-color: #6366f1; color: #fff; }
        .dark .sp-seat:disabled { background: #0f172a; color: #475569; }
        .sp-legend { display: flex; justify-content: center; gap: 1.25rem; margin-top: 1rem; font-size: .75rem; color: #64748b; }
        .sp-legend span { display: inline-flex; align-items: center; gap: .4rem; }
        .sp-dot { width: .9rem; height: .9rem; border-radius: .25rem; border: 1px solid #cbd5e1; }
    </style>

    @if (empty($seats))
        <p class="text-sm text-gray-500 dark:text-gray-400">
            Select an event to see its seats.
        </p>
    @else
        <div class="sp-screen">SCREEN</div>

        <div class="sp-scroll">
            <div class="sp-grid" style="grid-template-columns: repeat({{ max(1, $cols) }}, minmax(2.5rem, 2.5rem));">
                @foreach ($seats as $seat)
                    <button
                        type="button"
                        wire:key="seat-{{ $seat['id'] }}"
                        title="{{ $seat['label'] }}{{ $seat['available'] ? '' : ' (taken)' }}"
                        style="grid-row: {{ $seat['y'] }}; grid-column: {{ $seat['x'] }};"
                        class="sp-seat"
                        @if ($seat['available'])
                            x-on:click="toggle({{ $seat['id'] }})"
                            x-bind:class="{ 'sp-selected': has({{ $seat['id'] }}) }"
                        @else
                            disabled
                        @endif
                    >{{ $seat['label'] }}</button>
                @endforeach
            </div>
        </div>

        <div class="sp-legend">
            <span><i class="sp-dot" style="background:#f1f5f9"></i> Available</span>
            <span><i class="sp-dot" style="background:#4f46e5;border-color:#4f46e5"></i> Selected</span>
            <span><i class="sp-dot" style="background:#e5e7eb;border-style:dashed"></i> Taken</span>
        </div>
    @endif
</div>