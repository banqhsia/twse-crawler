<?php

namespace App\Quote\Futures;

use App\Normalizer\Taifex\InstitutionNormalizer;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;

/**
 * The collection holding TaifexInstitutionCommodity classes.
 */
class TaifexInstitutionCommodityCollection extends Collection
{
    protected $prioritizations = [
        InstitutionNormalizer::FOREIGN_CAPITAL => 1,
        InstitutionNormalizer::DEALER => 2,
        InstitutionNormalizer::CAPITAL => 3,
    ];

    /**
     * "Group by" the collection using the commodity.
     *
     * @return TaifexInstitutionCommodityCollection
     */
    public function groupByCommodity(): TaifexInstitutionCommodityCollection
    {
        return $this->groupBy->getCommodity();
    }

    /**
     * "Order by" the institution according to the "prioritization map".
     *
     * @return TaifexInstitutionCommodityCollection
     */
    public function prioritizeInstitution(): TaifexInstitutionCommodityCollection
    {
        return $this->sortBy(function ($commodity) {
            return Arr::get($this->prioritizations, $commodity->getInstitution());
        });
    }
}
