<?php

namespace Novomirskoy\Websocket\Client\Driver;

/**
 * Class InMemoryDriver
 * @package Novomirskoy\Websocket\Client\Driver
 */
class InMemoryDriver implements DriverInterface
{
    /**
     * @var array
     */
    protected $elements;

    public function __construct()
    {
        $this->elements = array();
    }

    /**
     * {@inheritdoc}
     */
    public function fetch($id)
    {
        if (!$this->contains($id)) {
            return false;
        }

        return $this->elements[$id];
    }

    /**
     * {@inheritdoc}
     */
    public function contains($id)
    {
        return isset($this->elements[$id]);
    }

    /**
     * {@inheritdoc}
     */
    public function save($id, $data, $lifeTime = 0)
    {
        $this->elements[$id] = $data;         //Lifetime is not supported

        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function delete($id)
    {
        unset($this->elements[$id]);

        return true;
    }
}
