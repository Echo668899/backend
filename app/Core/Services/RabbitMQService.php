<?php

namespace App\Core\Services;

use App\Utils\LogUtil;
use Bunny\Channel;
use Bunny\Client;

class RabbitMQService extends BaseService
{
    /** @var Client null */
    private $client = null;

    /** @var Channel null */
    private $channel = null;

    /**
     * 暂存未注册的交换机
     * @var array
     */
    private array $pendingDeclaredExchanges = [];
    /**
     * 暂存未注册的队列
     * @var array
     */
    private array $pendingDeclaredQueues = [];

    /**
     * 暂存未注册的消费者
     * @var array
     */
    private array $pendingQueueConsumers = [];

    /**
     * 交换机和队列绑定
     * @var array
     */
    private array $pendingQueueBinds = [];

    public function run()
    {
        $this->connect();
    }

    /**
     * 生产
     * @param  string $message
     * @param  array  $headers
     * @param  string $exchange
     * @param  string $routingKey
     * @return void
     */
    public function publish(string $message, array $headers = [], string $exchange = '', string $routingKey = '')
    {
        $this->checkConnection();

        $exchange   = $exchange . '.exchange';
        $routingKey = $routingKey . '.routing';
        if ($this->channel) {
            $this->channel->publish($message, $headers, $exchange, $routingKey);
            //            LogUtil::debug("[RabbitMQ] 已推送交换机: $exchange 路由:$routingKey");
        }
    }

    /**
     * 添加交换机
     * @param       $exchangeName
     * @param       $exchangeType
     * @param       $passive
     * @param       $durable
     * @param       $autoDelete
     * @param       $internal
     * @param       $nowait
     * @param       $arguments
     * @return void
     */
    public function bindExchange($exchangeName, $exchangeType = 'direct', $passive = false, $durable = false, $autoDelete = false, $internal = false, $nowait = false, $arguments = []): void
    {
        $this->pendingDeclaredExchanges[] = [
            'exchange'      => $exchangeName . '.exchange',
            'exchange_type' => $exchangeType,
            'passive'       => $passive,
            'durable'       => $durable,
            'auto_delete'   => $autoDelete,
            'internal'      => $internal,
            'nowait'        => $nowait,
            'arguments'     => $arguments
        ];
    }

    /**
     * 添加队列
     * @param  string $queueName
     * @param         $passive
     * @param         $durable
     * @param         $exclusive
     * @param         $autoDelete
     * @param         $nowait
     * @param         $arguments
     * @return void
     */
    public function bindDeclaredQueue(string $queueName, $passive = false, $durable = false, $exclusive = false, $autoDelete = false, $nowait = false, $arguments = []): void
    {
        $this->pendingDeclaredQueues[] = [
            'queue'       => $queueName . '.queue',
            'passive'     => $passive,
            'durable'     => $durable,
            'exclusive'   => $exclusive,
            'auto_delete' => $autoDelete,
            'nowait'      => $nowait,
            'arguments'   => $arguments,
        ];
    }

    /**
     * 添加消费者
     */
    public function bindQueueConsumer(string $queueName, callable $callback, bool $autoAck = true): void
    {
        $this->pendingQueueConsumers[] = [
            'queue'    => $queueName . '.queue',
            'callback' => $callback,
            'autoAck'  => $autoAck
        ];
    }

    /**
     * 注册 queue 与 exchange 的绑定关系（延迟绑定）
     */
    public function bindQueueToExchange(string $queueName, string $exchangeName, string $routingKey): void
    {
        $this->pendingQueueBinds[] = [
            'queue'       => $queueName . '.queue',
            'exchange'    => $exchangeName . '.exchange',
            'routing_key' => $routingKey . '.routing',
        ];
    }

    public function onWorkerStop()
    {
        if ($this->channel) {
            $this->channel->close();
        }
        if ($this->client) {
            $this->client->disconnect();
        }
        LogUtil::debug('[RabbitMQ] 连接已关闭');
    }

