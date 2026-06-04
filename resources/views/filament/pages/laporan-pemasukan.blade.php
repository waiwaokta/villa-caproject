<x-filament-panels::page>

    {{-- Filter --}}
    <x-filament::section>
        <form wire:change.debounce.300ms="$refresh">
            {{ $this->filterForm }}
        </form>
    </x-filament::section>

    {{-- Summary Cards --}}
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
        <x-filament::section>
            <div class="text-center">
                <p class="text-sm text-gray-500 dark:text-gray-400">Total Booking Disetujui</p>
                <p class="text-3xl font-bold text-primary-500 mt-1">{{ $this->getTotalBooking() }}</p>
            </div>
        </x-filament::section>

        <x-filament::section>
            <div class="text-center">
                <p class="text-sm text-gray-500 dark:text-gray-400">Total Pemasukan</p>
                <p class="text-2xl font-bold text-success-500 mt-1">{{ $this->getTotalPemasukan() }}</p>
            </div>
        </x-filament::section>

        <x-filament::section>
            <div class="text-center">
                <p class="text-sm text-gray-500 dark:text-gray-400">Periode</p>
                <p class="text-lg font-bold mt-1">
                    @if($bulan && $tahun)
                        {{ \Carbon\Carbon::create($tahun, $bulan)->translatedFormat('F Y') }}
                    @elseif($tahun)
                        Tahun {{ $tahun }}
                    @else
                        Semua Periode
                    @endif
                </p>
            </div>
        </x-filament::section>
    </div>

    {{-- Tabel --}}
    <x-filament::section heading="Rincian Pemasukan">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="border-b border-gray-200 dark:border-gray-700">
                        <th class="text-left py-3 px-4 font-medium text-gray-500">Kode Booking</th>
                        <th class="text-left py-3 px-4 font-medium text-gray-500">Wisma</th>
                        <th class="text-left py-3 px-4 font-medium text-gray-500">Nama Tamu</th>
                        <th class="text-left py-3 px-4 font-medium text-gray-500">Tipe</th>
                        <th class="text-left py-3 px-4 font-medium text-gray-500">Check-in</th>
                        <th class="text-left py-3 px-4 font-medium text-gray-500">Check-out</th>
                        <th class="text-left py-3 px-4 font-medium text-gray-500">Malam</th>
                        <th class="text-right py-3 px-4 font-medium text-gray-500">Total</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($this->getBookings() as $booking)
                        <tr class="border-b border-gray-100 dark:border-gray-800 hover:bg-gray-50 dark:hover:bg-gray-800/50">
                            <td class="py-3 px-4 font-mono text-xs font-bold">{{ $booking->bookingID }}</td>
                            <td class="py-3 px-4">{{ $booking->wisma?->name ?? '-' }}</td>
                            <td class="py-3 px-4">{{ $booking->guest_name }}</td>
                            <td class="py-3 px-4">
                                <span class="px-2 py-1 rounded text-xs font-medium {{ $booking->user_type === 'pln' ? 'bg-blue-100 text-blue-700' : 'bg-green-100 text-green-700' }}">
                                    {{ $booking->user_type === 'pln' ? 'PLN' : 'Umum' }}
                                </span>
                            </td>
                            <td class="py-3 px-4">{{ $booking->check_in->format('d M Y') }}</td>
                            <td class="py-3 px-4">{{ $booking->check_out->format('d M Y') }}</td>
                            <td class="py-3 px-4">{{ $booking->total_nights }} malam</td>
                            <td class="py-3 px-4 text-right font-medium">
                                Rp {{ number_format($booking->total_price, 0, ',', '.') }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="py-8 text-center text-gray-400">
                                Tidak ada data pemasukan untuk periode ini
                            </td>
                        </tr>
                    @endforelse
                </tbody>
                @if($this->getBookings()->count() > 0)
                    <tfoot>
                        <tr class="border-t-2 border-gray-300 dark:border-gray-600">
                            <td colspan="7" class="py-3 px-4 font-bold text-right">Grand Total</td>
                            <td class="py-3 px-4 text-right font-bold text-success-500">
                                {{ $this->getTotalPemasukan() }}
                            </td>
                        </tr>
                    </tfoot>
                @endif
            </table>
        </div>
    </x-filament::section>

</x-filament-panels::page>