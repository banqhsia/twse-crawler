<?php

namespace App\Provider;

use App\Clock;

class CommodityGuesser
{
    /**
     * @var Clock
     */
    private $clock;

    /**
     * Construct.
     *
     * @param Clock $clock
     */
    public function __construct(Clock $clock)
    {
        $this->clock = $clock;
    }

    /**
     * Get the monthly commodity name.
     *
     * @return string
     */
    public function getMonthly()
    {
        return $this->clock->getMonthlyClearingDay()->format('Ym');
    }
}
