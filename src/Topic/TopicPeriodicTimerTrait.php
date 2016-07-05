<?php

namespace Novomirskoy\Websocket\Topic;

/**
 * Class TopicPeriodicTimerTrait
 * @package Novomirskoy\Websocket\Topic
 */
trait TopicPeriodicTimerTrait
{
    /**
     * @var TopicPeriodicTimer
     */
    protected $periodicTimer;

    /**
     * @param TopicPeriodicTimer $periodicTimer
     */
    public function setPeriodicTimer(TopicPeriodicTimer $periodicTimer)
    {
        $this->periodicTimer = $periodicTimer;
    }
}
