<?php

namespace App\Transformer;

use App\Clock;
use App\Quote\Futures\TaifexInstitutionCommodity;
use App\Quote\Futures\TaifexInstitutionCommodityCollection;
use App\Quote\Options\TaifexFuturesInfo;
use App\Quote\Options\TaifexPutCallRatioInfo;
use App\Quote\TwseStockInfo;
use App\Quote\Twse\TwseInformationInfo;
use App\Template\Builder;
use App\Type\ChineseUnitValue;

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
     * @var TwseStockInfo
     */
    private $twseIndex;

    /**
     * @var TwseInformationInfo
     */
    private $twseInfo;

    /**
     * @var TaifexInstitutionCommodityCollection
     */
    private $institutionCommodities;

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

        *【 加權指數 】*
        開盤 `{tse_open_price}`
        收盤 `{tse_close_price}` (`{tse_increase_price}`, `{tse_increase_percentage}%`)
        高低 `{tse_highest_price}` / `{tse_lowest_price}` (幅 `{tse_range}`)
        昨收 `{tse_close_price_yesterday}`
        成交金額 `{tse_trade_value}`

        上漲家數 `{tse_increase_count}` (漲停 `{tse_increase_limit_up_count}`)
        下跌家數 `{tse_decrease_count}` (跌停 `{tse_decrease_limit_down_count}`)

        *【 選擇權 】*
        P/C ratio `{put_call_ratio}`
        PUT
          成交量 `{put_volume}` 口
          未平倉 `{put_oi}` 口
        CALL
          成交量 `{call_volume}` 口
          未平倉 `{call_oi}` 口

        *【 期貨 】*
        開盤 `{tx_open_price}`
        收盤 `{tx_close_price}` (`{tx_increase_price}`, `{tx_increase_percentage}%`)
        高低 `{tx_highest_price}` / `{tx_lowest_price}` (幅 `{tx_range}`)
        現貨 `{tse_close_price}` (價差 `{diff_stock_futures}`)

        {futures_institution_commodities}
        TEMPLATE;

        return Builder::template($template)->build([
            'today_date' => $this->clock->getCurrentTradingDay()->format('Y年m月d日'),
            'tse_open_price' => $this->twseIndex->getOpenPrice(),
            'tse_close_price' => $this->twseIndex->getClosePrice(),
            'tse_close_price_yesterday' => $this->twseIndex->getYesterdayClosePrice(),
            'tse_increase_price' => v($this->twseIndex->getIncreasePrice())->indicated(),
            'tse_increase_percentage' => $this->twseIndex->getIncreasePercentage(),
            'tse_highest_price' => $this->twseIndex->getHighPrice(),
            'tse_lowest_price' => $this->twseIndex->getLowPrice(),
            'tse_range' => $this->twseIndex->getHighPrice() - $this->twseIndex->getLowPrice(),
            'tse_trade_value' => ChineseUnitValue::value($this->twseInfo->getTradeValue()),
            'tse_increase_count' => $this->twseInfo->getIncreaseCount(),
            'tse_decrease_count' => $this->twseInfo->getDecreaseCount(),
            'tse_increase_limit_up_count' => $this->twseInfo->getIncreaseLimitUpCount(),
            'tse_decrease_limit_down_count' => $this->twseInfo->getDecreaseLimitDownCount(),
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
            'diff_stock_futures' => v($this->futuresInfo->getClosePrice() - $this->twseIndex->getClosePrice())->formatted(2),
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

        return $this->institutionCommodities
            ->prioritizeInstitution()
            ->groupByCommodity()
            ->transform(function ($institutionCommodities, $commodity) use ($commodityTemplate, $institutionTemplate) {
                return Builder::template($commodityTemplate)->build([
                    'commodity' => $commodity,
                    'institution_template' => $institutionCommodities->map(function (TaifexInstitutionCommodity $c) use ($institutionTemplate) {
                        return Builder::template($institutionTemplate)->build([
                            'institution' => $c->getInstitution(),
                            'long_short_net_volume' => v($c->getCurrentTradingDayInfo()->getLongShortNetVolume()),
                            'long_short_net_oi' => v($c->getCurrentTradingDayInfo()->getLongShortNetOpenInterest()),
                            'long_short_net_volume_compared_with_yesterday' => v($c->getCurrentTradingDayInfo()->getLongShortNetVolume())
                                ->sub($c->getLastTradingDayInfo()->getLongShortNetVolume())
                                ->signed(),
                            'long_short_net_oi_compared_with_yesterday' => v($c->getCurrentTradingDayInfo()->getLongShortNetOpenInterest())
                                ->sub($c->getLastTradingDayInfo()->getLongShortNetOpenInterest())
                                ->signed(),
                        ]);
                    })->join("\n"),
                ]);
            })->implode("\n");
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

    public function setTwseInfo(TwseInformationInfo $twseInfo)
    {
        $this->twseInfo = $twseInfo;

        return $this;
    }

    public function setTwseIndex(TwseStockInfo $twseIndex)
    {
        $this->twseIndex = $twseIndex;

        return $this;
    }

    public function setInstitutionCommodities(TaifexInstitutionCommodityCollection $institutionCommodities)
    {
        $this->institutionCommodities = $institutionCommodities;

        return $this;
    }
}
