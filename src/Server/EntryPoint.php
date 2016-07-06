<?php

namespace Novomirskoy\Websocket\Server;

use Novomirskoy\Websocket\Server\App\Registry\ServerRegistry;
use Novomirskoy\Websocket\Server\Exception\RuntimeException;
use Novomirskoy\Websocket\Server\Type\ServerInterface;

/**
 * Class EntryPoint
 * @package Novomirskoy\Websocket\Server
 */
class EntryPoint
{
    /**
     * @var ServerInterface[]
     */
    protected $serverRegistry;

    /**
     * EntryPoint constructor.
     * 
     * @param ServerRegistry $serverRegistry
     */
    public function __construct(ServerRegistry $serverRegistry)
    {
        $this->serverRegistry = $serverRegistry;
    }

    /**
     * Launch server
     * 
     * @param string|null $serverName
     * @param string $host
     * @param string $port
     * @param bool $profile
     */
    public function launch($serverName, $host, $port, $profile)
    {
        $servers = $this->serverRegistry->getServers();

        if (null === $serverName) {
            reset($servers);
            $server = current($servers);
        } else {
            if (!array_key_exists($serverName, $servers)) {
                throw new RuntimeException(sprintf(
                    'Unknown server %s in [%s]',
                    $serverName,
                    implode(', ', array_keys($servers))
                ));
            }

            $server = $servers[$serverName];
        }

        $server->launch($host, $port, $profile);
    }
}
