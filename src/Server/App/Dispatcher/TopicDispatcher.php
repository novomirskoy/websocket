<?php

namespace Novomirskoy\Websocket\Server\App\Dispatcher;

use Novomirskoy\Websocket\Router\WampRequest;
use Novomirskoy\Websocket\Router\WampRouter;
use Novomirskoy\Websocket\Server\App\Registry\TopicRegistry;
use Novomirskoy\Websocket\Topic\PushableTopicInterface;
use Novomirskoy\Websocket\Topic\TopicPeriodicTimer;
use Novomirskoy\Websocket\Topic\TopicPeriodicTimerInterface;
use Psr\Log\LoggerInterface;
use Psr\Log\NullLogger;
use Ratchet\ConnectionInterface;
use Ratchet\Wamp\Topic;
use Ratchet\Wamp\TopicManager;

/**
 * Class TopicDispatcher
 * @package Novomirskoy\Websocket\Server\App\Dispatcher
 */
class TopicDispatcher implements TopicDispatcherInterface
{
    /**
     * @var TopicRegistry
     */
    protected $topicRegistry;

    /**
     * @var WampRouter
     */
    protected $router;

    /**
     * @var TopicPeriodicTimer
     */
    protected $topicPeriodicTimer;

    /**
     * @var LoggerInterface|null
     */
    protected $logger;

    /**
     * @var TopicManager
     */
    protected $topicManager;

    const SUBSCRIPTION = 'onSubscribe';

    const UNSUBSCRIPTION = 'onUnSubscribe';

    const PUBLISH = 'onPublish';

    const PUSH = 'onPush';

    /**
     * TopicDispatcher constructor.
     * 
     * @param TopicRegistry $topicRegistry
     * @param WampRouter $router
     * @param TopicPeriodicTimer $topicPeriodicTimer
     * @param TopicManager $topicManager
     * @param LoggerInterface $logger
     */
    public function __construct(
        TopicRegistry $topicRegistry,
        WampRouter $router,
        TopicPeriodicTimer $topicPeriodicTimer,
        TopicManager $topicManager,
        LoggerInterface $logger = null
    ) {
        $this->topicRegistry = $topicRegistry;
        $this->router = $router;
        $this->topicPeriodicTimer = $topicPeriodicTimer;
        $this->topicManager = $topicManager;
        $this->logger = null === $logger ? new NullLogger() : $logger;
    }

    /**
     * @param ConnectionInterface $conn
     * @param Topic $topic
     * @param WampRequest $request
     */
    public function onSubscribe(ConnectionInterface $conn, Topic $topic, WampRequest $request)
    {
        $this->dispatch(self::SUBSCRIPTION, $conn, $topic, $request);
    }

    /**
     * @param WampRequest  $request
     * @param array|string $data
     * @param string $provider
     */
    public function onPush(WampRequest $request, $data, $provider)
    {
        $topic = $this->topicManager->getTopic($request->getMatched());
        $this->dispatch(self::PUSH, null, $topic, $request, $data, null, null, $provider);
    }

    /**
     * @param ConnectionInterface $conn
     * @param Topic $topic
     * @param WampRequest $request
     */
    public function onUnSubscribe(ConnectionInterface $conn, Topic $topic, WampRequest $request)
    {
        //if topic service exists, notify it
        $this->dispatch(self::UNSUBSCRIPTION, $conn, $topic, $request);
    }

    /**
     * @param ConnectionInterface $conn
     * @param Topic $topic
     * @param WampRequest $request
     * @param string $event
     * @param array $exclude
     * @param array $eligible
     */
    public function onPublish(ConnectionInterface $conn, Topic $topic, WampRequest $request, $event, array $exclude, array $eligible)
    {
        if (!$this->dispatch(self::PUBLISH, $conn, $topic, $request, $event, $exclude, $eligible)) {
            //default behaviour is to broadcast to all.
            $topic->broadcast($event);

            return;
        }
    }

    /**
     * @param string $calledMethod
     * @param ConnectionInterface|null $conn
     * @param Topic $topic
     * @param WampRequest $request
     * @param string|null $payload
     * @param string[]|null $exclude
     * @param string[]|null $eligible
     * @param string|null $provider
     * 
     * @return bool
     * 
     * @throws \Exception
     */
    public function dispatch($calledMethod, ConnectionInterface $conn = null, Topic $topic, WampRequest $request, $payload = null, $exclude = null, $eligible = null, $provider = null)
    {
        $dispatched = false;

        if ($topic) {
            foreach ((array) $request->getRoute()->getCallback() as $callback) {
                $appTopic = $this->topicRegistry->getTopic($callback);

                if ($appTopic instanceof TopicPeriodicTimerInterface) {
                    $appTopic->setPeriodicTimer($this->topicPeriodicTimer);

                    if (false === $this->topicPeriodicTimer->isRegistered($appTopic) && 0 !== count($topic)) {
                        $appTopic->registerPeriodicTimer($topic);
                    }
                }

                if ($calledMethod === static::UNSUBSCRIPTION && 0 === count($topic)) {
                    $this->topicPeriodicTimer->clearPeriodicTimer($appTopic);
                }

                if ($calledMethod === static::PUSH) {
                    if (!$appTopic instanceof PushableTopicInterface) {
                        throw new \Exception(sprintf('Topic %s doesn\'t support push feature', $appTopic->getName()));
                    }

                    $appTopic->onPush($topic, $request, $payload, $provider);
                    $dispatched = true;
                } else {
                    try {
                        if ($payload) { //its a publish call.
                            $appTopic->{$calledMethod}($conn, $topic, $request, $payload, $exclude, $eligible);
                        } else {
                            $appTopic->{$calledMethod}($conn, $topic, $request);
                        }

                        $dispatched = true;
                    } catch (\Exception $e) {
                        $this->logger->error($e->getMessage(), [
                            'code' => $e->getCode(),
                            'file' => $e->getFile(),
                            'trace' => $e->getTraceAsString(),
                        ]);

                        $conn->callError($topic->getId(), $topic, $e->getMessage(), [
                            'topic' => $topic,
                            'request' => $request,
                            'event' => $calledMethod,
                        ]);

                        $dispatched = false;
                    }
                }
            }
        }

        return $dispatched;
    }
}
