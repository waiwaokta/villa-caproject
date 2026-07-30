<?php

namespace App\Filament\Pages;

use App\Models\Booking;
use App\Models\Wisma;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Schemas\Concerns\InteractsWithSchemas;
use Filament\Schemas\Contracts\HasSchemas;
use Filament\Schemas\Schema;
use Filament\Pages\Page;
use Illuminate\Support\Carbon;
use Filament\Support\Icons\Heroicon;
use BackedEnum;
use UnitEnum;

class LaporanPemasukan extends Page implements HasSchemas
{
    use InteractsWithSchemas;

    protected string $view = 'filament.pages.laporan-pemasukan';
    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedBanknotes;
    protected static string |UnitEnum| null $navigationGroup = 'Laporan';
    protected static ?string $navigationLabel = 'Laporan Pemasukan';
    protected static ?string $pluralModelLabel = 'Laporan';
    protected static ?string $title = 'Laporan Pemasukan';
    protected static ?int $navigationSort = 3;

    // Filter state
    public ?string $wisma_id = null;
    public ?string $bulan = null;
    public ?string $tahun = null;

    public function mount(): void
    {
        $this->bulan = now()->format('m');
        $this->tahun = now()->format('Y');
    }

    public function filterForm(Schema $schema): Schema
    {
        return $schema->schema([
            Select::make('wisma_id')
                ->label('Wisma')
                ->options(['' => 'Semua Wisma'] + Wisma::pluck('name', 'wismaID')->toArray())
                ->placeholder('Semua Wisma')
                ->selectablePlaceholder(false)
                ->live(),

            Select::make('bulan')
                ->label('Bulan')
                ->options([
                    '01' => 'Januari', '02' => 'Februari', '03' => 'Maret',
                    '04' => 'April',   '05' => 'Mei',      '06' => 'Juni',
                    '07' => 'Juli',    '08' => 'Agustus',  '09' => 'September',
                    '10' => 'Oktober', '11' => 'November', '12' => 'Desember',
                ])
                ->selectablePlaceholder(false)
                ->live(),

            Select::make('tahun')
                ->label('Tahun')
                ->options(collect(range(now()->year, now()->year - 3))
                    ->mapWithKeys(fn($y) => [$y => $y])
                    ->toArray())
                ->selectablePlaceholder(false)
                ->live(),
        ])->columns(3);
    }

    public function getBookings()
    {
        $query = Booking::with('wisma')
            ->where('status', 'approved');

        if ($this->wisma_id) {
            $query->where('wismaID', $this->wisma_id);
        }

        if ($this->bulan && $this->tahun) {
            $query->whereMonth('check_in', $this->bulan)
                  ->whereYear('check_in', $this->tahun);
        } elseif ($this->tahun) {
            $query->whereYear('check_in', $this->tahun);
        }

        return $query->orderBy('check_in', 'desc')->get();
    }

    public function getTotalPemasukan(): string
    {
        $total = $this->getBookings()->sum('total_price');
        return 'Rp ' . number_format($total, 0, ',', '.');
    }

    public function getTotalBooking(): int
    {
        return $this->getBookings()->count();
    }
}