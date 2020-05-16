<?php

namespace Mq\Amqp;

/**
 * Class Producer
 * @package Mq\Amqp
 */
class Producer extends AbstractProducer
{
    /**
     * @param string $messageBody
     * @return mixed|void
     */
    public function publish($messageBody)
    {
        $message = $this->getMessage($messageBody);

        $this->channel->basic_publish(
            $message,
            $this->exchangeOptions['name'],
            $this->routingKey
        );
    }
}
