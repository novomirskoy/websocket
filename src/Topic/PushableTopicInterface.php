<?php

namespace Novomirskoy\Websocket\Topic;

use Ratchet\Wamp\Topic;

/**
 * Interface PushableTopicInterface
 * @package Novomirskoy\Websocket\Topic
 */
interface PushableTopicInterface
{
    /**
     * @param Topic $topic
     * @param WampRequest $request
     * @param string|array $data
     * @param string $provider
     */
    public function onPush(Topic $topic, WampRequest $request, $data, $provider);
}
