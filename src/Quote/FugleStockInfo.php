<?php

namespace App\Quote;

use App\Quote\StockInfo;
use Illuminate\Support\Arr;

class FugleStockInfo implements StockInfo
{
    /**
     * @var array
     */
    private $stockInfo;

    /**
     * Construct
     *
     * @param array $stockInfo
     */
    public function __construct($stockInfo)
    {
        $this->stockInfo = $stockInfo;
    }

    /**
     * 取得個股名稱
     *
     * @return string
     */
    public function getName()
    {
        return $this->retrieveInfo('n');
    }

    /**
     * 取得現在價格 (現價)
     *
     * @return string
     */
    public function getPrice()
    {
        return $this->retrieveInfo('trade.price');
    }

    /**
     * 取得盤中最低價
     *
     * @return string
     */
    public function getLowPrice()
    {
        return $this->retrieveInfo('priceLow.price');
    }

    /**
     * 取得盤中最高價
     *
     * @return string
     */
    public function getHighPrice()
    {
        return $this->retrieveInfo('priceHigh.price');
    }

    /**
     * 取得開盤價
     *
     * @return string
     */
    public function getOpenPrice()
    {
        return $this->retrieveInfo('priceOpen.price');
    }

    /**
     * 取得昨日收盤價
     *
     * @return string
     */
    public function getYesterdayClosePrice()
    {
        return $this->retrieveInfo('y');
    }

    /**
     * 取得漲跌幅
     *
     * @return float
     */
    public function getIncreasePercentage()
    {
        return round(
            ($this->getIncreasePrice() / $this->getOpenPrice()) * 100, 2, PHP_ROUND_HALF_UP
        );
    }

    /**
     * 取得漲跌價格
     *
     * @return float
     */
    public function getIncreasePrice()
    {
        return (float) $this->getPrice() - $this->getYesterdayClosePrice();
    }

    /**
     * 取得單量
     *
     * @return int
     */
    public function getTradingVolume()
    {
        return (int) $this->retrieveInfo('trade.unit');
    }

    /**
     * 取得總量
     *
     * @return int
     */
    public function getVolume()
    {
        return (int) $this->retrieveInfo('total.unit');
    }

    /**
     * 取得買價最佳五檔
     *
     * @return array<string, int>
     */
    public function getBuyFifthOrder()
    {
        return collect($this->retrieveInfo('order.bestBids'))->mapWithKeys(function($tick) {
            return [number_format($tick['price'], 2) => $tick['unit']];
        })->sortByDesc(function($volume, $price) {
            return $price;
        })->toArray();
    }

    /**
     * 取得賣價最佳五檔
     *
     * @return array<string, int>
     */
    public function getAskFifthOrder()
    {
        return collect($this->retrieveInfo('order.bestAsks'))->mapWithKeys(function($tick) {
            return [number_format($tick['price'], 2) => $tick['unit']];
        })->sortBy(function($volume, $price) {
            return $price;
        })->toArray();
    }

    /**
     * 取得此盤的時間
     *
     * @return \DateTimeImmutable
     */
    public function getTime()
    {
        return (new \DateTimeImmutable($this->retrieveInfo('trade.at'), new \DateTimeZone('UTC')))
            ->setTimezone(new \DateTimeZone('Asia/Taipei'));

    }

    /**
     * @param string $field
     * @param mixed $default
     * @return mixed
     */
    protected function retrieveInfo($field, $default = null)
    {
        return Arr::get($this->stockInfo, $field, $default);
    }
}
