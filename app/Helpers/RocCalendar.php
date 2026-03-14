<?php
namespace App\Helpers;

use Carbon\Carbon;

class RocCalendar
{
    public static function toRoc(\DateTimeInterface|string|null $date): ?string
    {
        if (!$date) return null;
        $carbon = $date instanceof \DateTimeInterface ? Carbon::instance($date) : Carbon::parse($date);
        $rocYear = $carbon->year - 1911;
        return sprintf('%d年%02d月%02d日', $rocYear, $carbon->month, $carbon->day);
    }

    public static function toRocYear(\DateTimeInterface|string|null $date): ?int
    {
        if (!$date) return null;
        $carbon = $date instanceof \DateTimeInterface ? Carbon::instance($date) : Carbon::parse($date);
        return $carbon->year - 1911;
    }

    public static function fromRoc(int $rocYear, int $month = 1, int $day = 1): Carbon
    {
        return Carbon::create($rocYear + 1911, $month, $day);
    }
}
