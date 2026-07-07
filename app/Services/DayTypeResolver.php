<?php

namespace App\Services;

use Carbon\Carbon;

class DayTypeResolver
{
    /**
     * Klasifikasi 1 tanggal — murni logic, tidak query apapun.
     * $holidayDates harus sudah di-fetch sebelumnya lewat HolidayService::getHolidayDatesInRange().
     */
    public function resolve(Carbon $date, array $holidayDates): string
    {
        if (in_array($date->toDateString(), $holidayDates)) {
            return 'holiday';
        }

        if ($date->dayOfWeek === 0 || $date->dayOfWeek === 6) {
            return 'weekend';
        }

        return 'weekday';
    }
}