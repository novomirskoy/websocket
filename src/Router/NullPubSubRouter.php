<?php

namespace Novomirskoy\Websocket\Router;

use Novomirskoy\Websocket\PubSubRouter\Router\RouteCollection;
use Novomirskoy\Websocket\PubSubRouter\Router\RouteInterface;
use Novomirskoy\Websocket\PubSubRouter\Router\RouterContext;
use Novomirskoy\Websocket\PubSubRouter\Router\RouterInterface;

/**
 * Class NullPubSubRouter
 * @package Novomirskoy\Websocket\Router
 */
class NullPubSubRouter implements RouterInterface
{
    /**
     * {@inheritdoc}
     */
    public function generate($routeName, Array $parameters = [], $tokenSeparator = null)
    {
        throw new \Exception('Websocket router is not configured, see doc');
    }

    /**
     * {@inheritdoc}
     */
    public function generateFromTokens(RouteInterface $route, Array $tokens, Array $parameters = [], $tokenSeparator)
    {
        throw new \Exception('Websocket router is not configured, see doc');
    }

    /**
     * {@inheritdoc}
     */
    public function match($channel, $tokenSeparator = null)
    {
        throw new \Exception('Websocket router is not configured, see doc');
    }

    /**
     * {@inheritdoc}
     */
    public function setCollection(RouteCollection $collection)
    {
        throw new \Exception('Websocket router is not configured, see doc');
    }

    /**
     * {@inheritdoc}
     */
    public function setContext(RouterContext $context)
    {
        throw new \Exception('Websocket router is not configured, see doc');
    }

    /**
     * @return RouterContext
     */
    public function getContext()
    {
        // TODO: Implement getContext() method.
    }

    /**
     * @return RouteCollection
     */
    public function getCollection()
    {
        // TODO: Implement getCollection() method.
    }

    /**
     * @return string
     */
    public function getName()
    {
        // TODO: Implement getName() method.
    }
}
