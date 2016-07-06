<?php

namespace Novomirskoy\Websocket\Topic;

use Novomirskoy\Websocket\Router\WampRequest;
use Ratchet\ConnectionInterface;
use Ratchet\Wamp\Topic;

/**
 * Interface TopicInterface
 * @package Novomirskoy\Websocket\Topic
 */
interface TopicInterface
{
    /**
     * @param ConnectionInterface $connection
     * @param Topic $topic
     * @param WampRequest $request
     */
    public function onSubscribe(ConnectionInterface $connection, Topic $topic, WampRequest $request);

    /**
     * @param ConnectionInterface $connection
     * @param Topic $topic
     * @param WampRequest $request
     */
    public function onUnSubscribe(ConnectionInterface $connection, Topic $topic, WampRequest $request);

    /**
     * @param ConnectionInterface $connection
     * @param Topic $topic
     * @param WampRequest $request
     * @param string $event
     * @param array $exclude
     * @param array $eligible
     */
    public function onPublish(ConnectionInterface $connection, Topic $topic, WampRequest $request, $event, array $exclude, array $eligible);

    /**
     * @return string
     */
    public function getName();
}
