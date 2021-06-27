<?php

namespace App\Provider\Filter;

interface Filterable
{
    /**
     * Get the filter function.
     *
     * @return callable
     */
    public function getFilter(): callable;
}
