<?php

namespace Novomirskoy\Websocket\Server\App\Dispatcher;

use Novomirskoy\Websocket\Router\WampRequest;
use Novomirskoy\Websocket\Topic\TopicInterface;
use Ratchet\ConnectionInterface;

/**
 * Class RpcDispatcherInterface
 * @package Novomirskoy\Websocket\Server\App\Dispatcher
 */
interface RpcDispatcherInterface
{
    /**
     * @param ConnectionInterface $conn
     * @param string              $id
     * @param TopicInterface      $topic
     * @param WampRequest         $request
     * @param array               $params
     */
    public function dispatch(ConnectionInterface $conn, $id, $topic, WampRequest $request, array $params);
}
