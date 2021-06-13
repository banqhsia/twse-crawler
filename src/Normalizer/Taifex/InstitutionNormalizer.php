<?php

namespace App\Normalizer\Taifex;

use Illuminate\Support\Arr;

class InstitutionNormalizer
{
    /**
     * @var array<string,string>
     */
    protected static $institutionAlias = [
        "外資及陸資" => "外資",
        "自營商" => "自營",
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
