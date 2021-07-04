<?php

namespace App\Provider\Securities;

use App\Clock;
use App\Provider\Optionable;
use App\Quote\Twse\TwseInformationInfo;
use Psr\Http\Message\ResponseInterface;

class TwseInformationProvider implements Optionable
{
    const TYPE = 'MS';
    const URL_BASE = 'https://www.twse.com.tw/exchangeReport/MI_INDEX';

    /**
     * @var Clock
     */
    private $clock;

    /**
     * Construct.
     *
     * @param Clock $clock
     */
    public function __construct(Clock $clock)
    {
        $this->clock = $clock;
    }

    /**
     * {@inheritDoc}
     */
    public function getMethod()
    {
        return 'get';
    }

    /**
     * {@inheritDoc}
     */
    public function getOptions()
    {
        return [
            'query' => [
                'response' => 'json',
                'date' => $this->clock->getCurrentTradingDay()->format('Ymd'),
                'type' => static::TYPE,
            ],
        ];
    }

    /**
     * {@inheritDoc}
     */
    public function getDecodeFunction()
    {
        return function (ResponseInterface $resposne) {
            return [new TwseInformationInfo(
                json_decode($resposne->getBody()->getContents())
            )];
        };
    }

    /**
     * {@inheritDoc}
     */
    public function getUrl()
    {
        return static::URL_BASE;
    }
}
