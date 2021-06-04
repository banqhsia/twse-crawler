<?php

namespace App;

use GuzzleHttp\Client as BaseClient;
use Psr\Http\Message\ResponseInterface;

class Client
{
    /**
     * @var \Closure|null
     */
    private $decoder = null;

    /**
     * @var BaseClient
     */
    private $client;

    /**
     * Construct.
     *
     * @param BaseClient $client
     */
    public function __construct(BaseClient $client)
    {
        $this->client = $client;
    }

    /**
     * Set the decoder.
     *
     * @param \Closure $decoder
     *
     * @return void
     */
    public function setDecoder(\Closure $decoder)
    {
        $this->decoder = $decoder;

        return $this;
    }

    /**
     * Get the decoder.
     *
     * @return \Closure
     */
    protected function getDecoder()
    {
        if ($this->decoder) {
            return $this->decoder;
        }

        return function (ResponseInterface $response) {
            return json_decode($response->getBody()->getContents(), true);
        };
    }

    /**
     * Send the request and decode the response.
     *
     * @param $provider
     * @return mixed
     */
    public function get($provider)
    {
        $response = $this->client->get($provider->getUrl());

        return call_user_func($provider->getDecodeFunction(), $response);
    }
}
