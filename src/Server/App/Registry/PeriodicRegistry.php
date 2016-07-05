<?php

namespace Novomirskoy\Websocket\Server\App\Registry;

use Novomirskoy\Websocket\Periodic\PeriodicInterface;

/**
 * Class PeriodicRegistry
 * @package Novomirskoy\Websocket\Server\App\Registry
 */
class PeriodicRegistry
{
    /**
     * @var PeriodicInterface[]
     */
    protected $periodics;

    public function __construct()
    {
        $this->periodics = [];
    }

    /**
     * @param PeriodicInterface $periodic
     */
    public function addPeriodic(PeriodicInterface $periodic)
    {
        $this->periodics[] = $periodic;
    }

    /**
     * @return PeriodicInterface[]
     */
    public function getPeriodics()
    {
        return $this->periodics;
    }
}
