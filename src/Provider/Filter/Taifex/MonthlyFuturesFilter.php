<?php

namespace App\Provider\Filter\Taifex;

use App\Provider\CommodityGuesser;
use App\Provider\Filter\FilterInterface;
use App\Quote\Options\TaifexFuturesInfo;

class MonthlyFuturesFilter implements FilterInterface
{
    /**
     * @var CommodityGuesser
     */
    private $commodityGuesser;

    /**
     * Construct.
     *
     * @param CommodityGuesser $commodityGuesser
     */
    public function __construct(CommodityGuesser $commodityGuesser)
    {
        $this->commodityGuesser = $commodityGuesser;
    }

    /**
     * {@inheritDoc}
     */
    public function __invoke($result)
    {
        return $result->filter(function (TaifexFuturesInfo $info) {
            $expiration = $this->commodityGuesser->getMonthly();

            return $info->expirationIs($expiration) && $info->tradingPeriodIs('一般');
        })->first();
    }
}
