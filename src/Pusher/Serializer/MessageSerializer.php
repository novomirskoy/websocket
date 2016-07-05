<?php

namespace Novomirskoy\Websocket\Pusher\Serializer;

use Novomirskoy\Websocket\Pusher\MessageInterface;

/**
 * Class MessageSerializer
 * @package Novomirskoy\Websocket\Pusher\Serializer
 */
class MessageSerializer
{
    /**
     * @var Serializer
     */
    protected $serializer;

    /**
     * @var NormalizerInterface[]
     */
    protected $normalizers;

    /** @var  string */
    protected $class;

    /**
     * @var EncoderInterface[]
     */
    protected $encoders;

    public function __construct()
    {
        $this->normalizers = [
            new GetSetMethodNormalizer(),
        ];

        $this->encoders = [
            new JsonEncoder(),
        ];

        $this->serializer = new Serializer($this->normalizers, $this->encoders);
    }

    /**
     * @param MessageInterface $message
     *
     * @return string|\Symfony\Component\Serializer\Encoder\scalar
     */
    public function serialize(MessageInterface $message)
    {
        $this->class = get_class($message);

        return $this->serializer->serialize($message, 'json');
    }

    public function deserialize($data)
    {
        $class = null === $this->class ? 'Gos\Bundle\WebSocketBundle\Pusher\Message' : $this->class;

        return $this->serializer->deserialize($data, $class, 'json');
    }
}
