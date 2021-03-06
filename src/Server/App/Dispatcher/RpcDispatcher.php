<?php

namespace Novomirskoy\Websocket\Server\App\Dispatcher;

use Novomirskoy\Websocket\Router\WampRequest;
use Novomirskoy\Websocket\RPC\RpcResponse;
use Novomirskoy\Websocket\Server\App\Registry\RpcRegistry;
use Ratchet\ConnectionInterface;
use Ratchet\Wamp\WampConnection;

/**
 * Class RpcDispatcher
 * @package Novomirskoy\Websocket\Server\App\Dispatcher
 */
class RpcDispatcher implements RpcDispatcherInterface
{
    /**
     * @var RpcRegistry
     */
    protected $rpcRegistry;


    /**
     * RpcDispatcher constructor.
     * 
     * @param RpcRegistry $rpcRegistry
     */
    public function __construct(RpcRegistry $rpcRegistry)
    {
        $this->rpcRegistry = $rpcRegistry;
    }

    /**
     * @param ConnectionInterface|WampConnection $conn
     * @param string $id
     * @param string $topic
     * @param WampRequest $request
     * @param array $params
     */
    public function dispatch(ConnectionInterface $conn, $id, $topic, WampRequest $request, array $params)
    {
        $callback = $request->getRoute()->getCallback();

        try {
            $procedure = $this->rpcRegistry->getRpc($callback);
        } catch (\Exception $e) {
            $conn->callError($id, $topic, $e->getMessage(), [
                'rpc' => $topic,
                'request' => $request,
            ]);

            return;
        }

        $method = $this->toCamelCase($request->getAttributes()->get('method'));
        $result = null;

        try {
            $result = call_user_func([$procedure, $method], $conn, $request, $params);
        } catch (\Exception $e) {
            $conn->callError($id, $topic, $e->getMessage(),  [
                'code' => $e->getCode(),
                'rpc' => $topic,
                'params' => $params,
                'request' => $request,
            ]);

            return;
        }

        if ($result === null) {
            $result = false;
        }

        if ($result) {
            if ($result instanceof RpcResponse) {
                $result = $result->getData();
            } elseif (!is_array($result)) {
                $result = [$result];
            }

            $conn->callResult($id, $result);

            return;
        } elseif ($result === false) {
            $conn->callError($id, $topic, 'RPC Error',  [
                'rpc' => $topic,
                'params' => $params,
                'request' => $request,
            ]);
        }

        $conn->callError($id, $topic, 'Unable to find that command',  [
            'rpc' => $topic->getId(),
            'params' => $params,
            'request' => $request,
        ]);

        return;
    }

    /**
     * @param $str
     *
     * @return string
     */
    protected function toCamelCase($str)
    {
        return preg_replace_callback('/_([a-z])/', function ($c) { return strtoupper($c[1]); }, $str);
    }
}
