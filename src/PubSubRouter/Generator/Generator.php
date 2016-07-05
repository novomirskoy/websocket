<?php

namespace Novomirskoy\Websocket\PubSubRouter\Generator;

use Novomirskoy\Websocket\PubSubRouter\Exception\InvalidArgumentException;
use Novomirskoy\Websocket\PubSubRouter\Exception\ResourceNotFoundException;
use Novomirskoy\Websocket\PubSubRouter\Router\RouteCollection;
use Novomirskoy\Websocket\PubSubRouter\Router\RouteInterface;
use Novomirskoy\Websocket\PubSubRouter\Tokenizer\TokenizerInterface;

/**
 * Class Generator
 * @package Novomirskoy\Websocket\PubSubRouter\Generator
 */
class Generator implements GeneratorInterface
{
    /**
     * @var TokenizerInterface
     */
    protected $tokenizer;

    /**
     * @var RouteCollection
     */
    protected $collection;

    /**
     * @param TokenizerInterface $tokenizer
     */
    public function __construct(TokenizerInterface $tokenizer)
    {
        $this->tokenizer = $tokenizer;
    }

    /**
     * {@inheritdoc}
     */
    public function setCollection(RouteCollection $collection)
    {
        $this->collection = $collection;
    }

    /**
     * {@inheritdoc}
     */
    public function generate($routeName, Array $parameters = [], $tokenSeparator)
    {
        $route = $this->collection->get($routeName);

        if (false === $route) {
            throw new ResourceNotFoundException(sprintf(
                'Route %s not exists in [%s]',
                $routeName,
                implode(', ', array_keys($this->collection->all()))
            ));
        }

        $tokens = $this->tokenizer->tokenize($route, $tokenSeparator);

        return $this->generateFromTokens($route, $tokens, $parameters, $tokenSeparator);
    }

    /**
     * {@inheritdoc}
     */
    public function generateFromTokens(RouteInterface $route, Array $tokens, Array $parameters = [], $tokenSeparator)
    {
        $graph = [];

        if (empty($tokens)) {
            return $route->getPattern();
        }

        /** @var Token $token */
        foreach ($tokens as $token) {
            if ($token->isParameter()) {
                if (!isset($parameters[$token->getExpression()])) {
                    throw new InvalidArgumentException(sprintf('Missing parameter %s', $token->getExpression()));
                }

                $value = $parameters[$token->getExpression()];
                $requirements = $token->getRequirements();

                if (isset($requirements['wildcard']) && true == $requirements['wildcard']) {
                    if ($value === '*' || $value === 'all') {
                        $graph[] = $value;

                        continue; //next token
                    }
                }

                if (isset($requirements['pattern'])) {
                    $pattern = $requirements['pattern'];

                    if (1 === preg_match("#^$pattern#i", $value)) {
                        $graph[] = $value;
                        continue; //next token
                    } else {
                        throw new InvalidArgumentException(sprintf(
                            'Invalid parameters %s, must match %s',
                            $token->getExpression(),
                            $pattern
                        ));
                    }
                } else {
                    $graph[] = $value;
                }
            } else {
                $graph[] = $token->getExpression();
            }
        }

        return implode($tokenSeparator, $graph);
    }
}