    private function connect()
    {
        try {
            $config       = env()->path('rabbitmq');
            $this->client = new Client([
                'host'      => $config['host'] ?? '127.0.0.1',
                'port'      => $config['port'] ?? 5672,
                'user'      => strval($config['user']) ?? 'guest',
                'password'  => strval($config['password']) ?? 'guest',
                'vhost'     => $config['vhost'] ?? '/',
                'heartbeat' => 30, // 单位：秒
            ]);
            $this->client->connect();
            $this->channel = $this->client->channel();

            LogUtil::debug("[RabbitMQ] 连接成功 to:{$config['host']} vhost:{$config['vhost']}");

            $this->registerDeclaredExchange($this->channel);
            $this->registerDeclaredQueue($this->channel);
            $this->registerBindRouter($this->channel);
            $this->registerConsumer($this->channel);
        } catch (\Exception $e) {
            LogUtil::error('[RabbitMQ]  连接失败: ' . $e->getMessage());
        }
    }

    /**
     * 注册交换机
     * @param  Channel $channel
     * @return void
     */
    private function registerDeclaredExchange(Channel $channel)
    {
        // 自动注册所有之前存储的消费队列
        foreach ($this->pendingDeclaredExchanges as $item) {
            $channel->exchangeDeclare(
                $item['exchange'],
                $item['exchange_type'],
                $item['passive'],
                $item['durable'],
                $item['auto_delete'],
                $item['internal'],
                $item['nowait'],
                $item['arguments'],
            );
            LogUtil::debug("[RabbitMQ] 已声明交换机: {$item['exchange']}");
        }
        $this->pendingDeclaredExchanges = [];
    }

    /**
     * 注册队列
     * @param  Channel $channel
     * @return void
     */
    private function registerDeclaredQueue(Channel $channel)
    {
        // 自动注册所有之前存储的消费队列
        foreach ($this->pendingDeclaredQueues as $item) {
            $channel->queueDeclare(
                $item['queue'],
                $item['passive'],
                $item['durable'],
                $item['exclusive'],
                $item['auto_delete'],
                $item['nowait'],
                $item['arguments'],
            );
            LogUtil::debug("[RabbitMQ] 已声明队列: {$item['queue']}");
        }
        $this->pendingDeclaredQueues = [];
    }

    /**
     * 绑定路由
     * @param  Channel $channel
     * @return void
     */
    private function registerBindRouter(Channel $channel)
    {
        // 自动注册所有之前存储的消费队列
        foreach ($this->pendingQueueBinds as $item) {
            $channel->queueBind(
                $item['queue'],
                $item['exchange'],
                $item['routing_key']
            );
            LogUtil::debug("[RabbitMQ] 已绑定交换机和队列: exchange:{$item['exchange']} queue:{$item['queue']}");
        }
        $this->pendingQueueBinds = [];
    }

    /**
     * 注册消费者
     * @param  Channel $channel
     * @return void
     */
    private function registerConsumer(Channel $channel)
    {
        // 自动注册所有之前存储的消费队列
        foreach ($this->pendingQueueConsumers as $item) {
            $channel->consume(
                $item['callback'],
                $item['queue'],
                '',
                false,
                $item['autoAck']
            );
            LogUtil::debug("[RabbitMQ] 已监听消费队列: {$item['queue']}");
        }
        $this->pendingQueueConsumers = [];
    }

    /**
     * @return void
     */
    private function checkConnection()
    {
        try {
            // 使用 ping 方法检测 Redis 连接是否正常
            if ($this->client == null || $this->client->isConnected() == false) {
                $this->client  = null;
                $this->channel = null;
                throw new \Exception();
            }
        } catch (\Exception $e) {
            LogUtil::error('[RabbitMQ] 连接失效，重连中...');
            $this->connect();  // 重新连接
        }
    }
}
