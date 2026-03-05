<?php

declare(strict_types=1);

namespace App\Core\Mongodb;

use App\Constants\StatusCode;
use App\Exception\BusinessException;
use App\Utils\LogUtil;
use MongoDB\Driver\Exception\Exception as MongoDBException;
use MongoDB\Driver\Manager;
use MongoDB\Driver\Session;

class MongoDbConnection
{
    protected $config = [];
    protected $conn   = null;

    /** @var Session */
    protected $session = null;

    public function __construct($config)
    {
        $this->config = $config;
    }

    /**
     * 执行命令
     * @param                    $opts
     * @return array
     * @throws BusinessException
     */
    public function executeCommand($opts)
    {
        try {
            $start = microtime(true);

            $cmd     = new \MongoDB\Driver\Command($opts);
            $session = $this->getSession();
            if ($session) {
                $result = $this->getClient()->executeCommand($this->config['dbname'], $cmd, ['session' => $session])->toArray();
            } else {
                $result = $this->getClient()->executeCommand($this->config['dbname'], $cmd)->toArray();
            }

            $end  = microtime(true);
            $cost = ($end - $start) * 1000;  // 毫秒

            if ($cost >= 1000 * 2) {
                LogUtil::error(__CLASS__ . ' 慢查询 ' . json_encode(['command' => $opts, 'duration_ms' => round($cost, 2)]));
            }

            return json_decode(json_encode($result), true);
        } catch (MongoDBException $exception) {
            LogUtil::error($exception);
        }
        throw new BusinessException(StatusCode::DB_ERROR);
    }

    /**
     * 返回当前回话的session
     * @return Session
     */
    public function getSession()
    {
        return $this->session;
    }

    /**
     * 获取mongodb连接
     * @return Manager
     */
    public function getClient()
    {
        if ($this->conn == null) {
            $uri = 'mongodb://';
            if ($this->config['username']) {
                $uri .= "{$this->config['username']}:{$this->config['password']}@";
            }
            $host = empty($this->config['host']) ? '127.0.0.1' : $this->config['host'];
            $port = empty($this->config['port']) ? '27017' : $this->config['port'];
            $uri .= "{$host}:{$port}";
            if (isset($this->config['replica']) && $this->config['replica']) {
                $uri .= "/?replicaSet={$this->config['replica']}";
            }
            $this->conn = new Manager($uri);
        }
        return $this->conn;
    }

    /**
     * 开启事务
     */
    public function startTransaction()
    {
        $this->startSession()->startTransaction([]);
    }

    /**
     * 开启会话
     * @return Session
     */
    public function startSession()
    {
        if ($this->session == null) {
            $this->session = $this->getClient()->startSession(['causalConsistency' => true]);
        }
        return $this->session;
    }

    /**
     * 结束事务
     */
    public function abortTransaction()
    {
        $session = $this->getSession();
        if ($session) {
            $session->abortTransaction();
            $session->endSession();
        }
        $this->session = null;
    }

    /**
     * 结束回话
     */
    public function endSession()
    {
        if ($this->session) {
            $this->session->endSession();
        }
        $this->session = null;
    }

    /**
     * 提交事务
     */
    public function commitTransaction()
    {
        $session = $this->getSession();
        if ($session) {
            $session->commitTransaction();
            $session->endSession();
        }
        $this->session = null;
    }
}
