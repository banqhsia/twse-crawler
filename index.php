<?php

use GuzzleHttp\Client;

require __DIR__ . '/vendor/autoload.php';

$client = new Client;
$codes = [
    '2337', '2330',
];

while (true) {
    $build = '';
    foreach ($codes as $code) {
        $build .= "tse_{$code}.tw|";
    }

    $response = $client->get("https://mis.twse.com.tw/stock/api/getStockInfo.jsp?ex_ch={$build}&_=" . time());

    $decoded = json_decode($response->getBody()->getContents(), true);

    $mapped = $decoded['msgArray'];

    foreach ($mapped as $info) {
        $result[$info['n']]['現價'] = $info['z'];
        $result[$info['n']]['委買'] = collect(explode('_', $info['b']))->combine(explode('_', $info['g']))->reject(function ($item) {return empty($item);})->toArray();
        $result[$info['n']]['委賣'] = collect(explode('_', $info['a']))->combine(explode('_', $info['f']))->reject(function ($item) {return empty($item);})->toArray();
        $result[$info['n']]['單量'] = $info['tv'];
        $result[$info['n']]['總量'] = $info['v'];
        $result[$info['n']]['時間'] = $info['t'];
    }

    dump($result);

    sleep(1);
}
