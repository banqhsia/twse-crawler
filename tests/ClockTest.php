<?php

namespace Tests;

use App\Clock;
use Carbon\CarbonImmutable;
use PHPUnit\Framework\TestCase;

class ClockTest extends TestCase
{
    /**
     * @var Clock
     */
    private $target;

    private $timezone = 'Asia/Taipei';

    protected function setUp(): void
    {
        $this->target = new Clock;
    }

    public function test_should_set_now()
    {
        $this->target->setNow("2021-01-25 10:30");

        $expected = CarbonImmutable::parse("2021-01-25 10:30", $this->timezone);

        $this->assertEquals($expected, $this->target->getNow());
    }

    /**
     * @testWith ["2021-06-03 10:30", "2021-06-03"]
     *           ["2021-06-04 10:30", "2021-06-04"]
     *           ["2021-06-05 10:30", "2021-06-04"]
     *           ["2021-06-06 10:30", "2021-06-04"]
     *           ["2021-06-07 10:30", "2021-06-07"]
     *
     */
    public function test_should_get_current_trading_day($now, $expected)
    {
        /**
         * 2021-06-03 is Thursday
         * 2021-06-04 is Friday
         */
        $this->target->setNow($now);

        $this->assertEquals($expected, $this->target->getCurrentTradingDay()->format('Y-m-d'));
    }

    /**
     * @testWith ["2021-06-03 10:30", "2021-06-02"]
     *           ["2021-06-04 10:30", "2021-06-03"]
     *           ["2021-06-05 10:30", "2021-06-03"]
     *           ["2021-06-06 10:30", "2021-06-03"]
     *           ["2021-06-07 10:30", "2021-06-04"]
     *           ["2021-06-08 10:30", "2021-06-07"]
     */
    public function test_should_get_last_trading_day($now, $expected)
    {
        /**
         * 2021-06-03 is Thursday
         * 2021-06-04 is Friday
         * 2021-06-05 is Saturday
         * 2021-06-06 is Sunday
         */
        $this->target->setNow($now);

        $this->assertEquals($expected, $this->target->getLastTradingDay()->format('Y-m-d'));
    }
}
