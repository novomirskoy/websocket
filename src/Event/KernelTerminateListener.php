<?php

namespace Novomirskoy\Websocket\Event;

use Novomirskoy\Websocket\Pusher\PusherRegistry;
use Symfony\Component\HttpKernel\Event\PostResponseEvent;

/**
 * Class KernelTerminateListener
 * @package Novomirskoy\Websocket\Event
 */
class KernelTerminateListener
{
    /**
     * @var PusherRegistry
     */
    protected $pusherRegistry;

    /**
     * KernelTerminateListener constructor.
     * 
     * @param PusherRegistry $pusherRegistry
     */
    public function __construct(PusherRegistry $pusherRegistry)
    {
        $this->pusherRegistry = $pusherRegistry;
    }

    /**
     * @param PostResponseEvent $event
     */
    public function closeConnection(PostResponseEvent $event)
    {
        foreach ($this->pusherRegistry->getPushers() as $pusher) {
            $pusher->close();
        }
    }
}
