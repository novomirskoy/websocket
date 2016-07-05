<?php

namespace Novomirskoy\Websocket\Periodic;

/**
 * Interface PeriodicInterface
 * @package Novomirskoy\Websocket\Periodic
 */
interface PeriodicInterface
{
    /**
     * Function excecuted n timeout.
     */
    public function tick();

    /**
     * @return int (in second)
     */
    public function getTimeout();
}
