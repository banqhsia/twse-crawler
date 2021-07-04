<?php

namespace App\Type;

class ChineseUnitValue
{
    /**
     * @var int
     */
    private $value = 0;

    /**
     * @var bool
     */
    private $isNegative = false;

    /**
     * @var int
     */
    protected $roundPrecision = 2;

    /**
     * @var int
     */
    protected $roundStrategy = PHP_ROUND_HALF_UP;

    /**
     * @var array<string,int>
     */
    protected $units = [
        '' => 1,
        '萬' => 10000,
        '億' => 100000000,
        '兆' => 1000000000000,
    ];

    /**
     * Construct.
     *
     * @param int|string $value
     */
    public function __construct($value)
    {
        $this->recognizeValue($value);
    }

    /**
     * Recognize the value.
     *
     * @param int|string $value
     * @return int
     */
    protected function recognizeValue($value)
    {
        $value = (int) filter_var($value, FILTER_SANITIZE_NUMBER_INT);

        if ($value < 0) {
            $this->isNegative = true;
        }

        $this->value = abs($value);
    }

    /**
     * Get the value with the unit.
     *
     * @return string
     */
    public function get()
    {
        $candidates = [];

        foreach ($this->units as $unit => $unitInNumber) {
            if ($this->value >= $unitInNumber) {
                $candidates[] = round(
                    $this->value / $unitInNumber, $this->roundPrecision, $this->roundStrategy
                );
            }
        }

        $units = collect($this->units)->take(count($candidates))->keys();

        $results = $units->combine($candidates)->map(function ($value, $unit) {
            $candidate = trim(sprintf('%s %s', $value, $unit));

            return $this->isNegative ? ("-{$candidate}") : $candidate;
        });

        return $results->last();
    }

    /**
     * Create the instance and get the result immediately.
     *
     * @param int|string $value
     * @return string
     */
    public static function value($value)
    {
        return static::make($value)->get();
    }

    /**
     * Create the instance.
     *
     * @param int|string $value
     * @return static
     */
    public static function make($value)
    {
        return new static($value);
    }
}
