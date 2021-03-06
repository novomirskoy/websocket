<?php

namespace Novomirskoy\Websocket\Server\Type;

/**
 * Interface ServerInterface
 * @package Novomirskoy\Websocket\Server\Type
 */
interface ServerInterface
{

    /**
     * Launches the server loop.
     * 
     * @param string $host
     * @param string $port
     * @param bool $profile
     */
    public function launch($host, $port, $profile);

    /**
     * Returns a string of the name of the server/service for debugging / display purposes.
     *
     * @return string
     */
    public function getName();
}
