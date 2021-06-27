<?php

namespace App\Message;

use Illuminate\Container\Container;

class Envelope
{
    /**
     * The receiver.
     *
     * @var string|int
     */
    private $receiver;

    /**
     * @var Message
     */
    private $message;

    /**
     * Construct.
     *
     * @param string|int $receiver
     * @param Message|null $message
     */
    public function __construct($receiver, Message $message = null)
    {
        $this->receiver = $receiver;
        $this->message = $message;
    }

    /**
     * Get the receiver.
     *
     * @return string|int
     */
    public function getReceiver()
    {
        return $this->receiver;
    }

    /**
     * Set the message.
     *
     * @param Message $message
     * @return $this
     */
    public function setMessage(Message $message)
    {
        $this->message = $message;

        return $this;
    }

    /**
     * @return bool
     */
    public function hasMessage()
    {
        return $this->getMessage() instanceof Message;
    }

    /**
     * Get the message
     *
     * @return Message
     */
    public function getMessage()
    {
        return $this->message;
    }

    public static function encapsulate(Message $message)
    {
        return Container::getInstance()->makeWith(static::class, [
            'message' => $message,
        ]);
    }
}
