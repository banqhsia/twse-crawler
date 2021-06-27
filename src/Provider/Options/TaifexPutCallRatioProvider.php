<?php

namespace App\Provider\Options;

use App\Provider\Filter\Filterable;
use App\Quote\Options\TaifexPutCallRatioInfo;
use Psr\Http\Message\ResponseInterface;

class TaifexPutCallRatioProvider implements Filterable
{
    const URL_BASE = 'https://www.taifex.com.tw/cht/3/pcRatioDown';

    /**
     * {@inheritDoc}
     */
    public function getFilter(): callable
    {
        return function ($result) {
            return $result->first();
        };
    }

    /**
     * Get the result processing function.
     *
     * @return \Closure
     */
    public function getDecodeFunction()
    {
        return function (ResponseInterface $response) {
            $exploded = explode("\r\n", $response->getBody()->getContents());

            return collect($exploded)
                ->filter(\Closure::fromCallable([$this, 'isLineValid']))
                ->transform(function ($line) {
                    $exploded = array_filter(explode(",", $line));

                    return new TaifexPutCallRatioInfo($exploded);
                })
                ->values()
                ->toArray();
        };
    }

    /**
     * Determine if the line is valid.
     *
     * @param string $line
     * @return bool
     */
    protected function isLineValid($line)
    {
        return mb_detect_encoding($line, 'UTF-8') && !empty($line);
    }

    /**
     * Get the URL.
     *
     * @return string
     */
    public function getUrl()
    {
        return static::URL_BASE;
    }
}
