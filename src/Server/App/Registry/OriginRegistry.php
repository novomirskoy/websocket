<?php

namespace Novomirskoy\Websocket\Server\App\Registry;

/**
 * Class OriginRegistry
 * @package Novomirskoy\Websocket\Server\App\Registry
 */
class OriginRegistry
{
    /**
     * @var array
     */
    protected $origins;

    public function __construct()
    {
        $this->origins = [];
    }

    /**
     * @param $origin
     */
    public function addOrigin($origin)
    {
        $this->origins[] = $origin;
    }

    /**
     * @return array
     */
    public function getOrigins()
    {
        return $this->origins;
    }
}
