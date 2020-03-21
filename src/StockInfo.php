<?php

namespace App;

use Illuminate\Support\Arr;

class StockInfo
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
        return $this->retrieveInfo('z');
    }

    /**
     * 取得盤中最低價
     *
     * @return string
     */
    public function getLowPrice()
    {
        return $this->retrieveInfo('l');
    }

    /**
     * 取得盤中最高價
     *
     * @return string
     */
    public function getHighPrice()
    {
        return $this->retrieveInfo('h');
    }

    /**
     * 取得開盤價
     *
     * @return string
     */
    public function getOpenPrice()
    {
        return $this->retrieveInfo('o');
    }

    public function getYesterdayClosePrice()
    {
        return $this->retrieveInfo('y');
    }

    public function getIncreasePercentage()
    {
        return round(
            ($this->getIncreasePrice() / $this->getOpenPrice()) * 100, 2, PHP_ROUND_HALF_UP
        );
    }

    public function getIncreasePrice()
    {
        return $this->getPrice() - $this->getYesterdayClosePrice();
    }

    /**
     * 取得單量
     *
     * @return int
     */
    public function getTradingVolume()
    {
        return (int) $this->retrieveInfo('tv');
    }

    /**
     * 取得總量
     *
     * @return int
     */
    public function getVolume()
    {
        return (int) $this->retrieveInfo('v');
    }

    /**
     * 取得買價最佳五檔
     *
     * @return array<string, int>
     */
    public function getBuyFifthOrder()
    {
        return collect(
            explode('_', $this->retrieveInfo('b'))
        )->combine(
            array_map('intval', explode('_', $this->retrieveInfo('g')))
        )->reject(
            function ($item) {
                return empty($item);
            }
        )->toArray();
    }

    /**
     * 取得賣價最佳五檔
     *
     * @return array<string, int>
     */
    public function getAskFifthOrder()
    {
        return collect(
            explode('_', $this->retrieveInfo('a'))
        )->combine(
            array_map('intval', explode('_', $this->retrieveInfo('f')))
        )->reject(
            function ($item) {
                return empty($item);
            }
        )->toArray();
    }

    /**
     * 取得此盤的時間
     *
     * @return \DateTimeImmutable
     */
    public function getTime()
    {
        return new \DateTimeImmutable($this->retrieveInfo('t'));
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
