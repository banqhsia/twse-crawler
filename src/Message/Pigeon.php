<?php

namespace App\Message;

use Longman\TelegramBot\Entities\ServerResponse;
use Longman\TelegramBot\Request;

class Pigeon
{
    /**
     * @var Request
     */
    private $request;

    /**
     * Construct.
     *
     * @param Request $request
     */
    public function __construct(Request $request)
    {
        $this->request = $request;
    }

    /**
     * Send the message.
     *
     * @param Envelope $envelope
     * @return ServerResponse
     */
    public function send(Envelope $envelope)
    {
        return $this->request->sendMessage([
            'chat_id' => $envelope->getReceiver(),
            'parse_mode' => $envelope->getMessage()->getParseMode(),
            'text' => $envelope->getMessage()->getText(),
        ]);
    }
}
