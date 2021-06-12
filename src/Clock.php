<?php

namespace App;

use Carbon\CarbonImmutable;
use Carbon\CarbonInterface;
use Carbon\CarbonTimeZone;
use DateTimeZone;

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
     * Get the current trading day. For example: if today Friday, the return
     * value will be Friday. If today is Sunday, the return value will be Friday
     * still.
     *
     * @return CarbonImmutable
     */
    public function getCurrentTradingDay()
    {
        $now = $this->getNow();

        if ($now->isWeekend()) {
            return $now->subWeekdays();
        }

        return $now;
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
        return $this->getCurrentTradingDay()->subWeekdays();
    }
}
