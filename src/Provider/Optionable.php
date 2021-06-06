<?php

namespace App\Provider;

interface Optionable
{
    /**
     * Get the Guzzle request options.
     *
     * @return array
     */
    public function getOptions();

    /**
     * Get the request method (HTTP verb).
     *
     * @return string
     */
    public function getMethod();
}
