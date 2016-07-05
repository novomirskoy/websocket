<?php

namespace Novomirskoy\Websocket\Client;

use Novomirskoy\Websocket\Client\Exception\StorageException;
use Novomirskoy\Websocket\User\UserInterface;
use Ratchet\ConnectionInterface;

/**
 * Class ClientStorageInterface
 * @package Novomirskoy\Websocket\Client
 */
interface ClientStorageInterface
{
    /**
     * @param DriverInterface $driver
     */
    public function setStorageDriver(DriverInterface $driver);

    /**
     * @param string $identifier
     *
     * @throws StorageException
     *
     * @return string|UserInterface|false
     */
    public function getClient($identifier);

    /**
     * @param ConnectionInterface $conn
     *
     * @return string
     */
    public static function getStorageId(ConnectionInterface $conn);

    /**
     * @param string               $identifier
     * @param string|UserInterface $user
     *
     * @throws StorageException
     */
    public function addClient($identifier, $user);

    /**
     * @param string $identifier
     *
     * @return bool
     */
    public function hasClient($identifier);

    /**
     * @param string $identifier
     *
     * @return bool
     */
    public function removeClient($identifier);
}
