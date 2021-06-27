<?php

namespace App\Provider\Options;

use App\Provider\Filter\Filterable;
use App\Provider\Filter\Taifex\CommodityFilter;
use App\Provider\Optionable;
use App\Quote\Options\TaifexInstitutionInfo;
use Carbon\CarbonPeriod;
use Psr\Http\Message\ResponseInterface;

class TaifexInstitutionProvider implements Optionable, Filterable
{
    const URL_BASE = 'https://www.taifex.com.tw/cht/3/futContractsDateDown';

    /**
     * @var string
     */
    private $symbol = '';

    /**
     * @var CarbonPeriod
     */
    private $period;

    /**
     * @var CommodityFilter
     */
    private $filter;

    /**
     * Construct.
     *
     * @param CommodityFilter $filter
     */
    public function __construct(CommodityFilter $filter)
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
     * Set the symbol.
     *
     * @param string $symbol
     * @return $this
     *
     * @see https://www.taifex.com.tw/cht/3/futContractsDate dropdown list.
     */
    public function setSymbol($symbol)
    {
        $this->symbol = $symbol;

        return $this;
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
                /** Not sure if this 2 columns are needed */
                // 'firstDate' => '2018/06/04+00:00',
                // 'lastDate' => '2021/06/04+00:00',
                'queryStartDate' => $this->period->getStartDate()->format('Y/m/d'),
                'queryEndDate' => $this->period->getEndDate()->format('Y/m/d'),
                'commodityId' => $this->symbol,
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
                    $exploded = explode(",", mb_convert_encoding($line, 'UTF-8', 'big-5'));

                    return new TaifexInstitutionInfo($exploded);
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
    protected function isLineValid($line, $index)
    {
        /** index [0] is the title of csv response. */
        return (0 !== $index) && !empty($line);
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
