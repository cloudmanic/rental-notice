<?php

namespace App\Services;

use Carbon\Carbon;

class DateService
{
    /**
     * Get the next available mailing date based on cutoff time and business days
     *
     * @param  Carbon|null  $date  The date to check (defaults to now)
     * @return Carbon The next available mailing date
     */
    public function getNextMailingDate(?Carbon $date = null): Carbon
    {
        $date = $date ?? Carbon::now();

        // Get configuration values
        $cutoffTime = config('constants.mailing.cutoff_time', '13:00');
        $timezone = config('constants.mailing.cutoff_timezone', 'America/Los_Angeles');

        // Create a copy of the date in the specified timezone
        $checkDate = $date->copy()->setTimezone($timezone);

        // Parse the cutoff time
        [$cutoffHour, $cutoffMinute] = explode(':', $cutoffTime);
        $cutoffDateTime = $checkDate->copy()
            ->setHour((int) $cutoffHour)
            ->setMinute((int) $cutoffMinute)
            ->setSecond(0);

        // If we're past the cutoff time, move to next day
        if ($checkDate->gt($cutoffDateTime)) {
            $checkDate->addDay();
        }

        // Skip to Monday if we land on Sunday
        if ($checkDate->isSunday()) {
            $checkDate->next(Carbon::MONDAY);
        }

        // Return the date at start of day
        return $checkDate->startOfDay();
    }

    /**
     * Check if a given time is past the mailing cutoff
     *
     * @param  Carbon|null  $date  The date to check (defaults to now)
     * @return bool True if past cutoff time
     */
    public function isPastCutoffTime(?Carbon $date = null): bool
    {
        $date = $date ?? Carbon::now();

        // Get configuration values
        $cutoffTime = config('constants.mailing.cutoff_time', '13:00');
        $timezone = config('constants.mailing.cutoff_timezone', 'America/Los_Angeles');

        // Create a copy of the date in the specified timezone
        $checkDate = $date->copy()->setTimezone($timezone);

        // Parse the cutoff time
        [$cutoffHour, $cutoffMinute] = explode(':', $cutoffTime);
        $cutoffDateTime = $checkDate->copy()
            ->setHour((int) $cutoffHour)
            ->setMinute((int) $cutoffMinute)
            ->setSecond(0);

        return $checkDate->gt($cutoffDateTime);
    }

    /**
     * Format a date for display in mailing documents
     *
     * @param  Carbon  $date  The date to format
     * @param  string  $format  The format string (default: 'F j, Y')
     * @return string The formatted date string
     */
    public function formatMailingDate(Carbon $date, string $format = 'F j, Y'): string
    {
        return $date->format($format);
    }

    /**
     * Calculate the service date based on notice type and mailing date
     *
     * @param  Carbon  $mailingDate  The mailing date
     * @param  int  $noticeDays  The number of days for the notice (10 or 13)
     * @return Carbon The service date
     */
    public function calculateServiceDate(Carbon $mailingDate, int $noticeDays = 10): Carbon
    {
        // Skip the day of mailing (start counting from next day)
        // Add the notice period (10 or 13 days)
        // Add 4 days for mailing
        $totalDays = 1 + $noticeDays + 4;

        return $mailingDate->copy()->addDays($totalDays);
    }
}
