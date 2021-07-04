# twse-crawler
台股即時交易爬蟲 / 盤後資訊日報

## Installation
1. `$ git clone git@github.com:banqhsia/twse-crawler.git`
1. `$ composer install`
1. `$ cp .env.example .env` (以及編輯 `.env` 裡面的設定)
1. `$ php daily_report.php`

## 使用說明
* 在 .env 裡面可以設定要將 daily report 廣播到的 telegram 及使用的 bot 帳號

## 專有名詞
為了此專案的一致性，在不同情境下所使用的相同詞彙已儘量統一，防止不必要的誤解及維護困難。列表如下：

### 一般
| 中文 | 英文 | 附註 |
| --- | --- | --- |
| 代碼 | symbol |
| 開盤價 | open price |
| 收盤價 | close price |
| 昨日收盤價 | yesterday close price |
| 現價 | price |
| 最高價 | high price |
| 最低價 | low price |
| 漲跌價 | increase price |
| 漲跌幅 | increase percentage |
| 漲停 | increase limit up |
| 跌停 | decrease limit down |
| 單量 | trading volume |
| 成交量 | volume |
| 買價最佳五檔 | buy fifth order |
| 賣價最佳五檔 | ask fifth order |

## 期貨/選擇權
| 中文 | 英文 | 附註 |
| --- | --- | --- |
| 商品名稱 | commodity | 例：臺指期貨/小型臺指期貨 |
| 結算價 | call price |
| 到期月份 | expiration |
| 未平倉 | open interest / OI |
| 淨- | net- | 例: 淨未平倉 net OI |
| 交易時段 | trading period | 例：一般/盤後
| 價差合約 | spread contract |
| 多方交易口數 | long volume |
| 空方交易口數 | short volume |
| 多空交易口數淨額 | long short net volume |

### 法人機構
| 中文 | 英文 | 附註 |
| --- | --- | --- |
| 法人/機構 | institution | 例：外資/投信/

### 用字統一
* 「台」字皆已統一為「臺」
* 「外資及陸資」統一為「外資」
* 「自營商」統一為「自營」
* 「臺股期貨」統一為「臺指期貨」(小型臺指期貨亦同)
* 沒有簡稱：~~台指期~~、~~大台~~、~~小台~~、~~小台期~~

## 資料來源
### 盤後資訊
* [TWSE 臺灣證券交易所](https://www.twse.com.tw/)
* [TAIFEX 臺灣期貨交易所](https://www.taifex.com.tw/)
### 盤中個股資訊
* [TWSE 臺灣證券交易所](https://www.twse.com.tw/)
* [玉山富果證券 API](https://www.fugle.tw/) (如果您有使用)

