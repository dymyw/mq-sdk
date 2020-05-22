<?php

namespace Mq\Amqp;

use InvalidArgumentException;

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

    /**
     * @param array $messageBodies
     */
    public function batchPublish(array $messageBodies)
    {
        foreach ($messageBodies as $key => $messageBody) {
            if (!is_string($messageBody) && !is_int($messageBody)) {
                throw new InvalidArgumentException(
                    'Message body must be string or integer, index: ' . $key
                );
            }
            $message = $this->getMessage($messageBody);

            $this->channel->batch_basic_publish(
                $message,
                $this->exchangeOptions['name'],
                $this->routingKey
            );
        }

        $this->channel->publish_batch();
    }
}
