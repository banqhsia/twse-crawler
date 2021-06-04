<?php

namespace App\Provider;

use App\Quote\FugleStockInfo;
use Psr\Http\Message\ResponseInterface;

class FugleProvider
{
    use SingleSymbol;

    const URL_BASE = 'https://api.fugle.tw/realtime/v0/intraday/quote';

    /**
     * @var string
     */
    private $apiToken;

    /**
     * Construct
     *
     * @param string $apiToken
     */
    public function __construct(string $apiToken)
    {
        $this->apiToken = $apiToken;
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

            return [new FugleStockInfo($decoded['data']['quote'])];
        };

    }

    /**
     * Get the URL.
     *
     * @return string
     */
    public function getUrl()
    {
        $urlBase = static::URL_BASE;

        $query = http_build_query([
            'symbolId' => $this->symbol->getId(),
            'apiToken' => $this->apiToken,
        ]);

        return "{$urlBase}?{$query}";
    }
}
