<?php

namespace App\Quote\Market;

use Illuminate\Support\Str;

class HolidayDetector
{
    /**
     * Guess if the date is holiday.
     *
     * @param TwseHolidayScheduleInfo $schedule
     * @return bool
     */
    public static function geuessIsHoliday(TwseHolidayScheduleInfo $schedule)
    {
        return !Str::endsWith($schedule->getDescription(), ["開始交易。", "最後交易。"]);
    }
}
