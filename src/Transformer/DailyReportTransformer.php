<?php

namespace App\Transformer;

use App\Clock;
use App\Quote\Futures\TaifexInstitutionCommodity;
use App\Quote\Options\TaifexFuturesInfo;
use App\Quote\Options\TaifexPutCallRatioInfo;
use App\Template\Builder;
use Illuminate\Support\Arr;

class DailyReportTransformer
{
    /**
     * @var TaifexPutCallRatioInfo
     */
    private $putCallRatioInfo;

    /**
     * @var TaifexFuturesInfo
     */
    private $futuresInfo;

    /**
     * @var TaifexInstitutionCommodity[]
     */
    private $institutionCommodities = [];

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
     * @return string
     */
    public function transform()
    {
        $template = <<<TEMPLATE
        {today_date} 市場快照

        *【 選擇權 】*
        P/C ratio `{put_call_ratio}`
        PUT
          成交量 `{put_volume}` 口
          未平倉 `{put_oi}` 口
        CALL
          成交量 `{call_volume}` 口
          未平倉 `{call_oi}` 口

        *【 期貨 】*
        開收 `{tx_open_price}` / `{tx_close_price}` (`{tx_increase_price}`, `{tx_increase_percentage}%`)
        高低 `{tx_highest_price}` / `{tx_lowest_price}` (幅 `{tx_range}`)

        {futures_institution_commodities}
        TEMPLATE;

        return Builder::template($template)->build([
            'today_date' => $this->clock->getCurrentTradingDay()->format('Y年m月d日'),
            'put_call_ratio' => $this->putCallRatioInfo->getPutCallRatio(),
            'put_volume' => v($this->putCallRatioInfo->getPutVolume()),
            'put_oi' => v($this->putCallRatioInfo->getPutOpenInterest()),
            'call_volume' => v($this->putCallRatioInfo->getCallVolume()),
            'call_oi' => v($this->putCallRatioInfo->getCallOpenInterest()),
            'tx_expiration' => $this->futuresInfo->getExpiration(),
            'tx_open_price' => $this->futuresInfo->getOpenPrice(),
            'tx_highest_price' => $this->futuresInfo->getHighPrice(),
            'tx_lowest_price' => $this->futuresInfo->getLowPrice(),
            'tx_close_price' => $this->futuresInfo->getClosePrice(),
            'tx_increase_price' => v($this->futuresInfo->getIncreasePrice())->indicated(),
            'tx_increase_percentage' => $this->futuresInfo->getIncreasePercentage(),
            'tx_range' => $this->futuresInfo->getHighPrice() - $this->futuresInfo->getLowPrice(),
            'futures_institution_commodities' => $this->mapCommodities(),
        ]);
    }

    /**
     * Map the commodities.
     *
     * @return string
     */
    protected function mapCommodities()
    {
        $commodityTemplate = <<<TEMPLATE
        <{commodity}> 買賣淨額 // 淨未平倉
        {institution_template}

        TEMPLATE;

        $institutionTemplate = <<<TEMPLATE
        {institution} // `{long_short_net_volume}` (`{long_short_net_volume_compared_with_yesterday}`) // `{long_short_net_oi}` (`{long_short_net_oi_compared_with_yesterday}`)
        TEMPLATE;

        $institutionCommodities = collect($this->institutionCommodities)->groupBy->getCommodity();

        $result = [];
        foreach ($institutionCommodities as $commodity => $institutionCommodities) {
            $institutionResult = collect($institutionCommodities)
                ->transform(function (TaifexInstitutionCommodity $c) use ($institutionTemplate) {

                    $today = $c->getCurrentTradingDayInfo();
                    $yesterday = $c->getLastTradingDayInfo();

                    return Builder::template($institutionTemplate)->build([
                        'institution' => $c->getInstitution(),
                        'long_short_net_volume' => v($today->getLongShortNetVolume()),
                        'long_short_net_oi' => v($today->getLongShortNetOpenInterest()),
                        'long_short_net_volume_compared_with_yesterday' => v($today->getLongShortNetVolume())
                            ->sub($yesterday->getLongShortNetVolume())
                            ->indicated(),
                        'long_short_net_oi_compared_with_yesterday' => v($today->getLongShortNetOpenInterest())
                            ->sub($yesterday->getLongShortNetOpenInterest())
                            ->indicated(),
                    ]);
                })->join("\n");

            $result[] = Builder::template($commodityTemplate)->build([
                'commodity' => $commodity,
                'institution_template' => $institutionResult,
            ]);
        }

        return implode("\n", $result);
    }

    public function setPutCallRatioInfo(TaifexPutCallRatioInfo $putCallRatioInfo)
    {
        $this->putCallRatioInfo = $putCallRatioInfo;

        return $this;
    }

    public function setFuturesInfo(TaifexFuturesInfo $futuresInfo)
    {
        $this->futuresInfo = $futuresInfo;

        return $this;
    }

    public function setInstitutionCommodity($institutionCommodities)
    {
        $this->institutionCommodities = array_merge(
            $this->institutionCommodities,
            Arr::wrap($institutionCommodities)
        );

        return $this;
    }
}
