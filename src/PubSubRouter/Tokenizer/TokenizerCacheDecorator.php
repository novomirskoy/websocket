<?php

namespace Novomirskoy\Websocket\PubSubRouter\Tokenizer;

use Doctrine\Common\Cache\Cache;
use Novomirskoy\Websocket\PubSubRouter\Exception\InvalidArgumentException;
use Novomirskoy\Websocket\PubSubRouter\Router\RouteInterface;

/**
 * Class TokenizerCacheDecorator
 * @package Novomirskoy\Websocket\PubSubRouter\Tokenizer
 */
class TokenizerCacheDecorator implements TokenizerInterface
{
    /**
     * @var Cache
     */
    protected $cache;

    /**
     * @var TokenizerInterface
     */
    protected $tokenizer;

    /**
     * @param TokenizerInterface $tokenizer
     * @param Cache              $cache
     */
    public function __construct(TokenizerInterface $tokenizer, Cache $cache)
    {
        $this->cache = $cache;
        $this->tokenizer = $tokenizer;
    }

    /**
     * @param string|RouteInterface $stringOrRoute
     * @param string $separator
     *
     * @return Token[]|false
     *
     * @throws InvalidArgumentException
     */
    public function tokenize($stringOrRoute, $separator)
    {
        if ($stringOrRoute instanceof RouteInterface) {
            $routeName = (string) $stringOrRoute;
            if ($tokens = $this->cache->fetch('tokens_' . $routeName)) {
                return $tokens;
            } else {
                $tokens = $this->tokenizer->tokenize($stringOrRoute, $separator);
                $this->cache->save('tokens_' . $routeName, $tokens);

                return $tokens;
            }
        }

        return $this->tokenizer->tokenize($stringOrRoute, $separator);
    }
}
