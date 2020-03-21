<?php

namespace App;

class CodeBuilder
{
    /**
     * 建立呼叫用的股票代碼
     *
     * @param string|string[] $codes
     * @return string
     */
    public static function buildStock($codes)
    {
        return collect($codes)->transform(function ($code) {
            return "tse_{$code}.tw";
        })->implode("|");
    }
}
