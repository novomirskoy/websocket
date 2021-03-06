<?php

namespace Novomirskoy\Websocket\PubSubRouter\Request;

use Novomirskoy\Websocket\PubSubRouter\Router\RouteInterface;
use Symfony\Component\HttpFoundation\ParameterBag;

/**
 * Class PubSubRequest
 * @package Novomirskoy\Websocket\PubSubRouter\Request
 */
class PubSubRequest
{
    /** @var  string */
    protected $routeName;

    /** @var  RouteInterface */
    protected $route;

    /** @var  ParameterBag */
    protected $attributes;

    /**
     * @param string         $routeName
     * @param RouteInterface $route
     * @param array          $attributes
     */
    public function __construct($routeName, $route, $attributes)
    {
        $this->attributes = new ParameterBag($attributes);
        $this->route = $route;
        $this->routeName = $routeName;
    }

    /**
     * @return string
     */
    public function getRouteName()
    {
        return $this->routeName;
    }

    /**
     * @return RouteInterface
     */
    public function getRoute()
    {
        return $this->route;
    }

    /**
     * @return ParameterBag
     */
    public function getAttributes()
    {
        return $this->attributes;
    }
}
