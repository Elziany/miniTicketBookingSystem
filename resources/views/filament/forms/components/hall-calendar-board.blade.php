<div
    wire:key="hall-calendar-{{ $hallId ?? 'none' }}"
    x-data="{ base: @js(\Illuminate\Support\Str::beforeLast($getStatePath(), '.')) }"
    x-init="if ($refs.frame) { $refs.frame.srcdoc = '<!DOCTYPE html>' + $refs.tpl.innerHTML }"
    x-on:message.window="
        if ($event.source === $refs.frame?.contentWindow && $event.data?.type === 'CALENDAR_SELECT') {
            $wire.set(base + '.start_time', $event.data.start, false);
            $wire.set(base + '.end_time', $event.data.end, false);
        }
    "
    class="w-full mb-6"
>
    {{-- Header --}}
    <div class="flex flex-wrap items-center justify-between gap-2 pb-3 mb-3 border-b border-gray-100 dark:border-gray-800">
        <div>
            <h4 class="text-xs font-semibold text-gray-900 dark:text-white">Hall Availability Month Grid</h4>
            <p class="text-[11px] text-gray-500 dark:text-gray-400">Click any date to pick event start and end time</p>
        </div>

        <span class="flex items-center gap-1.5 text-[11px] font-medium text-gray-500 dark:text-gray-400">
            <span class="w-2.5 h-2.5 rounded-full bg-indigo-600"></span>
            Booked Event
        </span>
    </div>

    @if (empty($hallId))
        <div class="flex items-center justify-center w-full h-[500px] rounded-xl border border-gray-200 dark:border-gray-800 bg-gray-50 dark:bg-slate-950">
            <div class="text-center">
                <p class="text-sm font-medium text-gray-600 dark:text-gray-300">Select a hall</p>
                <p class="mt-1 text-xs text-gray-400 dark:text-gray-500">The hall availability calendar will appear here.</p>
            </div>
        </div>
    @else
        <div wire:ignore class="w-full h-[500px] overflow-y-auto rounded-xl border border-gray-200 dark:border-gray-800 bg-white dark:bg-slate-950 shadow-sm">
            <iframe
                x-ref="frame"
                data-events="{{ json_encode($events ?? []) }}"
                class="w-full h-full border-0 block"
                style="width: 100%; height: 100%; min-height: 480px;"
            ></iframe>
        </div>

        {{-- Document loaded into the iframe. Inert here, so quotes are safe. --}}
        <template x-ref="tpl">
            <meta charset="utf-8">
            <meta name="viewport" content="width=device-width, initial-scale=1.0">
            <script src="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.10/index.global.min.js"></script>
            <style>
                * { box-sizing: border-box; }
                html, body {
                    margin: 0; padding: 10px; width: 100%; height: 100%;
                    overflow-y: auto;
                    font-family: ui-sans-serif, system-ui, -apple-system, sans-serif;
                    background-color: #ffffff; color: #1e293b;
                }
                html.dark, html.dark body { background-color: #090d16; color: #f8fafc; }
                html.dark .fc-theme-standard td,
                html.dark .fc-theme-standard th,
                html.dark .fc-theme-standard .fc-scrollgrid { border-color: #1e293b !important; }
                html.dark .fc .fc-toolbar-title { color: #f8fafc !important; }
                html.dark .fc .fc-button-primary {
                    background-color: #1e293b !important;
                    border-color: #334155 !important;
                    color: #e2e8f0 !important;
                }
                html.dark .fc-daygrid-day-number,
                html.dark .fc-col-header-cell-cushion { color: #94a3b8 !important; }
                #calendar { width: 100% !important; min-width: 100% !important; }
                .fc { width: 100% !important; font-size: 0.85rem; }
                .fc-daygrid-day-frame { min-height: 85px !important; cursor: pointer; }
                .fc-daygrid-day:hover { background-color: rgba(99, 102, 241, 0.08); }
                .fc-event { border-radius: 4px; padding: 2px 4px; font-size: 0.75rem; cursor: pointer; }
            </style>

            <div id="calendar"></div>

            <script>
                (function () {
                    if (!window.FullCalendar) {
                        document.getElementById('calendar').textContent = 'Calendar failed to load (CDN blocked?).';
                        return;
                    }

                    var events = JSON.parse(window.frameElement.dataset.events || '[]');

                    if (window.parent.document.documentElement.classList.contains('dark')) {
                        document.documentElement.classList.add('dark');
                    }

                    var calendar = new FullCalendar.Calendar(document.getElementById('calendar'), {
                        initialView: 'dayGridMonth',
                        headerToolbar: {
                            left: 'prev,next today',
                            center: 'title',
                            right: 'dayGridMonth,timeGridWeek'
                        },
                        height: 'auto',
                        events: events,

                        dateClick: function (info) {
                            var day = info.dateStr.substring(0, 10);
                            var slotStart = new Date(day + 'T10:00:00');
                            var slotEnd = new Date(day + 'T12:00:00');

                            var overlaps = events.some(function (ev) {
                                if (!ev.start) return false;
                                var s = new Date(ev.start);
                                var e = ev.end ? new Date(ev.end) : new Date(s.getTime() + 2 * 60 * 60 * 1000);
                                return s < slotEnd && e > slotStart;
                            });

                            if (overlaps) {
                                alert('This hall is already booked during 10:00 - 12:00 on this date.');
                                return;
                            }

                            window.parent.postMessage(
                                { type: 'CALENDAR_SELECT', start: day + ' 10:00:00', end: day + ' 12:00:00' },
                                window.location.origin
                            );
                        }
                    });

                    calendar.render();
                })();
            </script>
        </template>
    @endif
</div>