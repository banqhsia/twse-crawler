<?php

use App\StockInfo;
use App\CodeBuilder;
use GuzzleHttp\Client;

require __DIR__ . '/vendor/autoload.php';

$client = new Client;
$codes = [
    '2337', '2330',
];

while (true) {
    $build = CodeBuilder::buildStock($codes);

    $response = $client->get("https://mis.twse.com.tw/stock/api/getStockInfo.jsp?ex_ch={$build}&_=" . time());

    $decoded = json_decode($response->getBody()->getContents(), true);

    $mapped = collect($decoded['msgArray'])->mapInto(StockInfo::class);

    $result = $mapped->mapWithKeys(function (StockInfo $info) {
        return [$info->getName() => [
            '昨收' => $info->getYesterdayClosePrice(),
            '開盤' => $info->getOpenPrice(),
            '漲幅' => "{$info->getIncreasePrice()} ({$info->getIncreasePercentage()}%)",
            '現價' => $info->getPrice(),
            '委買' => $info->getBuyFifthOrder(),
            '委賣' => $info->getAskFifthOrder(),
            '單量' => $info->getTradingVolume(),
            '總量' => $info->getVolume(),
            '時間' => $info->getTime(),
        ]];
    });

    dump($result);

    sleep(1);
}
