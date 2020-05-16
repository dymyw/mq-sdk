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

发送消息

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

消费消息

```php
use Mq\Amqp\Consumer;

$consumer = new Consumer($connection);

// 回调函数
$callback = function($messageBody) {
    var_dump($messageBody);
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
    ->setCallback($callback);

// 消费
$consumer->consume(1);
```
