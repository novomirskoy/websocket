<?php

namespace Novomirskoy\Websocket\Client\Auth;

use Ratchet\ConnectionInterface;


/**
 * Interface WebsocketAuthenticationProviderInterface
 * @package Novomirskoy\Websocket\Client\Auth
 */
interface WebsocketAuthenticationProviderInterface
{
    /**
     * @param ConnectionInterface $conn
     *
     * @return \Symfony\Component\Security\Core\Authentication\Token\TokenInterface
     */
    public function authenticate(ConnectionInterface $conn);
}
