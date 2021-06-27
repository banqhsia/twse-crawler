<?php

namespace App\Quote\Options;

use Carbon\Carbon;
use Illuminate\Support\Str;

class TaifexFuturesInfo
{
    /**
     * @var string[]
     */
    private $info;

    /**
     * Construct.
     *
     * @param string[] $info
     */
    public function __construct($info)
    {
        $this->info = $info;
    }

    /**
     * Get the date.
     *
     * @return Carbon
     */
    public function getDate()
    {
        return Carbon::parse($this->info[0], new \DateTimeZone('Asia/Taipei'));
    }

    /**
     * Get the symbol.
     *
     * @return string
     */
    public function getSymbol()
    {
        return $this->info[1];
    }

    /**
     * Get the expiration
     *
     * @return string
     */
    public function getExpiration()
    {
        return trim($this->info[2]);
    }

    /**
     * Determine if the expiration is the given string.
     *
     * @param string $expiration
     * @return bool
     */
    public function expirationIs($expiration)
    {
        return $this->getExpiration() === $expiration;
    }

    /**
     * Get the open price.
     *
     * @return int
     */
    public function getOpenPrice()
    {
        return (int) $this->info[3];
    }

    /**
     * Get the high price.
     *
     * @return int
     */
    public function getHighPrice()
    {
        return (int) $this->info[4];

    }

    /**
     * Get the low price.
     *
     * @return int
     */
    public function getLowPrice()
    {
        return (int) $this->info[5];
    }

    /**
     * Get the close price.
     *
     * @return int
     */
    public function getClosePrice()
    {
        return (int) $this->info[6];
    }

    /**
     * Get the increase price.
     *
     * @return int
     */
    public function getIncreasePrice()
    {
        return (int) $this->info[7];
    }

    /**
     * Get the increase percentage.
     *
     * @return float
     */
    public function getIncreasePercentage()
    {
        return (float) $this->info[8];
    }

    /**
     * Get the volume.
     *
     * @return int
     */
    public function getVolume()
    {
        return (int) $this->info[9];
    }

    /**
     * Get the call price. (結算價)
     *
     * @return int
     */
    public function getCallPrice()
    {
        return (int) $this->info[10];
    }

    /**
     * Get the open interest.
     *
     * @return int
     */
    public function getOpenInterest()
    {
        return (int) $this->info[11];
    }

    /**
     * Get the open interest.
     *
     * @return int
     */
    public function getOI()
    {
        return $this->getOpenInterest();
    }

    /**
     * Get the trading period. (一般/盤後)
     *
     * @return string
     */
    public function getTradingPeriod()
    {
        return $this->info[17];
    }

    /**
     * Determine if the trading period is the given period.
     *
     * @param string $period
     * @return bool
     */
    public function tradingPeriodIs($period)
    {
        return $this->getTradingPeriod() === $period;
    }

    /**
     * Determine if the contact is a spread contract.
     *
     * @return bool
     */
    public function isSpreadContract()
    {
        return Str::contains($this->getExpiration(), "/");
    }
}
