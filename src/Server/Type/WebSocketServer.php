<?php

namespace Novomirskoy\Websocket\Server\Type;

use Gos\Component\RatchetStack\Builder;
use Novomirskoy\Websocket\Event\Events;
use Novomirskoy\Websocket\Event\ServerEvent;
use Novomirskoy\Websocket\Periodic\PeriodicInterface;
use Novomirskoy\Websocket\Periodic\PeriodicMemoryUsage;
use Novomirskoy\Websocket\Pusher\ServerPushHandlerRegistry;
use Novomirskoy\Websocket\Server\App\Registry\OriginRegistry;
use Novomirskoy\Websocket\Server\App\Registry\PeriodicRegistry;
use Novomirskoy\Websocket\Server\App\Stack\OriginCheck;
use Novomirskoy\Websocket\Server\App\Stack\WampConnectionPeriodicTimer;
use Novomirskoy\Websocket\Server\App\WampApplication;
use Psr\Log\LoggerInterface;
use Psr\Log\NullLogger;
use Ratchet;
use Ratchet\Wamp\TopicManager;
use React\EventLoop\LoopInterface;
use React\Socket\Server;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\Session\Storage\Handler\NullSessionHandler;

/**
 * Class WebSocketServer
 * @package Novomirskoy\Websocket\Server\Type
 */
class WebSocketServer implements ServerInterface
{
    /**
     * @var LoopInterface
     */
    protected $loop;

    /**
     * @var \SessionHandler|null
     */
    protected $sessionHandler;

    /**
     * @var EventDispatcherInterface
     */
    protected $eventDispatcher;

    /**
     * @var PeriodicRegistry
     */
    protected $periodicRegistry;

    /**
     * @var WampApplication
     */
    protected $wampApplication;

    /**
     * @var OriginRegistry|null
     */
    protected $originRegistry;

    /**
     * @var bool
     */
    protected $originCheck;

    /**
     * @var LoggerInterface
     */
    protected $logger;

    /**
     * @var ServerPushHandlerRegistry
     */
    protected $serverPusherHandlerRegistry;

    /**
     * @var TopicManager
     */
    protected $topicManager;

    /**
     * WebSocketServer constructor.
     *
     * @param LoopInterface $loop
     * @param EventDispatcherInterface $eventDispatcher
     * @param PeriodicRegistry $periodicRegistry
     * @param WampApplication $wampApplication
     * @param OriginRegistry $originRegistry
     * @param bool $originCheck
     * @param TopicManager $topicManager
     * @param ServerPushHandlerRegistry $serverPushHandlerRegistry
     * @param LoggerInterface|null $logger
     */
    public function __construct(
        LoopInterface $loop,
        EventDispatcherInterface $eventDispatcher,
        PeriodicRegistry $periodicRegistry,
        WampApplication $wampApplication,
        OriginRegistry $originRegistry,
        $originCheck,
        TopicManager $topicManager,
        ServerPushHandlerRegistry $serverPushHandlerRegistry,
        LoggerInterface $logger = null
    ) {
        $this->loop = $loop;
        $this->eventDispatcher = $eventDispatcher;
        $this->periodicRegistry = $periodicRegistry;
        $this->wampApplication = $wampApplication;
        $this->originRegistry = $originRegistry;
        $this->originCheck = $originCheck;
        $this->logger = null === $logger ? new NullLogger() : $logger;
        $this->topicManager = $topicManager;
        $this->serverPusherHandlerRegistry = $serverPushHandlerRegistry;
        $this->sessionHandler = new NullSessionHandler();
    }

    /**
     * @param \SessionHandlerInterface $sessionHandler
     */
    public function setSessionHandler(\SessionHandlerInterface $sessionHandler)
    {
        $this->sessionHandler = $sessionHandler;
    }

    /**
     * @param string $host
     * @param string $port
     * @param bool $profile
     * 
     * @throws \React\Socket\ConnectionException
     */
    public function launch($host, $port, $profile)
    {
        $this->logger->info('Starting web socket');

        //In order to avoid circular reference
        $this->topicManager->setWampApplication($this->wampApplication);

        $stack = new Builder();

        $server = new Server($this->loop);
        $server->listen($port, $host);

        if (true === $profile) {
            $memoryUsagePeriodicTimer = new PeriodicMemoryUsage($this->logger);
            $this->periodicRegistry->addPeriodic($memoryUsagePeriodicTimer);
        }

        /** @var PeriodicInterface $periodic */
        foreach ($this->periodicRegistry->getPeriodics() as $periodic) {
            $this->loop->addPeriodicTimer($periodic->getTimeout(), [$periodic, 'tick']);

            $this->logger->info(sprintf(
                'Register periodic callback %s, executed each %s seconds',
                $periodic instanceof ProxyInterface ? get_parent_class($periodic) : get_class($periodic),
                $periodic->getTimeout()
            ));
        }

        $allowedOrigins = array_merge(array('localhost', '127.0.0.1'), $this->originRegistry->getOrigins());

        $stack
            ->push(Ratchet\Server\IoServer::class, $server, $this->loop)
            ->push(Ratchet\Http\HttpServer::class);

        if ($this->originCheck) {
            $stack->push(OriginCheck::class, $allowedOrigins, $this->eventDispatcher);
        }

        $stack
            ->push(Ratchet\WebSocket\WsServer::class)
            ->push(WampConnectionPeriodicTimer::class, $this->loop)
            ->push(Ratchet\Session\SessionProvider::class, $this->sessionHandler)
            ->push(Ratchet\Wamp\WampServer::class, $this->topicManager);

        $app = $stack->resolve($this->wampApplication);

        //Push Transport Layer
        foreach ($this->serverPusherHandlerRegistry->getPushers() as $handler) {
            try {
                $handler->handle($this->loop, $this->wampApplication);
            } catch (\Exception $e) {
                $this->logger->error($e->getMessage(), [
                    'code' => $e->getCode(),
                    'file' => $e->getFile(),
                    'line' => $e->getLine(),
                    'push_handler_name' => $handler->getName(),
                ]);
            }
        }

        /* Server Event Loop to add other services in the same loop. */
        $event = new ServerEvent($this->loop, $server);
        $this->eventDispatcher->dispatch(Events::SERVER_LAUNCHED, $event);

        $this->logger->info(sprintf(
            'Launching %s on %s PID: %s',
            $this->getName(),
            $host . ':' . $port,
            getmypid()
        ));

        $app->run();
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'Ratchet';
    }
}
