<?php

namespace App\Type;

class Value
{
    /**
     * @var int|float
     */
    protected $value;

    /**
     * The increasing/decreasing indicators.
     *
     * @var array<int,string>
     */
    protected $indicators = [
        1 => "▲",
        0 => "",
        -1 => "▼",
    ];

    /**
     * Construct.
     *
     * @param int|float $value
     */
    public function __construct($value)
    {
        $this->value = $value;
    }

    /**
     * Get the indicator. For increasing or decreasing.
     *
     * @return string
     */
    public function indicator()
    {
        return $this->indicators[$this->value <=> 0];
    }

    /**
     * Get the value with indicator prepend.
     *
     * For example:  15 will return ▲15
     *              -15 will return ▼15
     *
     * @return string
     */
    public function indicated()
    {
        return $this->indicator() . $this->absolute();
    }

    /**
     * Get the value with sign indicator prepend.
     *
     * For example:  15 will return +15
     *              -15 will return -15
     *                0 will return   0
     *
     * @return string
     */
    public function signed()
    {
        if ($this->value > 0) {
            return "+" . $this->value;
        }

        return $this->value;
    }

    /**
     * Get the absolute value.
     *
     * @return int|float
     */
    public function absolute()
    {
        return abs($this->value);
    }

    /**
     * @param int|float|static $value
     */
    public function sub($value)
    {
        if ($value instanceof Value) {
            $value = $value->value();
        }

        return static::make($this->value() - $value);
    }

    /**
     * Get the formatted value.
     *
     * @param int $decimals
     * @return string
     */
    public function formatted(int $decimals = 0)
    {
        return number_format($this->value, $decimals);
    }

    /**
     * {@inheritDoc}
     */
    public function __toString()
    {
        return $this->formatted();
    }

    /**
     * Get the value.
     *
     * @return int|float
     */
    public function value()
    {
        return $this->value;
    }

    /**
     * @param int|float|Value $value
     * @return static
     */
    public static function make($value)
    {
        if ($value instanceof Value) {
            $value = $value->value();
        }

        return new static($value);
    }
}
