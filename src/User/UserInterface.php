<?php

namespace Novomirskoy\Websocket\User;

/**
 * Class UserInterface
 * @package Novomirskoy\Websocket\User
 */
interface UserInterface
{
    /**
     * @return string
     */
    public function getUsername();

    /**
     * @return string
     */
    public function getPassword();
}
