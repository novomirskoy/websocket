<?php

namespace Novomirskoy\Websocket\Pusher;

use Ratchet\Wamp\WampServerInterface;
use React\EventLoop\LoopInterface;

/**
 * Interface ServerPushHandlerInterface
 * @package Novomirskoy\Websocket\Pusher
 */
interface ServerPushHandlerInterface
{
    /**
     * @param LoopInterface       $loop
     * @param WampServerInterface $app
     */
    public function handle(LoopInterface $loop, WampServerInterface $app);

    /**
     * @param string $name
     */
    public function setName($name);

    /**
     * @param array $config
     */
    public function setConfig(array $config);

    /**
     * @return array
     */
    public function getConfig();

    /**
     * @return string
     */
    public function getName();

    public function close();
}
