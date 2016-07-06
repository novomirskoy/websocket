<?php

namespace Novomirskoy\Websocket\Topic;

use ArrayIterator;
use Countable;
use IteratorAggregate;
use Ratchet\ConnectionInterface;
use Ratchet\Wamp\WampConnection;
use React\EventLoop\LoopInterface;

/**
 * Class ConnectionPeriodicTimer
 * @package Novomirskoy\Websocket\Topic
 */
class ConnectionPeriodicTimer implements IteratorAggregate, Countable
{
    /**
     * @var array
     */
    protected $registry;

    /**
     * @var LoopInterface
     */
    protected $loop;

    /**
     * @var ConnectionInterface|WampConnection
     */
    protected $connection;

    /**
     * ConnectionPeriodicTimer constructor.
     *
     * @param ConnectionInterface $connection
     * @param LoopInterface $loop
     */
    public function __construct(ConnectionInterface $connection, LoopInterface $loop)
    {
        $this->loop = $loop;
        $this->registry = [];
        $this->connection = $connection;
    }

    /**
     * @param $name
     *
     * @return bool
     */
    public function getPeriodicTimer($name)
    {
        $tid = $this->getTid($name);

        if (!$this->isPeriodicTimerActive($name)) {
            return false;
        }

        return $this->registry[$tid];
    }

    /**
     * @param string $name
     *
     * @return string
     */
    protected function getTid($name)
    {
        return sha1($this->connection->resourceId . $this->connection->WAMP->sessionId . $name);
    }

    /**
     * @param string    $name
     * @param int|float $timeout
     * @param mixed     $callback
     */
    public function addPeriodicTimer($name, $timeout, $callback)
    {
        $this->registry[$this->getTid($name)] = $this->loop->addPeriodicTimer($timeout, $callback);
    }

    /**
     * @param string $name
     *
     * @return bool
     */
    public function isPeriodicTimerActive($name)
    {
        $tid = $this->getTid($name);

        if (!isset($this->registry[$tid])) {
            return false;
        }

        return $this->loop->isTimerActive($this->registry[$tid]);
    }

    /**
     * @param string $tidOrName
     */
    public function cancelPeriodicTimer($tidOrName)
    {
        if (!isset($this->registry[$tidOrName])) {
            $tid = $this->getTid($tidOrName);
        } else {
            $tid = $tidOrName;
        }

        $timer = $this->registry[$tid];
        $this->loop->cancelTimer($timer);
        unset($this->registry[$tid]);
    }

    /**
     * @return ArrayIterator
     */
    public function getIterator()
    {
        return new ArrayIterator($this->registry);
    }

    /**
     * return int.
     */
    public function count()
    {
        return count($this->registry);
    }
}
