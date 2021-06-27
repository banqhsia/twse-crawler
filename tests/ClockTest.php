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
     * @testWith ["2021-06-14 10:30", "2021-06-11"]
     *           ["2021-06-15 10:30", "2021-06-11"]
     *           ["2021-06-16 10:30", "2021-06-16"]
     */
    public function test_getCurrentTradingDay_should_deal_with_trading_holiday($now, $expected)
    {
        /**
         * 2021-06-14 is Monday
         * 2021-06-15 is Tuesday
         */
        $this->target->setNow($now);

        $this->target->setTradingHolidays([
            new CarbonImmutable('2021-06-14', new \DateTimeZone('Asia/Taipei')),
            new CarbonImmutable('2021-06-15', new \DateTimeZone('Asia/Taipei')),
        ]);

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

    /**
     * @testWith ["2021-06-14 10:30", "2021-06-10"]
     *           ["2021-06-15 10:30", "2021-06-10"]
     *           ["2021-06-16 10:30", "2021-06-11"]
     */
    public function test_getLastTradingDay_should_deal_with_trading_holiday($now, $expected)
    {
        /**
         * 2021-06-14 is Monday
         * 2021-06-15 is Tuesday
         */
        $this->target->setNow($now);

        $this->target->setTradingHolidays([
            new CarbonImmutable('2021-06-14', new \DateTimeZone('Asia/Taipei')),
            new CarbonImmutable('2021-06-15', new \DateTimeZone('Asia/Taipei')),
        ]);

        $this->assertEquals($expected, $this->target->getLastTradingDay()->format('Y-m-d'));
    }

    public function test_is_trading_holiday_should_be_true()
    {
        $givenTradingHoliday = [
            /** 2021-06-14 is Monday*/
            new CarbonImmutable('2021-06-14'),
        ];

        $givenDate = new CarbonImmutable('2021-06-14');

        $this->target->setTradingHolidays($givenTradingHoliday);

        $this->assertTrue($this->target->isTradingHoliday($givenDate));
    }

    public function test_is_trading_holiday_should_be_false()
    {
        $givenTradingHoliday = [
            /** 2021-06-14 is Monday*/
            new CarbonImmutable('2021-06-14'),
        ];

        $givenDate = new CarbonImmutable('2021-06-15');

        $this->target->setTradingHolidays($givenTradingHoliday);

        $this->assertFalse($this->target->isTradingHoliday($givenDate));
    }

    /**
     * @testWith ["2021-06-02", "2021-06-09"]
     *           ["2021-06-03", "2021-06-09"]
     *           ["2021-06-04", "2021-06-09"]
     *           ["2021-06-05", "2021-06-09"]
     *           ["2021-06-06", "2021-06-09"]
     *           ["2021-06-07", "2021-06-09"]
     *           ["2021-06-08", "2021-06-09"]
     *           ["2021-06-09", "2021-06-17"]
     *           ["2021-06-10", "2021-06-17"]
     *           ["2021-06-11", "2021-06-17"]
     *           ["2021-06-12", "2021-06-17"]
     *           ["2021-06-13", "2021-06-17"]
     *           ["2021-06-14", "2021-06-17"]
     *           ["2021-06-15", "2021-06-17"]
     *           ["2021-06-16", "2021-06-17"]
     *           ["2021-06-17", "2021-06-23"]
     */
    public function test_getWeeklyClearingDay($now, $expected)
    {
        $this->target->setNow($now);

        /**
         * 2021-06-16 is Wednesday
         */
        $this->target->setTradingHolidays([
            new CarbonImmutable('2021-06-16', new \DateTimeZone('Asia/Taipei')),
        ]);

        $expected = CarbonImmutable::parse($expected, $this->timezone);

        $this->assertEquals($expected, $this->target->getWeeklyClearingDay());;
    }


    /**
     * @testWith ["2021-05-31", "2021-06-16"]
     *           ["2021-06-01", "2021-06-16"]
     *           ["2021-06-04", "2021-06-16"]
     *           ["2021-06-09", "2021-06-16"]
     *           ["2021-06-15", "2021-06-16"]
     *           ["2021-06-16", "2021-07-21"]
     *           ["2021-06-17", "2021-07-21"]
     *           ["2021-06-22", "2021-07-21"]
     *           ["2021-06-23", "2021-07-21"]
     *           ["2021-06-24", "2021-07-21"]
     *           ["2021-06-25", "2021-07-21"]
     *           ["2021-06-30", "2021-07-21"]
     *           ["2021-07-01", "2021-07-21"]
     *           ["2021-07-20", "2021-07-21"]
     *           ["2021-07-21", "2021-08-18"]
     *           ["2021-07-22", "2021-08-18"]
     *           ["2021-07-23", "2021-08-18"]
     */
    public function test_getMonthlyClearingDay($now, $expected)
    {
        $this->target->setNow($now);

        $expected = CarbonImmutable::parse($expected, $this->timezone);

        $this->assertEquals($expected, $this->target->getMonthlyClearingDay());;
    }

    /**
     * @testWith ["2021-05-31", "2021-06-17"]
     *           ["2021-06-01", "2021-06-17"]
     *           ["2021-06-04", "2021-06-17"]
     *           ["2021-06-09", "2021-06-17"]
     *           ["2021-06-15", "2021-06-17"]
     *           ["2021-06-16", "2021-06-17"]
     *           ["2021-06-17", "2021-07-21"]
     *           ["2021-06-22", "2021-07-21"]
     *           ["2021-06-23", "2021-07-21"]
     *           ["2021-06-24", "2021-07-21"]
     *           ["2021-06-25", "2021-07-21"]
     *           ["2021-06-30", "2021-07-21"]
     *           ["2021-07-01", "2021-07-21"]
     *           ["2021-07-20", "2021-07-21"]
     *           ["2021-07-21", "2021-08-18"]
     *           ["2021-07-22", "2021-08-18"]
     *           ["2021-07-23", "2021-08-18"]
     */
    public function test_getMonthlyClearingDay_with_trading_holidays($now, $expected)
    {
        $this->target->setNow($now);

        /**
         * 2021-06-16 is the 3rd Wednesday of June
         */
        $this->target->setTradingHolidays([
            new CarbonImmutable('2021-06-16', new \DateTimeZone('Asia/Taipei')),
        ]);

        $expected = CarbonImmutable::parse($expected, $this->timezone);

        $this->assertEquals($expected, $this->target->getMonthlyClearingDay());;
    }


}
