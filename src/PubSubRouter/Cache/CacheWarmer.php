<?php

namespace Novomirskoy\Websocket\PubSubRouter\Cache;

use Doctrine\Common\Cache\Cache;
use Novomirskoy\Websocket\PubSubRouter\Loader\RouteLoader;
use Symfony\Component\HttpKernel\CacheWarmer\CacheWarmerInterface;

/**
 * Class CacheWarmer
 * @package Novomirskoy\Websocket\PubSubRouter\Cache
 */
class CacheWarmer implements CacheWarmerInterface
{
    /** @var  ContainerInterface */
    protected $container;

    /**
     * @var Cache
     */
    protected $cache;

    /**
     * @param Cache              $cache
     * @param ContainerInterface $container
     */
    public function __construct(Cache $cache, ContainerInterface $container)
    {
        $this->cache = $cache;
        $this->container = $container;
    }

    /**
     * {@inheritdoc}
     */
    public function isOptional()
    {
        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function warmUp($cacheDir)
    {
        $registeredRouter = $this->container->getParameter('gos_pubsub_registered_routers');

        foreach ($registeredRouter as $routerType) {

            /** @var RouteCollection $collection */
            $collection = $this->container->get('gos_pubsub_router.collection.' . $routerType);

            /** @var RouteLoader $loader */
            $loader = $this->container->get('gos_pubsub_router.loader.' . $routerType);

            $loader->load($collection); //trigger cache on route collection
        }
    }
}
