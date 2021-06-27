<?php

namespace App\Provider\Filter;

use Illuminate\Support\Collection;

interface FilterInterface
{
    /**
     * The implementation of the result filter.
     *
     * @param Collection<mixed>
     * @return mixed
     */
    public function __invoke($result);
}
