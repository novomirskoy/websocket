<?php

namespace Novomirskoy\Websocket\PubSubRouter\Matcher;

use Novomirskoy\Websocket\PubSubRouter\Exception\ResourceNotFoundException;
use Novomirskoy\Websocket\PubSubRouter\Router\RouteCollection;

/**
 * Interface MatcherInterface
 * @package Novomirskoy\Websocket\PubSubRouter\Matcher
 */
interface MatcherInterface
{
    /**
     * @param string $channel
     * @param string $tokenSeparator
     *
     * @return bool
     *
     * @throws ResourceNotFoundException
     */
    public function match($channel, $tokenSeparator = null);

    /**
     * @param RouteCollection $collection
     */
    public function setCollection(RouteCollection $collection);
}
