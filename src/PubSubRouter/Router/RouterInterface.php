<?php

namespace Novomirskoy\Websocket\PubSubRouter\Router;

/**
 * Class RouterInterface
 * @package Novomirskoy\Websocket\PubSubRouter\Router
 */
interface RouterInterface extends MathcherInterface, GeneratorInterface
{
    /**
     * @param RouterContext $context
     */
    public function setContext(RouterContext $context);

    /**
     * @return RouterContext
     */
    public function getContext();

    /**
     * @return RouteCollection
     */
    public function getCollection();

    /**
     * @return string
     */
    public function getName();

    /**
     * @param string $routeName
     * @param array  $parameters
     * @param null   $tokenSeparator
     *
     * @return mixed
     */
    public function generate($routeName, Array $parameters = [], $tokenSeparator = null);

    /**
     * @param string $channel
     * @param null   $tokenSeparator
     *
     * @return mixed
     */
    public function match($channel, $tokenSeparator = null);
}
