<?php

namespace Novomirskoy\Websocket\Server\App;

use Novomirskoy\Websocket\Event\ClientErrorEvent;
use Novomirskoy\Websocket\Event\ClientEvent;
use Novomirskoy\Websocket\Event\Events;
use Novomirskoy\Websocket\Router\WampRequest;
use Novomirskoy\Websocket\Router\WampRouter;
use Novomirskoy\Websocket\Server\App\Dispatcher\RpcDispatcherInterface;
use Novomirskoy\Websocket\Server\App\Dispatcher\TopicDispatcherInterface;
use Novomirskoy\Websocket\User\UserInterface;
use Psr\Log\LoggerInterface;
use Psr\Log\NullLogger;
use Ratchet\ConnectionInterface;
use Ratchet\Wamp\Topic;
use Ratchet\Wamp\WampServerInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

/**
 * Class WampApplication
 * @package Novomirskoy\Websocket\Server\App
 */
class WampApplication implements WampServerInterface
{
    /**
     * @var TopicDispatcherInterface
     */
    protected $topicDispatcher;

    /**
     * @var RpcDispatcherInterface
     */
    protected $rpcDispatcher;

    /**
     * @var EventDispatcherInterface
     */
    protected $eventDispatcher;

    /**
     * @var ClientStorageInterface
     */
    protected $clientStorage;

    /**
     * @var LoggerInterface
     */
    protected $logger;

    /**
     * @var WampRouter
     */
    protected $wampRouter;

    /**
     * @param RpcDispatcherInterface   $rpcDispatcher
     * @param TopicDispatcherInterface $topicDispatcher
     * @param EventDispatcherInterface $eventDispatcher
     * @param ClientStorageInterface   $clientStorage
     * @param WampRouter               $wampRouter
     * @param LoggerInterface          $logger
     */
    public function __construct(
        RpcDispatcherInterface $rpcDispatcher,
        TopicDispatcherInterface $topicDispatcher,
        EventDispatcherInterface $eventDispatcher,
        ClientStorageInterface $clientStorage,
        WampRouter $wampRouter,
        LoggerInterface $logger = null
    ) {
        $this->rpcDispatcher = $rpcDispatcher;
        $this->topicDispatcher = $topicDispatcher;
        $this->eventDispatcher = $eventDispatcher;
        $this->clientStorage = $clientStorage;
        $this->wampRouter = $wampRouter;
        $this->logger = null === $logger ? new NullLogger() : $logger;
    }

    /**
     * @param ConnectionInterface        $conn
     * @param \Ratchet\Wamp\Topic|string $topic
     * @param string                     $event
     * @param array                      $exclude
     * @param array                      $eligible
     */
    public function onPublish(ConnectionInterface $conn, $topic, $event, array $exclude, array $eligible)
    {
        $user = $this->clientStorage->getClient($conn->WAMP->clientStorageId);
        $username = $user instanceof UserInterface ? $user->getUsername() : $user;

        $this->logger->info(sprintf(
            '%s publish to %s',
            $username,
            $topic->getId()
        ));

        $request = $this->wampRouter->match($topic);

        $this->topicDispatcher->onPublish($conn, $topic, $request, $event, $exclude, $eligible);
    }

    /**
     * @param WampRequest $request
     * @param string      $data
     * @param string      $provider
     */
    public function onPush(WampRequest $request, $data, $provider)
    {
        $this->logger->info(sprintf('Pusher %s has pushed', $provider), [
            'provider' => $provider,
            'topic' => $request->getMatched(),
        ]);

        $this->topicDispatcher->onPush($request, $data, $provider);
    }

    /**
     * @param ConnectionInterface $conn
     * @param string              $id
     * @param Topic               $topic
     * @param array               $params
     */
    public function onCall(ConnectionInterface $conn, $id, $topic, array $params)
    {
        $request = $this->wampRouter->match($topic);
        $this->rpcDispatcher->dispatch($conn, $id, $topic, $request, $params);
    }

    /**
     * @param ConnectionInterface        $conn
     * @param \Ratchet\Wamp\Topic|string $topic
     */
    public function onSubscribe(ConnectionInterface $conn, $topic)
    {
        $user = $this->clientStorage->getClient($conn->WAMP->clientStorageId);
        $username = $user instanceof UserInterface ? $user->getUsername() : $user;

        $this->logger->info(sprintf(
            '%s subscribe to %s',
            $username,
            $topic->getId()
        ));

        $wampRequest = $this->wampRouter->match($topic);

        $this->topicDispatcher->onSubscribe($conn, $topic, $wampRequest);
    }

    /**
     * @param ConnectionInterface        $conn
     * @param \Ratchet\Wamp\Topic|string $topic
     */
    public function onUnSubscribe(ConnectionInterface $conn, $topic)
    {
        $user = $this->clientStorage->getClient($conn->WAMP->clientStorageId);
        $username = $user instanceof UserInterface ? $user->getUsername() : $user;

        $this->logger->info(sprintf(
            'User %s unsubscribed to %s',
            $username,
            $topic->getId()
        ));

        $wampRequest = $this->wampRouter->match($topic);

        $this->topicDispatcher->onUnSubscribe($conn, $topic, $wampRequest);
    }

    /**
     * @param ConnectionInterface $conn
     */
    public function onOpen(ConnectionInterface $conn)
    {
        $event = new ClientEvent($conn, ClientEvent::CONNECTED);
        $this->eventDispatcher->dispatch(Events::CLIENT_CONNECTED, $event);
    }

    /**
     * @param ConnectionInterface $conn
     */
    public function onClose(ConnectionInterface $conn)
    {
        foreach ($conn->WAMP->subscriptions as $topic) {
            $wampRequest = $this->wampRouter->match($topic);
            $this->topicDispatcher->onUnSubscribe($conn, $topic, $wampRequest);
        }

        $event = new ClientEvent($conn, ClientEvent::DISCONNECTED);
        $this->eventDispatcher->dispatch(Events::CLIENT_DISCONNECTED, $event);
    }

    /**
     * @param ConnectionInterface $conn
     * @param \Exception          $e
     */
    public function onError(ConnectionInterface $conn, \Exception $e)
    {
        $event = new ClientErrorEvent($conn, ClientEvent::ERROR);
        $event->setException($e);
        $this->eventDispatcher->dispatch(Events::CLIENT_ERROR, $event);
    }
}
