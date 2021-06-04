<?php

namespace App;

class Symbol
{
    /**
     * @var string
     */
    private $id;

    /**
     * @var string
     */
    private $market;

    /**
     * @var string
     */
    private $name;

    /**
     * Construct
     *
     * @param string $id
     * @param string $market
     * @param string? $name
     */
    public function __construct(string $id, string $market, string $name = null)
    {
        $this->id = $id;
        $this->market = $market;
        $this->name = $name;
    }

    /**
     * @return string
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getMarket()
    {
        return $this->market;
    }

    /**
     * @var string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return $this->id;
    }

    /**
     * @param string[] $symbols
     * @return static[]
     */
    public static function createSymbols($symbols)
    {
        return collect($symbols)->map(function ($symbol) {
            $exploded = explode('.', $symbol);

            return new static($exploded[1], $exploded[0]);
        })->toArray();
    }

    /**
     * @param string $envWatchingStocksString
     * @return static[]
     */
    public static function createSymbolsFromEnvString(string $envWatchingStocksString)
    {
        return static::createSymbols(explode(',', $envWatchingStocksString));
    }
}
