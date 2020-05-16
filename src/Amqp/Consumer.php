<?php

namespace Mq\Amqp;

use ErrorException;
use PhpAmqpLib\Message\AMQPMessage;

/**
 * Class Consumer
 * @package Mq\Amqp
 */
class Consumer extends AbstractConsumer
{
    /**
     * @var int
     */
    protected $consumed = 0;

    /**
     * @var int
     */
    private $target;

    /**
     * @param int $number
     * @return mixed|void
     * @throws ErrorException
     */
    public function consume($number)
    {
        $this->target = $number;

        // qos
        if (!empty($this->consumerOptions['qos'])) {
            if (!empty($this->consumerOptions['qos'])) {
                $this->channel->basic_qos(
                    $this->consumerOptions['qos']['prefetch_size'],
                    $this->consumerOptions['qos']['prefetch_count'],
                    $this->consumerOptions['qos']['global']
                );
            }
        }

        // consume
        $this->channel->basic_consume(
            $this->queueOptions['name'],
            $this->getConsumerTag(),
            false,
            false,
            false,
            false,
            [$this, 'processMessage']
        );

        // receive
        while (count($this->channel->callbacks)) {
            $this->channel->wait();
        }
    }

    /**
     * @param AMQPMessage $message
     */
    public function processMessage(AMQPMessage $message)
    {
        call_user_func($this->callback, $message->body);
        $message->delivery_info['channel']
            ->basic_ack($message->delivery_info['delivery_tag']);

        $this->consumed++;
        if ($this->consumed == $this->target) {
            $message->delivery_info['channel']
                ->basic_cancel($message->delivery_info['consumer_tag']);
        }
    }
}
