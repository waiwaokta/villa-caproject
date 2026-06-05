<x-filament-panels::page>

    {{-- Filter --}}
    <x-filament::section>
        {{ $this->filterForm }}
    </x-filament::section>

    {{-- Summary Cards --}}
    <div style="display:grid;grid-template-columns:repeat(3,1fr);gap:1rem;">
        <x-filament::section>
            <p style="font-size:13px;color:#6b7280;margin-bottom:6px">Total Booking Disetujui</p>
            <p style="font-size:28px;font-weight:700;color:#f59e0b">{{ $this->getTotalBooking() }}</p>
        </x-filament::section>

        <x-filament::section>
            <p style="font-size:13px;color:#6b7280;margin-bottom:6px">Total Pemasukan</p>
            <p style="font-size:28px;font-weight:700;color:#22c55e">{{ $this->getTotalPemasukan() }}</p>
        </x-filament::section>

        <x-filament::section>
            <p style="font-size:13px;color:#6b7280;margin-bottom:6px">Periode</p>
            <p style="font-size:28px;font-weight:600;">
                @if($bulan && $tahun)
                    {{ \Carbon\Carbon::create($tahun, $bulan)->translatedFormat('F Y') }}
                @elseif($tahun)
                    Tahun {{ $tahun }}
                @else
                    Semua Periode
                @endif
            </p>
        </x-filament::section>
    </div>

    {{-- Tabel Rincian --}}
    <x-filament::section heading="Rincian Pemasukan">
        <div style="max-height:400px;overflow-y:auto;">
            <table style="width:100%;border-collapse:collapse;font-size:13px;">
                <thead>
                    <tr style="border-bottom:1px solid #374151;">
                        <th style="position:sticky;top:0;background:#111827;text-align:left;padding:10px 12px;color:#9ca3af;font-weight:500;">Kode Booking</th>
                        <th style="position:sticky;top:0;background:#111827;text-align:left;padding:10px 12px;color:#9ca3af;font-weight:500;">Wisma</th>
                        <th style="position:sticky;top:0;background:#111827;text-align:left;padding:10px 12px;color:#9ca3af;font-weight:500;">Nama Tamu</th>
                        <th style="position:sticky;top:0;background:#111827;text-align:left;padding:10px 12px;color:#9ca3af;font-weight:500;">Tipe</th>
                        <th style="position:sticky;top:0;background:#111827;text-align:left;padding:10px 12px;color:#9ca3af;font-weight:500;">Check-in</th>
                        <th style="position:sticky;top:0;background:#111827;text-align:left;padding:10px 12px;color:#9ca3af;font-weight:500;">Check-out</th>
                        <th style="position:sticky;top:0;background:#111827;text-align:left;padding:10px 12px;color:#9ca3af;font-weight:500;">Malam</th>
                        <th style="position:sticky;top:0;background:#111827;text-align:right;padding:10px 12px;color:#9ca3af;font-weight:500;">Total</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($this->getBookings() as $booking)
                        <tr style="border-bottom:1px solid #1f2937;">
                            <td style="padding:10px 12px;font-family:monospace;font-weight:700;font-size:12px;">
                                {{ $booking->bookingID }}
                            </td>
                            <td style="padding:10px 12px;">{{ $booking->wisma?->name ?? '-' }}</td>
                            <td style="padding:10px 12px;">{{ $booking->guest_name }}</td>
                            <td style="padding:10px 12px;">
                                @if($booking->user_type === 'pln')
                                    <span style="background:#1d4ed8;color:#fff;padding:2px 8px;border-radius:4px;font-size:11px;">PLN</span>
                                @else
                                    <span style="background:#15803d;color:#fff;padding:2px 8px;border-radius:4px;font-size:11px;">Umum</span>
                                @endif
                            </td>
                            <td style="padding:10px 12px;">{{ $booking->check_in->format('d M Y') }}</td>
                            <td style="padding:10px 12px;">{{ $booking->check_out->format('d M Y') }}</td>
                            <td style="padding:10px 12px;">{{ $booking->total_nights }} malam</td>
                            <td style="padding:10px 12px;text-align:right;font-weight:600;">
                                Rp {{ number_format($booking->total_price, 0, ',', '.') }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" style="padding:32px;text-align:center;color:#6b7280;">
                                Tidak ada data pemasukan untuk periode ini
                            </td>
                        </tr>
                    @endforelse
                </tbody>
                @if($this->getBookings()->count() > 0)
                    <tfoot>
                        <tr style="border-top:2px solid #374151;">
                            <td colspan="7" style="padding:12px;text-align:right;font-weight:700;">Grand Total</td>
                            <td style="padding:12px;text-align:right;font-weight:700;color:#22c55e;">
                                {{ $this->getTotalPemasukan() }}
                            </td>
                        </tr>
                    </tfoot>
                @endif
            </table>
        </div>
    </x-filament::section>

    {{-- Ringkasan per Wisma --}}
    @if($this->getBookings()->count() > 0)
    <x-filament::section heading="Ringkasan per Wisma">
        <div style="max-height:300px;overflow-y:auto;">
            <table style="width:100%;border-collapse:collapse;font-size:13px;">
                <thead>
                    <tr style="border-bottom:1px solid #374151;">
                        <th style="position:sticky;top:0;background:#111827;text-align:left;padding:10px 12px;color:#9ca3af;font-weight:500;">Wisma</th>
                        <th style="position:sticky;top:0;background:#111827;text-align:center;padding:10px 12px;color:#9ca3af;font-weight:500;">Jumlah Booking</th>
                        <th style="position:sticky;top:0;background:#111827;text-align:center;padding:10px 12px;color:#9ca3af;font-weight:500;">Total Malam</th>
                        <th style="position:sticky;top:0;background:#111827;text-align:right;padding:10px 12px;color:#9ca3af;font-weight:500;">Total Pemasukan</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($this->getBookings()->groupBy('wismaID') as $wismaID => $bookings)
                        <tr style="border-bottom:1px solid #1f2937;">
                            <td style="padding:10px 12px;font-weight:500;">
                                {{ $bookings->first()->wisma?->name ?? '-' }}
                            </td>
                            <td style="padding:10px 12px;text-align:center;">
                                {{ $bookings->count() }} booking
                            </td>
                            <td style="padding:10px 12px;text-align:center;">
                                {{ $bookings->sum('total_nights') }} malam
                            </td>
                            <td style="padding:10px 12px;text-align:right;font-weight:600;color:#22c55e;">
                                Rp {{ number_format($bookings->sum('total_price'), 0, ',', '.') }}
                            </td>
                        </tr>
                    @endforeach
                </tbody>
                <tfoot>
                    <tr style="border-top:2px solid #374151;">
                        <td style="padding:12px;font-weight:700;">Total</td>
                        <td style="padding:12px;text-align:center;font-weight:700;">
                            {{ $this->getBookings()->count() }} booking
                        </td>
                        <td style="padding:12px;text-align:center;font-weight:700;">
                            {{ $this->getBookings()->sum('total_nights') }} malam
                        </td>
                        <td style="padding:12px;text-align:right;font-weight:700;color:#22c55e;">
                            {{ $this->getTotalPemasukan() }}
                        </td>
                    </tr>
                </tfoot>
            </table>
        </div>
    </x-filament::section>
    @endif

</x-filament-panels::page>