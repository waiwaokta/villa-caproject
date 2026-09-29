<x-filament-panels::page>
    <div class="bg-white dark:bg-gray-900 rounded-xl border border-gray-200 dark:border-gray-700 p-6">

        <div class="flex items-center justify-center gap-2 mb-6">
            <button wire:click="prevMonth" type="button" class="fi-btn flex-none px-3 py-1.5 rounded-lg border border-gray-300 dark:border-gray-600 text-sm font-medium hover:bg-gray-50 dark:hover:bg-gray-800">
                &larr; Prev
            </button>

            <select
                wire:model.live="bulan"
                class="fi-select-input flex-none w-[115px] rounded-lg border border-gray-300 dark:border-gray-600 dark:bg-gray-800 text-sm font-medium px-3 py-1.5"
            >
                @foreach(['Januari','Februari','Maret','April','Mei','Juni','Juli','Agustus','September','Oktober','November','Desember'] as $i => $namaBulan)
                    <option value="{{ $i + 1 }}">{{ $namaBulan }}</option>
                @endforeach
            </select>

            <select
                wire:model.live="tahun"
                class="fi-select-input flex-none w-[75px] rounded-lg border border-gray-300 dark:border-gray-600 dark:bg-gray-800 text-sm font-medium px-3 py-1.5"
            >
                @for($tahunOpsi = now()->year - 1; $tahunOpsi <= now()->year + 3; $tahunOpsi++)
                    <option value="{{ $tahunOpsi }}">{{ $tahunOpsi }}</option>
                @endfor
            </select>

            <button wire:click="nextMonth" type="button" class="fi-btn flex-none px-3 py-1.5 rounded-lg border border-gray-300 dark:border-gray-600 text-sm font-medium hover:bg-gray-50 dark:hover:bg-gray-800">
                Next &rarr;
            </button>
        </div>

        @php
            $events = $this->getEvents();
            $startOfMonth = \Carbon\Carbon::createFromDate($tahun, $bulan, 1);
            $daysInMonth = $startOfMonth->daysInMonth;
            $firstDayOfWeek = $startOfMonth->dayOfWeek;
            $dayLabels = ['Minggu', 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'];
            $today = \Carbon\Carbon::today();
        @endphp

        <div class="grid grid-cols-7 gap-2 mb-2">
            @foreach($dayLabels as $label)
                <div class="text-center text-xs font-bold text-gray-500 py-1">{{ $label }}</div>
            @endforeach
        </div>

        <div class="grid grid-cols-7 gap-2">
            @for($i = 0; $i < $firstDayOfWeek; $i++)
                <div></div>
            @endfor

            @for($d = 1; $d <= $daysInMonth; $d++)
                @php
                    $currentDate = \Carbon\Carbon::createFromDate($tahun, $bulan, $d);
                    $dateStr = $currentDate->toDateString();
                    $dayEvents = $events[$dateStr] ?? [];
                    $isPast = $currentDate->lt($today);
                @endphp
                <div
                    @if(!$isPast)
                        wire:click="openAddEventModal('{{ $dateStr }}')"
                    @endif
                    @class([
                        'border rounded-lg p-1.25 min-h-[80px] text-xs transition-colors',
                        'border-gray-300 dark:border-gray-700 cursor-pointer hover:bg-gray-100 dark:hover:bg-gray-800' => !$isPast,
                        'border-gray-200 dark:border-gray-800 bg-gray-50 dark:bg-gray-900/40 opacity-50 cursor-not-allowed' => $isPast,
                    ])
                >
                    <div class="font-semibold mb-1 {{ $isPast ? 'text-gray-400 dark:text-gray-600' : '' }}">{{ $d }}</div>
                    @foreach($dayEvents as $event)
                        @if($event['type'] === 'holiday')
                            <div
                                @if(!$isPast)
                                    wire:click.stop="openEditEventModal('holiday', '{{ $dateStr }}')"
                                @endif
                                @class([
                                    'rounded px-1 py-0.5 mb-1 truncate',
                                    'bg-red-100 text-red-700 dark:bg-red-900 dark:text-red-200 cursor-pointer hover:opacity-75' => !$isPast,
                                    'bg-red-50 text-red-400 dark:bg-red-950 dark:text-red-800 cursor-not-allowed' => $isPast,
                                ])
                                title="{{ $event['label'] }}{{ $isPast ? '' : ' (klik untuk edit)' }}"
                            >
                                🔴 {{ $event['label'] }}
                            </div>
                        @else
                            @php
                                $villaIdParam = $event['villaID'] ? "'" . $event['villaID'] . "'" : 'null';
                            @endphp
                            <div
                                @if(!$isPast)
                                    wire:click.stop="openEditEventModal('maintenance', '{{ $dateStr }}', {{ $villaIdParam }})"
                                @endif
                                @class([
                                    'rounded px-1 py-0.5 mb-1 truncate',
                                    'bg-amber-100 text-amber-700 dark:bg-amber-900 dark:text-amber-200 cursor-pointer hover:opacity-75' => !$isPast,
                                    'bg-amber-50 text-amber-400 dark:bg-amber-950 dark:text-amber-800 cursor-not-allowed' => $isPast,
                                ])
                                title="{{ $event['label'] }} - {{ $event['reason'] }}{{ $isPast ? '' : ' (klik untuk edit)' }}"
                            >
                                🟡 {{ $event['label'] }}
                            </div>
                        @endif
                    @endforeach
                </div>
            @endfor
        </div>

        <div class="flex gap-4 mt-4 text-xs text-gray-500">
            <span>🔴 Libur Nasional</span>
            <span>🟡 Maintenance Villa</span>
        </div>
    </div>
</x-filament-panels::page>