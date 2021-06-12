<?php

namespace App\Quote\Futures;

use App\Quote\Options\TaifexInstitutionInfo;

class TaifexInstitutionCommodity
{
    /**
     * @var string
     */
    private $commodity;

    /**
     * @var string
     */
    private $institution;

    /**
     * @var TaifexInstitutionInfo[]
     */
    private $infos;

    /**
     * Construct.
     *
     * @param TaifexInstitutionInfo[] $infos
     */
    public function __construct($infos)
    {
        $this->infos = $infos;

        $this->commodity = $this->guessCommodity($infos);
        $this->institution = $this->guessInstitution($infos);
    }

    /**
     * @param TaifexInstitutionInfo[] $infos
     * @return string
     */
    protected function guessInstitution($infos)
    {
        return $infos[0]->getInstitution();
    }

    /**
     * @param TaifexInstitutionInfo[] $infos
     * @return string
     */
    protected function guessCommodity($infos)
    {
        return $infos[0]->getCommodity();
    }

    /**
     * @return TaifexInstitutionInfo
     */
    public function getCurrentTradingDayInfo()
    {
        // TODO: Should use a specify day
        return collect($this->infos)->sortByDesc(function (TaifexInstitutionInfo $info) {
            return $info->getDate();
        })->first();
    }

    /**
     * @return TaifexInstitutionInfo
     */
    public function getLastTradingDayInfo()
    {
        return collect($this->infos)->sortBy(function (TaifexInstitutionInfo $info) {
            return $info->getDate();
        })->first();
    }

    /**
     * Get the commodity name of this group.
     *
     * @return string
     */
    public function getCommodity()
    {
        return $this->commodity;
    }

    /**
     * Get the institution of this group.
     *
     * @return string
     */
    public function getInstitution()
    {
        return $this->institution;
    }

    /**
     * Create instances from TaifexInstitutionInfo[] array.
     *
     * @param TaifexInstitutionInfo[] $infos
     * @return static[]
     */
    public static function createFromInstitutionInfos($infos)
    {
        return collect($infos)->groupBy(function (TaifexInstitutionInfo $info) {
            return $info->getIdentifier();
        })->transform(function ($group) {
            return new TaifexInstitutionCommodity($group->all());
        })->values()->all();
    }
}
