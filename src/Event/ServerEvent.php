<?php

namespace Novomirskoy\Websocket\Event;

use React\EventLoop\LoopInterface;
use React\Socket\Server;
use Symfony\Component\EventDispatcher\Event;

/**
 * Class ServerEvent
 * @package Novomirskoy\Websocket\Event
 */
class ServerEvent extends Event
{
    /**
     * @var LoopInterface
     */
    protected $loop;

    /**
     * @var Server
     */
    protected $server;

    /**
     * @param LoopInterface $loop
     * @param Server $server
     */
    public function __construct(LoopInterface $loop, Server $server)
    {
        $this->loop = $loop;
        $this->server = $server;
    }

    /**
     * Get Server Event Loop to add other services in the same loop.
     *
     * @return LoopInterface
     */
    public function getEventLoop()
    {
        return $this->loop;
    }

    /**
     * @return Server
     */
    public function getServer()
    {
        return $this->server;
    }
}
