<?php

namespace App\Normalizer\Taifex;

use Illuminate\Support\Arr;

class InstitutionNormalizer
{
    public const FOREIGN_CAPITAL = "外資";
    public const CAPITAL = "投信";
    public const DEALER = "自營";

    /**
     * @var array<string,string>
     */
    protected static $institutionAlias = [
        "外資及陸資" => self::FOREIGN_CAPITAL,
        "自營商" => self::DEALER,
    ];

    /**
     * Normalize the institution name.
     *
     * @param string $institution
     * @return string
     */
    public static function normalize(string $institution)
    {
        if ($alias = Arr::get(static::$institutionAlias, $institution)) {
            return $alias;
        }

        return $institution;
    }
}
