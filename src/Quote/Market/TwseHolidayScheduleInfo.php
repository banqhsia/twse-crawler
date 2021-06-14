<?php

namespace App\Quote\Market;

use Carbon\CarbonImmutable;

class TwseHolidayScheduleInfo
{
    /**
     * @var \stdClass
     */
    private $schedule;

    /**
     * Construct.
     *
     * @param \stdClass $schedule
     */
    public function __construct($schedule)
    {
        $this->schedule = $schedule;
    }

    /**
     * Get the schedule name.
     *
     * @return string
     */
    public function getName()
    {
        return $this->schedule->Name;
    }

    /**
     * Get the date.
     *
     * @return string
     */
    public function getDate()
    {
        /**
         * We get Taiwan/ROC date time like an integer 1100315 (March 15, 2021)
         *
         * The easiest way to cast Taiwan/ROC year is adding "1911" to
         * Taiwan/ROC year. For example: 110 + 1911 = 2021.
         */
        $taiwanDate = $this->schedule->Date;
        $date = $taiwanDate + 19110000;

        return CarbonImmutable::createFromFormat(
            'Ymd', $date, new \DateTimeZone('Asia/Taipei')
        )->startOfDay();
    }

    /**
     * Get the description
     *
     * @return string
     */
    public function getDescription()
    {
        return $this->schedule->Description;
    }

    /**
     * Determine if the date is holiday.
     *
     * @return bool
     */
    public function isHoliday()
    {
        return HolidayDetector::geuessIsHoliday($this);
    }

    /**
     * Determine if the date is a trading day.
     *
     * @return bool
     */
    public function isTradingDay()
    {
        return !$this->isHoliday();
    }
}
