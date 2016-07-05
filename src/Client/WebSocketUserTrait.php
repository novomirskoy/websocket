<?php

namespace Novomirskoy\Websocket\Client;

use Novomirskoy\Websocket\User\UserInterface;
use Ratchet\ConnectionInterface;

/**
 * Class WebSocketUserTrait
 * @package Novomirskoy\Websocket\Client
 */
trait WebSocketUserTrait
{
    /**
     * @var ClientStorageInterface
     */
    protected $clientStorage;

    /**
     * @param ConnectionInterface $connection
     *
     * @return false|string|UserInterface
     */
    public function getCurrentUser(ConnectionInterface $connection)
    {
        @trigger_error('User ClientManipulator service instead, will be remove in 2.0', E_USER_DEPRECATED);

        return $this->clientStorage->getClient($this->clientStorage->getStorageId($connection));
    }
}
