<?php

namespace App\Quote\Options;

use App\Normalizer\Taifex\CommodityNormalizer;
use App\Normalizer\Taifex\InstitutionNormalizer;
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
     * Get the name of commodity. E.g. "臺指期貨"
     *
     * @return string
     */
    public function getCommodity()
    {
        return CommodityNormalizer::normalize($this->info[1]);
    }

    /**
     * Determine if the commodity is one of the given names.
     *
     * @param string[] $commodities
     * @return bool
     */
    public function commodityIs(array $commodities = [])
    {
        return in_array($this->getCommodity(), $commodities);
    }

    /**
     * Get the institution type. E.g. "外資"
     *
     * @return string
     */
    public function getInstitution()
    {
        return InstitutionNormalizer::normalize($this->getInstitutionOrigin());
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
