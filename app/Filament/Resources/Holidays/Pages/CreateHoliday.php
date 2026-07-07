<?php

namespace App\Filament\Resources\Holidays\Pages;

use App\Filament\Resources\Holidays\HolidayResource;
use App\Models\Holiday;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Validation\ValidationException;
use Illuminate\Database\Eloquent\Model;

class CreateHoliday extends CreateRecord
{
    protected static string $resource = HolidayResource::class;

    protected function handleRecordCreation(array $data): Model // diubah — override total, generate banyak row kalau ada date_end
    {
        $rangeParts = explode(' - ', $data['date_range']);
        $startDate  = Carbon::parse(trim($rangeParts[0]));
        $endDate    = isset($rangeParts[1]) ? Carbon::parse(trim($rangeParts[1])) : $startDate->copy();

        $period = CarbonPeriod::create($startDate, $endDate);
        $dates  = collect($period)->map(fn (Carbon $d) => $d->toDateString());

        // Cek dulu apakah ada tanggal yang sudah eksis sebelum insert apapun
        $existingDates = Holiday::whereIn('date', $dates)->pluck('date')
            ->map(fn ($d) => Carbon::parse($d)->toDateString());

        if ($existingDates->isNotEmpty()) {
            throw ValidationException::withMessages([
                'data.date_end' => 'Tanggal ' . $existingDates->implode(', ') . ' sudah terdaftar sebagai hari libur lain.',
            ]);
        }

        $lastRecord = null;
        foreach ($dates as $date) {
            $lastRecord = Holiday::create([
                'name' => $data['name'],
                'date' => $date,
            ]);
        }

        if ($dates->count() > 1) {
            Notification::make()
                ->title($dates->count() . ' hari libur berhasil ditambahkan')
                ->success()
                ->send();
        }

        return $lastRecord; // Filament butuh 1 record dikembalikan untuk redirect setelah create
    }
}