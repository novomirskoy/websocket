<?php

namespace Novomirskoy\Websocket\Router;

use Novomirskoy\Websocket\PubSubRouter\Exception\ResourceNotFoundException;
use Novomirskoy\Websocket\PubSubRouter\Router\RouteCollection;
use Novomirskoy\Websocket\PubSubRouter\Router\Router;
use Novomirskoy\Websocket\PubSubRouter\Router\RouterContext;
use Novomirskoy\Websocket\PubSubRouter\Router\RouterInterface;
use Psr\Log\LoggerInterface;
use Psr\Log\NullLogger;
use Ratchet\Wamp\Topic;
use Symfony\Component\HttpFoundation\ParameterBag;

/**
 * Class WampRouter
 * @package Novomirskoy\Websocket\Router
 */
class WampRouter
{
    /**
     * @var Router
     */
    protected $pubSubRouter;

    /**
     * @var LoggerInterface
     */
    protected $logger;

    /**
     * @var bool
     */
    protected $debug;

    /**
     * WampRouter constructor.
     *
     * @param RouterInterface|null $router
     * @param $debug
     * @param LoggerInterface|null $logger
     */
    public function __construct(RouterInterface $router = null, $debug, LoggerInterface $logger = null)
    {
        $this->pubSubRouter = $router;
        $this->logger = null === $logger ? new NullLogger() : $logger;
        $this->debug = $debug;
    }

    /**
     * @param RouterContext $context
     */
    public function setContext(RouterContext $context)
    {
        $this->pubSubRouter->setContext($context);
    }

    /**
     * @return RouterContext
     */
    public function getContext()
    {
        return $this->pubSubRouter->getContext();
    }

    /**
     * @param Topic       $topic
     * @param string|null $tokenSeparator
     *
     * @return WampRequest
     *
     * @throws ResourceNotFoundException
     * @throws \Exception
     */
    public function match(Topic $topic, $tokenSeparator = null)
    {
        try {
            list($routeName, $route, $attributes) = $this->pubSubRouter->match($topic->getId(), $tokenSeparator);

            if ($this->debug) {
                $this->logger->debug(sprintf(
                    'Matched route "%s"',
                    $routeName
                ), $attributes);
            }

            return new WampRequest($routeName, $route, new ParameterBag($attributes), $topic->getId());
        } catch (ResourceNotFoundException $e) {
            $this->logger->error(sprintf(
                'Unable to find route for %s',
                $topic->getId()
            ));

            throw $e;
        }
    }

    /**
     * @param string      $routeName
     * @param array       $parameters
     * @param null|string $tokenSeparator
     *
     * @return string
     */
    public function generate($routeName, Array $parameters = [], $tokenSeparator = null)
    {
        return $this->pubSubRouter->generate($routeName, $parameters, $tokenSeparator);
    }

    /**
     * @return RouteCollection
     */
    public function getCollection()
    {
        return $this->pubSubRouter->getCollection();
    }
}
