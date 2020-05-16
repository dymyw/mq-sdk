<?php

namespace Mq\Amqp;

use Mq\ProducerInterface;
use PhpAmqpLib\Message\AMQPMessage;

/**
 * Class AbstractProducer
 * @package Mq\Amqp
 */
abstract class AbstractProducer extends BaseAmqp implements ProducerInterface
{
    /**
     * @param string $messageBody
     * @return AMQPMessage
     */
    public function getMessage($messageBody)
    {
        $this->setParameter('delivery_mode', AMQPMessage::DELIVERY_MODE_PERSISTENT);

        $message = new AMQPMessage(
            $messageBody,
            $this->getParameters()
        );

        return $message;
    }

    /**
     * @param string $messageBody
     * @return mixed
     */
    abstract public function publish($messageBody);
}
