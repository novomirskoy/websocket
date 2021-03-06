<?php

namespace Novomirskoy\Websocket\Client;

use Novomirskoy\Websocket\User\UserInterface;
use Ratchet\ConnectionInterface;
use Ratchet\Wamp\Topic;

/**
 * Class ClientManipulatorInterface
 * @package Novomirskoy\Websocket\Client
 */
interface ClientManipulatorInterface
{
    /**
     * @param ConnectionInterface $connection
     *
     * @return false|string|UserInterface
     */
    public function getClient(ConnectionInterface $connection);

    /**
     * @param Topic  $topic
     * @param string $username
     *
     * @return array|false
     */
    public function findByUsername(Topic $topic, $username);

    /**
     * @param Topic $topic
     * @param array $roles
     *
     * @return array|false
     */
    public function findByRoles(Topic $topic, array $roles);

    /**
     * @param Topic $topic
     * @param bool  $anonymous
     *
     * @return array|false
     */
    public function getAll(Topic $topic, $anonymous = false);
}
