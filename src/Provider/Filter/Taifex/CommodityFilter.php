<?php

namespace App\Provider\Filter\Taifex;

use App\Provider\Filter\FilterInterface;
use App\Quote\Futures\TaifexInstitutionCommodity;

class CommodityFilter implements FilterInterface
{
    /**
     * {@inheritDoc}
     */
    public function __invoke($result)
    {
        return TaifexInstitutionCommodity::createFromInstitutionInfos(
            $result->filter->commodityIs(['臺指期貨', '小型臺指期貨'])
        );
    }
}
