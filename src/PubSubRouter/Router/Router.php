<?php

namespace Novomirskoy\Websocket\PubSubRouter\Router;

use Novomirskoy\Websocket\PubSubRouter\Generator\GeneratorInterface;
use Novomirskoy\Websocket\PubSubRouter\Loader\RouteLoader;
use Novomirskoy\Websocket\PubSubRouter\Matcher\MatcherInterface;

/**
 * Class Router
 * @package Novomirskoy\Websocket\PubSubRouter\Router
 */
class Router implements RouterInterface
{
    /**
     * @var RouteCollection
     */
    protected $collection;

    /**
     * @var RouterContext
     */
    protected $context;

    /**
     * @var MatcherInterface
     */
    protected $matcher;

    /**
     * @var GeneratorInterface
     */
    protected $generator;

    /**
     * @var RouteLoader
     */
    protected $loader;

    /**
     * @var string
     */
    protected $name;

    /**
     * @param RouteCollection    $routeCollection
     * @param MatcherInterface   $matcher
     * @param GeneratorInterface $generator
     * @param RouteLoader        $loader
     * @param string             $name
     */
    public function __construct(
        RouteCollection $routeCollection,
        MatcherInterface $matcher,
        GeneratorInterface $generator,
        RouteLoader $loader,
        $name
    ) {
        $this->collection = $routeCollection;
        $this->matcher = $matcher;
        $this->generator = $generator;
        $this->loader = $loader;
        $this->name = $name;
    }

    /**
     * @param RouteCollection $collection
     */
    public function setCollection(RouteCollection $collection)
    {
        $this->collection = $collection;
    }

    /**
     * {@inheritdoc}
     */
    public function setContext(RouterContext $context)
    {
        $this->context = $context;
    }

    /**
     * {@inheritdoc}
     */
    public function getContext()
    {
        return $this->context;
    }

    /**
     * {@inheritdoc}
     */
    public function generate($routeName, Array $parameters = [], $tokenSeparator = null)
    {
        $this->generator->setCollection($this->collection);

        if (null === $tokenSeparator && null !== $this->context) {
            $tokenSeparator = $this->context->getTokenSeparator();
        }

        return $this->generator->generate($routeName, $parameters, $tokenSeparator);
    }

    /**
     * {@inheritdoc}
     */
    public function generateFromTokens(RouteInterface $route, Array $tokens, Array $parameters = [], $tokenSeparator = null)
    {
        if (null === $tokenSeparator && null !== $this->context) {
            $tokenSeparator = $this->context->getTokenSeparator();
        }

        return $this->generator->generateFromTokens($route, $tokens, $parameters, $tokenSeparator);
    }

    /**
     * {@inheritdoc}
     */
    public function match($channel, $tokenSeparator = null)
    {
        $this->matcher->setCollection($this->collection);

        if (null === $tokenSeparator && null !== $this->context) {
            $tokenSeparator = $this->context->getTokenSeparator();
        }

        return $this->matcher->match($channel, $tokenSeparator);
    }

    /**
     * {@inheritdoc}
     */
    public function getCollection()
    {
        return $this->collection;
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return $this->name;
    }
}
