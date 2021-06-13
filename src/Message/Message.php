<?php

namespace App\Message;

class Message
{
    /**
     * @var string
     */
    private $text;

    /**
     * @var string
     */
    private $parseMode = 'markdown';

    /**
     * Construct.
     *
     * @param string $text
     */
    public function __construct($text)
    {
        $this->text = $text;
    }

    /**
     * Get the message.
     *
     * @return string
     */
    public function getText()
    {
        return $this->text;
    }

    /**
     * Get the parse mode.
     *
     * @return string
     */
    public function getParseMode()
    {
        return $this->parseMode;
    }
}
