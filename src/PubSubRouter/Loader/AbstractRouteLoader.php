<?php

namespace Novomirskoy\Websocket\PubSubRouter\Loader;

use Symfony\Component\Config\Loader\FileLoader;

/**
 * Class AbstractRouteLoader
 * @package Novomirskoy\Websocket\PubSubRouter\Loader
 */
abstract class AbstractRouteLoader extends FileLoader
{
    /**
     * @param mixed $resource
     * @param null  $type
     *
     * @return array|string
     */
    public function load($resource, $type = null)
    {
        $path = $this->locator->locate($resource);

        if (!stream_is_local($path)) {
            throw new \InvalidArgumentException(sprintf('This is not a local file "%s".', $path));
        }

        if (!file_exists($path)) {
            throw new \InvalidArgumentException(sprintf('File "%s" not found.', $path));
        }

        return $path;
    }
}
