# twse-crawler
台股即時交易資訊爬蟲

## Installation
1. `$ git clone git@github.com:banqhsia/twse-crawler.git`
1. `$ composer install`
1. `$ php index.php`

## 使用說明
1. 在 `index.php` 中的 `$code = []` 可以設定股票代號
2. 可以自行從 `sleep(1)` 調整抓取頻率

## 資料來源
* 臺灣證券交易所 API (mis.twse.com.tw)