<?php

namespace App\Support;

use Carbon\CarbonInterface;
use Illuminate\Support\Carbon;

class BranchSchedule
{
    public static function all(): array
    {
        return config('clinic.branches', []);
    }

    public static function keys(): array
    {
        return array_keys(self::all());
    }

    public static function isAvailable(string $branch, string|CarbonInterface $date): bool
    {
        $schedule = self::all()[$branch] ?? null;

        if (! $schedule) {
            return false;
        }

        $day = $date instanceof CarbonInterface ? $date->dayOfWeekIso : Carbon::parse($date)->dayOfWeekIso;

        return in_array($day, $schedule['days'], true);
    }

    public static function name(?string $branch): string
    {
        return self::all()[$branch]['name'] ?? 'Clinic branch';
    }

    public static function hours(?string $branch): string
    {
        return self::all()[$branch]['hours'] ?? 'Hours unavailable';
    }
}
