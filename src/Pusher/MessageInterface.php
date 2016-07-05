<?php

namespace Novomirskoy\Websocket\Pusher;

use JsonSerializable;

/**
 * Interface MessageInterface
 * @package Novomirskoy\Websocket\Pusher
 */
interface MessageInterface extends JsonSerializable
{
    /**
     * @return string
     */
    public function getTopic();

    /**
     * @return array
     */
    public function getData();
}
