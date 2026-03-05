<?php

declare(strict_types=1);

namespace App\Services\Common;

use App\Core\Services\BaseService;
use App\Utils\LogUtil;

/**
 * Class QueueService
 * @package App\Services
 */
class QueueService extends BaseService
{
    public const KEY = 'quque';

    /**
     * 执行程序
     * @throws \App\Exception\BusinessException
     */
    public static function run()
    {
        $queueKey = self::getQueueKey();
        // 注册到队列管理器
        redis()->sAdd(self::KEY, $queueKey);
        while (true) {
            $queue = redis()->rPop($queueKey);
            if (empty($queue) || empty($queue['action'])) {
                sleep(1);
                continue;
            }
            // 队列只是接收最近一小时内的数据,其它直接丢去,避免脏数据
            $queueTime = $queue['time'];
            if ((time() - $queueTime) > 3600) {
                continue;
            }
            $action = $queue['action'];
            LogUtil::info('Do queue:' . $action);
            $data = $queue['data'];
            switch ($action) {
            }
        }
    }

    /**
     * 加入队列
     * @param       $action
     * @param array $data
     */
    public static function join($action, $data = [])
    {
        $queueKey = self::getQueueKey();
        redis()->lPush($queueKey, [
            'action' => $action,
            'data'   => $data,
            'time'   => time()
        ]);
    }

    /**
     * 发送到其他节点
     * @param      $action
     * @param      $data
     * @param bool $self
     */
    public static function sendNodes($action, $data, $self = true)
    {
        $keys = redis()->sMembers(self::KEY);
        if (empty($keys)) {
            return;
        }
        foreach ($keys as $key) {
            if (self::getQueueKey() == $key && $self == false) {
                continue;
            }
            redis()->lPush($key, [
                'action' => $action,
                'data'   => $data,
                'time'   => time()
            ]);
        }
    }

    /**
     * 获取队列
     * @return string
     */
    protected static function getQueueKey()
    {
        $config = env()->path('queue')->toArray();
        return $config['channel'] . $config['index'];
    }
}
