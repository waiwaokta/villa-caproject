<?php

namespace App\Services;

use App\Models\Maintenance;
use Carbon\Carbon;

class MaintenanceService
{
    /**
     * Cek apakah villa tertentu punya maintenance block yang overlap dengan rentang tanggal.
     * villaID null di tabel artinya berlaku untuk SEMUA villa.
     */
    public function isVillaBlocked(string $villaID, Carbon $checkIn, Carbon $checkOut): bool
    {
        return Maintenance::where(function ($q) use ($villaID) {
                $q->where('villaID', $villaID)
                  ->orWhereNull('villaID');
            })
            ->whereBetween('date', [$checkIn->toDateString(), $checkOut->copy()->subDay()->toDateString()])
            ->exists();
    }

    /**
     * Ambil semua tanggal maintenance untuk 1 villa dalam rentang tertentu (termasuk yang berlaku semua villa).
     * Dipakai untuk kalender visual.
     */
    public function getBlockedDatesForVilla(string $villaID, Carbon $start, Carbon $end): array
    {
        return Maintenance::where(function ($q) use ($villaID) {
                $q->where('villaID', $villaID)
                  ->orWhereNull('villaID');
            })
            ->whereBetween('date', [$start->toDateString(), $end->toDateString()])
            ->pluck('date')
            ->map(fn ($d) => Carbon::parse($d)->toDateString())
            ->toArray();
    }
}