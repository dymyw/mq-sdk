## 消息队列扩展包

### 使用说明

建立连接

```php
use PhpAmqpLib\Connection\AMQPLazyConnection;

$connection = new AMQPLazyConnection(
    $config['host'],
    $config['port'],
    $config['user'],
    $config['password']
);
```

发送消息（普通发送）

```php
use Mq\Amqp\Producer;

$producer = new Producer($connection);

// 配置
$producer
    ->setExchangeOptions([
        'name'  => $config['exchange_name'],
        'type'  => $config['exchange_type'],
    ])
    ->setRoutingKey($config['routing_key']);

// 发送
$message = json_encode([
    'event' => 'User.AddPoint',
    'body'  => [
        'user_id'   => 1,
        'point'     => 100,
    ],
]);
$producer->publish($message);
```

可靠性发送

```php
use Mq\Amqp\Producer;
use PhpAmqpLib\Message\AMQPMessage;

$producer = new Producer($connection);

$channel = $producer->getChannel();

// 设置 ack 成功回调函数
$channel->set_ack_handler(
    function (AMQPMessage $message) {
        echo 'ack ----' . PHP_EOL;
        var_dump($message->body, $message->delivery_info);
    }
);

// 设置 ack 失败回调函数
$channel->set_nack_handler(
    function (AMQPMessage $message) {
        echo 'no ack ----' . PHP_EOL;
    }
);

// 配置
$producer
    ->setExchangeOptions([
        'name'  => $config['exchange_name'],
        'type'  => $config['exchange_type'],
    ])
    ->setRoutingKey($config['routing_key']);

// 确认模式
$channel->confirm_select();

// 发送
$producer->publish('111');
$producer->publish('222');

// 等待
$channel->wait_for_pending_acks();
```

消费消息

```php
use Mq\Amqp\Consumer;

$consumer = new Consumer($connection);

// 回调函数
$callback = function($messageBody) {
    var_dump($messageBody);
    return false;
};

// 配置
$consumer
    ->setQos([
        'prefetch_size'     => 0,
        'prefetch_count'    => 1,
        'global'            => true,
    ])
    ->setQueueOptions([
        'name'  => $config['queue_name'],
    ])
    ->setCallback($callback)
    ->setMaxRetries(3);

// 消费
$consumer->consume(1);
```
