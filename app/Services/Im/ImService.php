<?php

namespace App\Services\Im;

use App\Constants\CacheKey;
use App\Core\Services\BaseService;
use App\Services\Im\Entity\ImPayloadMessage;
use App\Utils\AesUtil;
use App\Utils\CommonUtil;
use App\Utils\LogUtil;

/**
 * IM服务
 */
class ImService extends BaseService
{
    public const ACTION_SEND_TO_USER = 'sendToUser';

    /**
     * 推送数据给指定用户
     * 只处理消息推送,不参与业务逻辑
     * @param                   $fromId
     * @param                   $toId
     * @param                   $msgId
     * @param  ImPayloadMessage $payload
     * @param  mixed            $cmd
     * @return void
     */
    public static function sendToUser($cmd, $fromId, $toId, $msgId, ImPayloadMessage $payload)
    {
        try {
            $fromId = self::fmt($fromId);
            $toId   = self::fmt($toId);

            self::httpRequest([
                'appid'     => self::getConfig()['appid'],
                'from_id'   => $fromId,
                'to_id'     => $toId,
                'msg_id'    => $msgId,
                'cmd'       => $cmd,
                'data'      => AesUtil::encrypt(json_encode($payload), self::getConfig()['key']),
                'timestamp' => time()
            ]);
            //            LogUtil::info("推送消息 from_id:{$fromId} to_id:{$toId} message:".json_encode($payload));
        } catch (\Exception $e) {
            LogUtil::error(sprintf('%s in %s line %s', $e->getMessage(), $e->getFile(), $e->getLine()));
        }
    }

    /**
     * 加密key
     * @return mixed|null
     */
    public static function getConfig()
    {
        $config = env()->path('imserver');
        return [
            'appid'    => $config['appid'],
            'key'      => $config['key'],
            'ws_url'   => $config['ws_url'],
            'http_url' => $config['http_url'],
        ];
    }

    /**
     * 强制下线用户
     * @param  string|int|array $ids
     * @return void
     */
    public static function kill($ids)
    {
        try {
            if (!is_array($ids)) {
                $ids = [$ids];
            }

            foreach ($ids as &$id) {
                $id = self::fmt($id);
                unset($id);
            }

            self::httpRequest([
                'appid' => self::getConfig()['appid'],
                'cmd'   => 'kill',
                'data'  => json_encode($ids),
            ]);
        } catch (\Exception $e) {
            LogUtil::error(sprintf('%s in %s line %s', $e->getMessage(), $e->getFile(), $e->getLine()));
        }
    }

    /**
     * 刷新在线用户
     * @return void
     */
    public static function doGetOnlineUid()
    {
        $appid = self::getConfig()['appid'];
        $rows  = self::httpRequest([
            'appid' => $appid,
            'cmd'   => 'getOnlineUid',
            'data'  => json_encode([]),
        ]);

        foreach ($rows as &$uid) {
            $uid = ltrim($uid, "{$appid}_");
            unset($uid);
        }
        redis()->del(CacheKey::ONLINE_USER);
        redis()->sAddArray(CacheKey::ONLINE_USER, $rows);
    }

    /**
     * 是否在线
     * @param              $uid
     * @return bool|\Redis
     */
    public static function isOnline($uid)
    {
        return redis()->sismember(CacheKey::ONLINE_USER, $uid);
    }

    /**
     * 获取websocket连接
     * @param         $userId
     * @param         $deviceId
     * @return string
     */
    public static function getWsUrl($userId, $deviceId)
    {
        $config = self::getConfig();
        if (empty($config['http_url'])) {
            return null;
        }
        $token = [
            'user_id'    => self::fmt($userId),
            'device_id'  => $deviceId,
            'expired_at' => time() + 86400 * 15,
        ];
        $tokenStr = AesUtil::encrypt(json_encode($token), $config['key']);
        return $config['ws_url'] . '?token=' . $tokenStr;
    }

    /**
     * 格式化用户id
     * @param         $userId
     * @return string
     */
    protected static function fmt($userId)
    {
        $config = self::getConfig();
        return "{$config['appid']}_{$userId}";
    }

    /**
     * @param             $message
     * @return mixed|null
     */
    private static function httpRequest($message)
    {
        $config = self::getConfig();
        if (empty($config['http_url'])) {
            return null;
        }
        $url    = $config['http_url'] . '/api/im';
        $result = CommonUtil::httpPost($url, json_encode($message));
        $result = empty($result) ? null : json_decode($result, true);
        if (empty($result) || $result['status'] != 'y' || empty($result['data'])) {
            return null;
        }
        return $result['data'];
    }
}
