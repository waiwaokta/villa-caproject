<?php

namespace App\Services;

use App\Models\Holiday;
use Carbon\Carbon;

class HolidayService
{
    /**
     * Ambil semua tanggal holiday dalam rentang tertentu — 1 query untuk banyak tanggal,
     * dipakai untuk hindari N+1 saat klasifikasi banyak tanggal sekaligus (misal 1 booking multi-malam).
     */
    public function getHolidayDatesInRange(Carbon $start, Carbon $end): array
    {
        return Holiday::whereBetween('date', [$start->toDateString(), $end->toDateString()])
            ->pluck('date')
            ->map(fn ($d) => Carbon::parse($d)->toDateString())
            ->toArray();
    }
}