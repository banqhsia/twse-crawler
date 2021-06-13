<?php

namespace Tests\Normalizer\Taifex;

use App\Normalizer\Taifex\InstitutionNormalizer;
use PHPUnit\Framework\TestCase;

class InstitutionNormalizerTest extends TestCase
{
    /**
     * @testWith ["外資及陸資", "外資"]
     *           ["外資", "外資"]
     *           ["自營商", "自營"]
     *           ["自營", "自營"]
     *           ["投信", "投信"]
     *           ["花旗銀行", "花旗銀行"]
     */
    public function test_normalize($givenInstitution, $expected)
    {
        $this->assertEquals($expected, InstitutionNormalizer::normalize($givenInstitution));
    }
}
