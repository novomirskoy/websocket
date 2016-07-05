<?php

namespace Novomirskoy\Websocket\Client;

use Novomirskoy\Websocket\Client\Exception\ClientNotFoundException;
use Novomirskoy\Websocket\Client\Exception\StorageException;
use Novomirskoy\Websocket\User\UserInterface;
use Psr\Log\LoggerInterface;
use Psr\Log\NullLogger;
use Ratchet\ConnectionInterface;

/**
 * Class ClientStorage
 * @package Novomirskoy\Websocket\Client
 */
class ClientStorage implements ClientStorageInterface
{
    /**
     * @var DriverInterface
     */
    protected $driver;

    /**
     * @var LoggerInterface
     */
    protected $logger;

    /**
     * @var int
     */
    protected $ttl;

    /**
     * @param int             $ttl
     * @param LoggerInterface $logger
     */
    public function __construct($ttl, LoggerInterface $logger = null)
    {
        $this->ttl = $ttl;
        $this->logger = null === $logger ? new NullLogger() : $logger;
    }

    /**
     * {@inheritdoc}
     */
    public function setStorageDriver(DriverInterface $driver)
    {
        $this->driver = $driver;
    }

    /**
     * {@inheritdoc}
     */
    public function getClient($identifier)
    {
        try {
            $result = $this->driver->fetch($identifier);
        } catch (\Exception $e) {
            throw new StorageException(sprintf('Driver %s failed', get_class($this)), $e->getCode(), $e);
        }

        $this->logger->debug('GET CLIENT ' . $identifier);

        if (false === $result) {
            throw new ClientNotFoundException(sprintf('Client %s not found', $identifier));
        }

        return unserialize($result);
    }

    /**
     * {@inheritdoc}
     */
    public static function getStorageId(ConnectionInterface $conn)
    {
        return $conn->resourceId;
    }

    /**
     * {@inheritdoc}
     */
    public function addClient($identifier, $user)
    {
        $serializedUser = serialize($user);

        $context = [
            'user' => $serializedUser,
        ];

        if ($user instanceof UserInterface) {
            $context['username'] = $user->getUsername();
        }

        $this->logger->debug(sprintf('INSERT CLIENT ' . $identifier), $context);

        try {
            $result = $this->driver->save($identifier, $serializedUser, $this->ttl);
        } catch (\Exception $e) {
            throw new StorageException(sprintf('Driver %s failed', get_class($this)), $e->getCode(), $e);
        }

        if (false === $result) {
            throw new StorageException('Unable add client');
        }
    }

    /**
     * {@inheritdoc}
     */
    public function hasClient($identifier)
    {
        try {
            $result = $this->driver->contains($identifier);
        } catch (\Exception $e) {
            throw new StorageException(sprintf('Driver %s failed', get_class($this)), $e->getCode(), $e);
        }

        return $result;
    }

    /**
     * {@inheritdoc}
     */
    public function removeClient($identifier)
    {
        $this->logger->debug('REMOVE CLIENT ' . $identifier);

        try {
            $result = $this->driver->delete($identifier);
        } catch (\Exception $e) {
            throw new StorageException(sprintf('Driver %s failed', get_class($this)), $e->getCode(), $e);
        }

        return $result;
    }
}
