<?php

namespace Novomirskoy\Websocket\Pusher;

/**
 * Interface PusherInterface
 * @package Novomirskoy\Websocket\Pusher
 */
interface PusherInterface
{
    /**
     * @param string|array $data
     * @param string       $routeName
     * @param array[]      $routeParameters
     */
    public function push($data, $routeName, array $routeParameters = array(), array $context = []);

    /**
     * @return array
     */
    public function getConfig();

    /**
     * @param array $config
     */
    public function setConfig($config);

    public function close();

    /**
     * @return string
     */
    public function getName();
}
