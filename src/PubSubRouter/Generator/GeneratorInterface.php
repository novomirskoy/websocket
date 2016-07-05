<?php

namespace Novomirskoy\Websocket\PubSubRouter\Generator;

use Novomirskoy\Websocket\PubSubRouter\Exception\InvalidArgumentException;
use Novomirskoy\Websocket\PubSubRouter\Router\RouteCollection;
use Novomirskoy\Websocket\PubSubRouter\Router\RouteInterface;
use Novomirskoy\Websocket\PubSubRouter\Tokenizer\Token;

/**
 * Class GeneratorInterface
 * @package Novomirskoy\Websocket\PubSubRouter\Generator
 */
interface GeneratorInterface
{
    /**
     * @param string          $routeName
     * @param array           $parameters
     * @param RouteCollection $routeCollection
     * @param null|string     $tokenSeparator
     *
     * @return mixed
     */
    public function generate($routeName, Array $parameters = [], $tokenSeparator);

    /**
     * @param Token[]     $tokens
     * @param array       $parameters
     * @param string|null $tokenSeparator
     *
     * @throws InvalidArgumentException
     *
     * @return string
     */
    public function generateFromTokens(RouteInterface $route, Array $tokens, Array $parameters = [], $tokenSeparator);

    /**
     * @param RouteCollection $collection
     */
    public function setCollection(RouteCollection $collection);
}
