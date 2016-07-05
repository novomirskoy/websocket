<?php

namespace Novomirskoy\Websocket\Server\App\Registry;

use Novomirskoy\Websocket\Server\Type\ServerInterface;

/**
 * Class ServerRegistry
 * @package Novomirskoy\Websocket\Server\App\Registry
 */
class ServerRegistry
{
    /**
     * @var ServerInterface[]
     */
    protected $servers;

    public function __construct()
    {
        $this->servers = [];
    }

    /**
     * @param ServerInterface $server
     */
    public function addServer(ServerInterface $server)
    {
        $this->servers[$server->getName()] = $server;
    }

    /**
     * @param $serverName
     *
     * @return ServerInterface
     */
    public function getServer($serverName)
    {
        return $this->servers[$serverName];
    }

    /**
     * @return ServerInterface[]
     */
    public function getServers()
    {
        return $this->servers;
    }
}
