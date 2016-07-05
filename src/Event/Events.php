<?php

namespace Novomirskoy\Websocket\Event;

/**
 * Class Events
 * @package Novomirskoy\Websocket\Event
 */
final class Events
{
    const SERVER_LAUNCHED = 'web_socket.server_launched';
    const CLIENT_CONNECTED = 'web_socket.client_connected';
    const CLIENT_DISCONNECTED = 'web_socket.client_disconnected';
    const CLIENT_ERROR = 'web_socket.client_error';
    const CLIENT_REJECTED = 'web_socket.client_rejected';
    const PUSHER_FAIL = 'web_socket.push_fail';
    const PUSHER_SUCCESS = 'web_socket.push_success';
}
