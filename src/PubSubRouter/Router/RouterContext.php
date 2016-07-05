<?php

namespace Novomirskoy\Websocket\PubSubRouter\Router;

/**
 * Class RouterContext
 * @package Novomirskoy\Websocket\PubSubRouter\Router
 */
class RouterContext
{
    /**
     * @var string
     */
    protected $tokenSeparator;

    /**
     * @return string
     */
    public function getTokenSeparator()
    {
        return $this->tokenSeparator;
    }

    /**
     * @param string $tokenSeparator
     *
     * @return $this|RouterContext
     */
    public function setTokenSeparator($tokenSeparator)
    {
        $this->tokenSeparator = $tokenSeparator;

        return $this;
    }
}
