<?php

namespace App\Quote\Options;

use Carbon\Carbon;
use DateTimeZone;

class TaifexPutCallRatioInfo
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
        return Carbon::parse($this->info[0], new DateTimeZone('Asia/Taipei'));
    }

    /**
     * Get PUT volume.
     *
     * @return int
     */
    public function getPutVolume()
    {
        return (int) $this->info[1];
    }

    /**
     * Get CALL volume.
     *
     * @return int
     */
    public function getCallVolume()
    {
        return (int) $this->info[2];
    }

    /**
     * Get PUT/CALL volume percentage. (PUT volume/CALL volume)
     *
     * @return float
     */
    public function getPutCallVolumePercentage()
    {
        return (float) $this->info[3];
    }

    /**
     * Get PUT open interest (OI).
     *
     * @return int
     */
    public function getPutOpenInterest()
    {
        return (int) $this->info[4];
    }

    /**
     * Get PUT open interest (OI).
     *
     * @return int
     */
    public function getPutOI()
    {
        return $this->getPutOpenInterest();
    }

    /**
     * Get CALL open interest (OI).
     *
     * @return int
     */
    public function getCallOpenInterest()
    {
        return (int) $this->info[5];
    }

    /**
     * Get CALL open interest (OI).
     *
     * @return int
     */
    public function getCallOI()
    {
        return $this->getCallOpenInterest();
    }

    /**
    * Get PUT/CALL ratio (P/C ratio).
    *
    * @return int
    */
    public function getPutCallRatio()
    {
        return (float) $this->info[6];
    }
}
