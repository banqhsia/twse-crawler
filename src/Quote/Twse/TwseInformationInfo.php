<?php

namespace App\Quote\Twse;

class TwseInformationInfo
{
    /**
     * @var \stdClass
     */
    private $info;

    /**
     * Construct.
     *
     * @param \stdClass $info
     */
    public function __construct($info)
    {
        $this->info = $info;
    }

    /**
     * Get the count of increasing symbols. (上漲家數)
     *
     * @return int
     */
    public function getIncreaseCount()
    {
        return (int) $this->splitValueAroundParentheses(data_get($this->info, 'data8.0.2'))[1];
    }

    /**
     * Get the count of increase limit up. (漲停家數)
     *
     * @return int
     */
    public function getIncreaseLimitUpCount()
    {
        return (int) $this->splitValueAroundParentheses(data_get($this->info, 'data8.0.2'))[2];
    }

    /**
     * Get the count of decreasing symbols. (下跌家數)
     *
     * @return int
     */
    public function getDecreaseCount()
    {
        return (int) $this->splitValueAroundParentheses(data_get($this->info, 'data8.1.2'))[1];

    }

    /**
     * Get the count of decrease limit dow. (跌停家數)
     *
     * @return int
     */
    public function getDecreaseLimitDownCount()
    {
        return (int) $this->splitValueAroundParentheses(data_get($this->info, 'data8.1.2'))[2];
    }

    /**
     * Split the string around parentheses into an array. 100(60) = [100, 60]
     *
     * @param string $value
     * @return int[]
     */
    protected function splitValueAroundParentheses($value)
    {
        preg_match('/(\d{1,})\((\d{1,})\)/', $value, $match);

        return $match;
    }

    /**
     * Get the trade value. (成交金額)
     *
     * @return string
     */
    public function getTradeValue()
    {
        $value = data_get($this->info, 'data7.16.1');

        return (int) filter_var($value, FILTER_SANITIZE_NUMBER_INT);
    }
}
