<?php

namespace App\Normalizer\Taifex;

use Illuminate\Support\Arr;
use Illuminate\Support\Str;

class CommodityNormalizer
{
    /**
     * @var array<string,string>
     */
    protected static $commodityAlias = [
        '大臺' => '臺指期貨',
        '臺指期' => '臺指期貨',
        '小臺' => '小型臺指期貨',
        '小臺期' => '小型臺指期貨',
    ];

    /**
     * Normalize the commodity name.
     *
     * @param string $commodity
     * @return string
     */
    public static function normalize(string $commodity)
    {
        $commodity = str_replace("台", "臺", $commodity);

        if ($alias = Arr::get(static::$commodityAlias, $commodity)) {
            return $alias;
        }

        if (Str::endsWith($commodity, "期")) {
            $commodity .= "貨";
        }

        return str_replace("臺股期貨", "臺指期貨", $commodity);
    }
}
