<?php

namespace App;

use Carbon\CarbonImmutable;
use Carbon\CarbonInterface;
use Carbon\CarbonTimeZone;
use DateTimeZone;
use Illuminate\Support\Arr;

/**
 * @todo this class does not deal with non-trading day.
 */
class Clock
{
    /**
     * @var CarbonImmutable
     */
    protected $now;

    /**
     * @var array<string,bool> [date => true]
     */
    protected $tradingHolidays = [];

    /**
     * Get now date time.
     *
     * @return CarbonImmutable
     */
    public function getNow()
    {
        if (!$this->now instanceof CarbonImmutable) {
            $this->touchNow();
        }

        return $this->now;
    }

    /**
     * Set now date time.
     *
     * @param string|\DateTimeInterface|CarbonInterface $dateTime
     * @param string|\DateTimeZone|CarbonTimeZone $timezone
     * @return $this
     */
    public function setNow($dateTime, $timezone = null)
    {
        $timezone = $timezone ?: $this->getTimeZone();

        $this->now = CarbonImmutable::parse($dateTime, $timezone)->timezone($this->getTimeZone());

        return $this;
    }

    /**
     * Touch the date time.
     *
     * @return $this
     */
    public function touchNow()
    {
        $this->now = CarbonImmutable::now(
            $this->getTimeZone()
        );

        return $this;
    }

    /**
     * Get the timezone.
     *
     * @return \DateTimeZone
     */
    public function getTimeZone()
    {
        return new \DateTimeZone('Asia/Taipei');
    }

    /**
     * Set the holidays.
     *
     * @param CarbonImmutable $tradingHolidays
     * @return $this
     */
    public function setTradingHolidays($tradingHolidays)
    {
        $tradingHolidays = Arr::wrap($tradingHolidays);

        $this->tradingHolidays = collect($tradingHolidays)->mapWithKeys(function ($date) {
            return [$date->format('Y-m-d') => true];
        })->all();

        return $this;
    }

    /**
     * Determine if the date is trading holiday.
     *
     * @param string|\DateTimeInterface|CarbonImmutable $date
     * @return bool
     */
    public function isTradingHoliday($date)
    {
        if ($date instanceof \DateTimeInterface) {
            $date = $date->format('Y-m-d');
        }

        return Arr::exists($this->tradingHolidays, $date);
    }

    /**
     * Get the current trading day. For example: if today Friday, the return
     * value will be Friday. If today is Sunday, the return value will be Friday
     * still.
     *
     * @return CarbonImmutable
     */
    public function getCurrentTradingDay()
    {
        $now = $this->getNow();

        while ($this->isHoliday($now)) {
            $now = $now->subWeekdays();
        }

        return $now;
    }

    /**
     * Determine if the date is holiday or trading holiday.
     *
     * @param CarbonImmutable $date
     * @return bool
     */
    public function isHoliday(CarbonImmutable $date)
    {
        return $date->isWeekend() || $this->isTradingHoliday($date);
    }

    /**
     * Get the last trading day. For example: if today is Wednesday, then return
     * value will be Tuesday. If today is Saturday, the return value will be
     * Thursday.
     *
     * @return CarbonImmutable
     */
    public function getLastTradingDay()
    {
        $date = $this->getCurrentTradingDay()->subWeekdays();

        while ($this->isHoliday($date)) {
            $date = $date->subWeekdays();
        }

        return $date;
    }
}
