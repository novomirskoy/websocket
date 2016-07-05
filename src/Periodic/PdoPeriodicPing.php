<?php

namespace Novomirskoy\Websocket\Periodic;

use PDO;
use PDOException;
use Psr\Log\LoggerInterface;
use Psr\Log\NullLogger;

/**
 * Class PdoPeriodicPing
 * @package Novomirskoy\Websocket\Periodic
 */
class PdoPeriodicPing implements PeriodicInterface
{
    /**
     * @var PDO
     */
    protected $pdo;

    /**
     * @var LoggerInterface
     */
    protected $logger;

    /**
     * @var int|float
     */
    protected $timeout;

    /**
     * PdoPeriodicPing constructor.
     *
     * @param PDO|null $pdo
     * @param LoggerInterface|null $logger
     */
    public function __construct(PDO $pdo = null, LoggerInterface $logger = null)
    {
        $this->pdo = $pdo;
        $this->logger = null === $logger ? new NullLogger() : $logger;
        $this->timeout = 20;
    }

    /**
     * @param int|float $timeout
     */
    public function setTimeout($timeout)
    {
        $this->timeout = $timeout;
    }

    /**
     * @throws PDOException
     */
    public function tick()
    {
        if (null === $this->pdo) {
            $this->logger->warning('Unable to ping sql server, service pdo is unavailable');

            return;
        }

        //if connection is persistent we don't need to ping
        if (true === $this->pdo->getAttribute(PDO::ATTR_PERSISTENT)) {
            return;
        }

        try {
            $startTime = microtime(true);
            $this->pdo->query('SELECT 1');
            $endTime = microtime(true);
            $this->logger->notice(sprintf('Successfully ping sql server (~%s ms)', round(($endTime - $startTime) * 100000)));
        } catch (PDOException $e) {
            $this->logger->emergency('Sql server is gone, and unable to reconnect');
            throw $e;
        }
    }

    /**
     * @return int
     */
    public function getTimeout()
    {
        return $this->timeout;
    }
}
