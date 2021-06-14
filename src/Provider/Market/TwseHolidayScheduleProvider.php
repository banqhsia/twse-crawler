<?php

namespace App\Provider\Market;

use App\Quote\Market\TwseHolidayScheduleInfo;
use Psr\Http\Message\ResponseInterface;

class TwseHolidayScheduleProvider
{
    const URL_BASE = 'https://openapi.twse.com.tw/v1/holidaySchedule/holidaySchedule';

    /**
     * Get the result processing function.
     *
     * @return \Closure
     */
    public function getDecodeFunction()
    {
        return function (ResponseInterface $response) {
            $decoded = json_decode($response->getBody()->getContents());

            return collect($decoded)->mapInto(TwseHolidayScheduleInfo::class);
        };
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
