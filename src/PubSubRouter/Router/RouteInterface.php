<?php

namespace Novomirskoy\Websocket\PubSubRouter\Router;

/**
 * Interface RouteInterface
 * @package Novomirskoy\Websocket\PubSubRouter\Router
 */
interface RouteInterface
{
    /**
     * @return string
     */
    public function getPattern();

    /**
     * @return Callable|string
     */
    public function getCallback();

    /**
     * @return array
     */
    public function getRequirements();

    /**
     * @return array
     */
    public function getArgs();

    /**
     * @param string $name
     */
    public function setName($name);
}
