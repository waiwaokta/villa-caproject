<?php

namespace App\Services;

use App\Models\Maintenance;
use Carbon\Carbon;

class MaintenanceService
{
    /**
     * Cek apakah wisma tertentu punya maintenance block yang overlap dengan rentang tanggal.
     * wismaID null di tabel artinya berlaku untuk SEMUA wisma.
     */
    public function isWismaBlocked(string $wismaID, Carbon $checkIn, Carbon $checkOut): bool
    {
        return Maintenance::where(function ($q) use ($wismaID) {
                $q->where('wismaID', $wismaID)
                  ->orWhereNull('wismaID');
            })
            ->whereBetween('date', [$checkIn->toDateString(), $checkOut->copy()->subDay()->toDateString()])
            ->exists();
    }

    /**
     * Ambil semua tanggal maintenance untuk 1 wisma dalam rentang tertentu (termasuk yang berlaku semua wisma).
     * Dipakai untuk kalender visual.
     */
    public function getBlockedDatesForWisma(string $wismaID, Carbon $start, Carbon $end): array
    {
        return Maintenance::where(function ($q) use ($wismaID) {
                $q->where('wismaID', $wismaID)
                  ->orWhereNull('wismaID');
            })
            ->whereBetween('date', [$start->toDateString(), $end->toDateString()])
            ->pluck('date')
            ->map(fn ($d) => Carbon::parse($d)->toDateString())
            ->toArray();
    }
}