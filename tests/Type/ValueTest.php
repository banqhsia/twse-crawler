<?php

namespace Tests\Type;

use App\Type\Value;
use PHPUnit\Framework\TestCase;

class ValueTest extends TestCase
{
    /**
     * @testWith [150, "▲"]
     *           [1.5, "▲"]
     *           [-150, "▼"]
     *           [-1.5, "▼"]
     *           [0, ""]
     *           [-0, ""]
     */
    public function test_get_indicator($value, $expected)
    {
        $actual = Value::make($value)->indicator();

        $this->assertEquals($expected, $actual);
    }
}
