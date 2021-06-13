<?php

namespace Tests\Normalizer\Taifex;

use App\Normalizer\Taifex\CommodityNormalizer;
use PHPUnit\Framework\TestCase;

class CommodityNormalizerTest extends TestCase
{
    /**
     * @testWith ["台指期貨", "臺指期貨"]
     *           ["台股期貨", "臺指期貨"]
     *           ["臺股期貨", "臺指期貨"]
     *           ["臺指期", "臺指期貨"]
     *           ["台指期", "臺指期貨"]
     *           ["大台", "臺指期貨"]
     *           ["小台", "小型臺指期貨"]
     *           ["小型台指期貨", "小型臺指期貨"]
     *           ["小型台股期貨", "小型臺指期貨"]
     *           ["小台期", "小型臺指期貨"]
     */
    public function test_normalize($givenInstitution, $expected)
    {
        $this->assertEquals($expected, CommodityNormalizer::normalize($givenInstitution));
    }
}
