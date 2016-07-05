<?php

namespace Novomirskoy\Websocket\PubSubRouter\Tokenizer;

use Novomirskoy\Websocket\PubSubRouter\Exception\InvalidArgumentException;
use Novomirskoy\Websocket\PubSubRouter\Router\RouteInterface;

interface TokenizerInterface
{
    /**
     * @param string|RouteInterface $stringOrRoute
     * @param string       $separator
     *
     * @return Token[]|false
     *
     * @throws InvalidArgumentException
     */
    public function tokenize($stringOrRoute, $separator);
}
