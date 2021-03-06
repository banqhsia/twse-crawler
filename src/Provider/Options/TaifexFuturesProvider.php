<?php

namespace App\Provider\Options;

use App\Provider\Filter\Filterable;
use App\Provider\Filter\Taifex\MonthlyFuturesFilter;
use App\Provider\Optionable;
use App\Quote\Options\TaifexFuturesInfo;
use Carbon\CarbonImmutable;
use Carbon\CarbonPeriod;
use DeepCopy\Filter\Filter;
use Psr\Http\Message\ResponseInterface;

class TaifexFuturesProvider implements Optionable, Filterable
{
    const URL_BASE = 'https://www.taifex.com.tw/cht/3/futDataDown';
    const DOWN_TYPE = 1;

    /**
     * @var CarbonPeriod
     */
    private $period;

    /**
     * @var string
     */
    private $symbol;

    /**
     * @var MonthlyFuturesFilter
     */
    private $filter;

    /**
     * Construct.
     *
     * @param MonthlyFuturesFilter $filter
     */
    public function __construct(MonthlyFuturesFilter $filter)
    {
        $this->filter = $filter;
    }

    /**
     * {@inheritDoc}
     */
    public function getFilter(): callable
    {
        return $this->filter;
    }

    /**
     * Set the date period of the query.
     *
     * @param CarbonPeriod $period
     * @return $this
     */
    public function setDatePeriod(CarbonPeriod $period)
    {
        $this->period = $period;

        return $this;
    }

    /**
     * Set the date of the query.
     *
     * @param string|\DateTimeImmutable|CarbonImmutable $date
     * @return $this
     */
    public function setDate($date)
    {
        if (!$date instanceof CarbonImmutable) {
            $date = CarbonImmutable::parse($date);
        }

        return $this->setDatePeriod($date->toPeriod($date));
    }

    /**
     * Set the symbol.
     *
     * @param string $symbol
     * @return $this
     *
     * @see https://www.taifex.com.tw/cht/3/futDailyMarketReport dropdown list.
     */
    public function setSymbol($symbol)
    {
        $this->symbol = $symbol;

        return $this;
    }

    /**
     * {@inheritDoc}
     */
    public function getMethod()
    {
        return 'post';
    }

    /**
     * {@inheritDoc}
     */
    public function getOptions()
    {
        return [
            'form_params' => [
                'down_type' => static::DOWN_TYPE,
                'commodity_id' => $this->symbol,
                'commodity_id2' => '',
                'queryStartDate' => $this->period->getStartDate()->format('Y/m/d'),
                'queryEndDate' => $this->period->getEndDate()->format('Y/m/d'),
            ],
        ];
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
                    $line = mb_convert_encoding($line, 'UTF-8', 'BIG-5');
                    $exploded = array_filter(explode(",", $line));

                    return new TaifexFuturesInfo($exploded);
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
