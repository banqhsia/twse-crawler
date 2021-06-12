<?php

namespace App\Quote\Options;

use Carbon\Carbon;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;

class TaifexInstitutionInfo
{
    /**
     * @var array<string,string>
     */
    protected $institutionAlias = [
        "外資及陸資" => "外資",
        "自營商" => "自營",
    ];

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
     * Get the name of commodity. E.g. "臺指期貨"
     *
     * @return string
     */
    public function getCommodity()
    {
        $commodity = Str::replaceFirst("台", "臺", $this->info[1]);

        if (Str::is("臺股期貨", $commodity)) {
            return "臺指期貨";
        }

        return $commodity;
    }

    /**
     * Get the institution type. E.g. "外資"
     *
     * @return string
     */
    public function getInstitution()
    {
        $institution = $this->getInstitutionOrigin();

        if ($alias = Arr::get($this->institutionAlias, $institution)) {
            return $alias;
        }

        return $institution;
    }

    /**
     * Get the origin unmodified institution type. E.g. "外資及陸資"
     *
     * @return string
     */
    public function getInstitutionOrigin()
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

    /**
     * Get the identifier. This indicates if they are the same commodity and
     * institution.
     *
     * @return string
     */
    public function getIdentifier()
    {
        return md5($this->getCommodity() . $this->getInstitution());
    }
}
