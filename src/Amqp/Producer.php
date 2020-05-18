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

    /**
     * @param string $messageBody
     * @param int $timeout
     */
    public function confirmPublish($messageBody, $timeout = 1)
    {
        $message = $this->getMessage($messageBody);

        // confirm mode
        $this->channel->confirm_select();

        $this->channel->basic_publish(
            $message,
            $this->exchangeOptions['name'],
            $this->routingKey,
            true
        );

        // wait
        $this->channel->wait_for_pending_acks_returns($timeout);
    }

    /**
     * 设置发送成功 ack 回调
     *
     * @param callable $callback
     */
    public function setAckHandler(callable $callback)
    {
        $this->channel->set_ack_handler($callback);
    }

    /**
     * 设置发送失败 ack 回调
     *
     * @param callable $callback
     */
    public function setNackHandler(callable $callback)
    {
        $this->channel->set_nack_handler($callback);
    }

    /**
     * 设置路由失败回调
     *
     * @param callable $callback
     */
    public function setReturnHandler(callable $callback)
    {
        $this->channel->set_return_listener($callback);
    }
}
