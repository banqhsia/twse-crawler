<?php

namespace App\Provider;

use App\Quote\TwseStockInfo;
use App\Symbol;
use Psr\Http\Message\ResponseInterface;

class TwseProvider implements DateUnaware
{
    const URL_BASE = 'https://mis.twse.com.tw/stock/api/getStockInfo.jsp';

    /**
     * Set the symbols.
     *
     * @param Symbol[] $symbols
     * @return $this
     */
    public function setSymbols($symbols)
    {
        $this->symbols = $symbols;

        return $this;
    }

    /**
     * Get the result processing function.
     *
     * @return \Closure
     */
    public function getDecodeFunction()
    {
        return function (ResponseInterface $response) {
            $decoded = json_decode($response->getBody()->getContents(), true);

            $mapped = collect($decoded['msgArray'])->mapInto(TwseStockInfo::class);

            return $mapped;
        };
    }

    /**
     * Get the URL.
     *
     * @return string
     */
    public function getUrl()
    {
        $baseUrl = static::URL_BASE;

        $codes = collect($this->symbols)->transform(function (Symbol $code) {
            return "{$code->getMarket()}_{$code->getId()}.tw";
        })->implode("|");

        $query = urldecode(http_build_query([
            'ex_ch' => $codes,
            '_' => time(),
        ]));

        return "{$baseUrl}?{$query}";
    }
}
