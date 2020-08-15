<?php

namespace App\Quote;

interface StockInfo
{
    /**
     * 取得個股名稱
     *
     * @return string
     */
    public function getName();

    /**
     * 取得現在價格 (現價)
     *
     * @return string
     */
    public function getPrice();

    /**
     * 取得盤中最低價
     *
     * @return string
     */
    public function getLowPrice();

    /**
     * 取得盤中最高價
     *
     * @return string
     */
    public function getHighPrice();

    /**
     * 取得開盤價
     *
     * @return string
     */
    public function getOpenPrice();

    /**
     * 取得昨日收盤價
     *
     * @return string
     */
    public function getYesterdayClosePrice();

    /**
     * 取得漲跌幅
     *
     * @return float
     */
    public function getIncreasePercentage();

    /**
     * 取得漲跌價格
     *
     * @return float
     */
    public function getIncreasePrice();

    /**
     * 取得單量
     *
     * @return int
     */
    public function getTradingVolume();

    /**
     * 取得總量
     *
     * @return int
     */
    public function getVolume();

    /**
     * 取得買價最佳五檔
     *
     * @return array<string, int>
     */
    public function getBuyFifthOrder();

    /**
     * 取得賣價最佳五檔
     *
     * @return array<string, int>
     */
    public function getAskFifthOrder();

    /**
     * 取得此盤的時間
     *
     * @return \DateTimeImmutable
     */
    public function getTime();
}
