<?php

namespace Novomirskoy\Websocket\Event;

use Exception;

/**
 * Class ClientErrorEvent
 * @package Novomirskoy\Websocket\Event
 */
class ClientErrorEvent extends ClientEvent
{
    /**
     * @var Exception
     */
    protected $exception;

    /**
     * @param Exception $exception
     */
    public function setException(Exception $exception)
    {
        $this->exception = $exception;
    }

    /**
     * @return Exception
     */
    public function getException()
    {
        return $this->exception;
    }
}
