<?php

namespace App\Provider;

use App\Symbol;

trait SingleSymbol
{
    /**
     * @param Symbol $symbol
     * @return $this
     */
    public function setSymbol(Symbol $symbol)
    {
        $this->symbol = $symbol;

        return $this;
    }
}
