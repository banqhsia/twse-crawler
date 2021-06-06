<?php

namespace App\Quote\Options;

use Carbon\Carbon;

class TaifexInstitutionInfo
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
     * Get the name of commodity. E.g. "臺股期貨"
     *
     * @return string
     */
    public function getCommodity()
    {
        return $this->info[1];
    }

    /**
     * Get the institution type. E.g. "外資及陸資"
     *
     * @return string
     */
    public function getInstitution()
    {
        return $this->info[2];
    }

    /**
     * Get the long volume. (多方交易口數)
     *
     * @return int
     */
    public function getLongVolume()
    {
        return (int) $this->info[3];
    }

    /**
     * Get the short volume. (空方交易口數)
     *
     * @return int
     */
    public function getShortVolume()
    {
        return (int) $this->info[5];
    }

    /**
     * Get the long/short net volume. (多空交易口數淨額)
     *
     * @return int
     */
    public function getLongShortNetVolume()
    {
        return (int) $this->info[7];
    }

    /**
     * Get the long open interest (OI). (多方未平倉口數)
     *
     * @return int
     */
    public function getLongOpenInterest()
    {
        return (int) $this->info[9];
    }

    /**
     * Get the short open interest (OI). (空方未平倉口數)
     *
     * @return int
     */
    public function getShortOpenInterest()
    {
        return (int) $this->info[11];
    }

    /**
     * Get the long/short net open interest (OI). (多空未平倉口數淨額)
     *
     * @return int
     */
    public function getLongShortNetOpenInterest()
    {
        return (int) $this->info[13];
    }
}
