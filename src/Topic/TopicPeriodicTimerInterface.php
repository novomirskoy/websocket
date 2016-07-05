<?php

namespace Novomirskoy\Websocket\Topic;

use Ratchet\Wamp\Topic;

/**
 * Interface TopicPeriodicTimerInterface
 * @package Novomirskoy\Websocket\Topic
 */
interface TopicPeriodicTimerInterface
{
    /**
     * @param Topic $topic
     *
     * @return mixed
     */
    public function registerPeriodicTimer(Topic $topic);

    /**
     * @param TopicPeriodicTimer $periodicTimer
     */
    public function setPeriodicTimer(TopicPeriodicTimer $periodicTimer);
}
