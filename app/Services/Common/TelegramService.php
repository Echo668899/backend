<?php

namespace App\Services\Common;

use App\Core\Services\BaseService;
use App\Utils\CommonUtil;

class TelegramService extends BaseService
{
    public const API_URL = 'https://api.telegram.org/bot';

    /**
     * 获取更新列表
     * @param             $token
     * @return mixed
     * @throws \Exception
     */
    public static function getUpdates($token)
    {
        if (empty($token)) {
            throw new \Exception('参数错误');
        }
        $url    = self::API_URL . $token . '/getUpdates';
        $result = CommonUtil::httpPost($url, [], 10);
        $result = json_decode($result, true);
        return $result;
    }

    /**
     * 发送消息
     * @param             $token
     * @param             $chatId
     * @param             $text
     * @return bool
     * @throws \Exception
     */
    public static function sendMessage($token, $chatId, $text)
    {
        if (empty($token)) {
            throw new \Exception('参数错误');
        }
        $url    = self::API_URL . $token . '/';
        $result = CommonUtil::httpPost($url, json_encode([
            'method'     => 'sendMessage',
            'chat_id'    => $chatId,
            'text'       => $text,
            'parse_mode' => 'HTML',
        ]), 10, ['Content-Type: application/json']);

        $result = json_decode($result, true);
        if ($result['error_code']) {
            throw new \Exception($result['description']);
        }
        return true;
    }

    /**
     * 设置回调url
     *
     * 回调获取内容
     * {"update_id":24757117,"message":{"message_id":88,"from":{"id":1630917144,"is_bot":false,"first_name":"\u590f\u5c0f\u5ddd","username":"BA1456","language_code":"zh-hans"},"chat":{"id":-476787557,"title":"\u6f2b\u753b\u66f4\u65b0","type":"group","all_members_are_administrators":true},"date":1631698337,"text":"/jjjjj","entities":[{"offset":0,"length":6,"type":"bot_command"}]}}
     *
     * @param             $token
     * @param             $hookUrl
     * @param             $ipAddress //指定ip
     * @return bool
     * @throws \Exception
     */
    public static function setWebHook($token, $hookUrl, $ipAddress = '')
    {
        if (empty($token) || empty($hookUrl)) {
            throw new \Exception('参数错误');
        }
        $url = self::API_URL . $token . '/';

        $data = [
            'method' => 'setWebhook',
            'url'    => $hookUrl,
        ];
        if (!empty($ipAddress)) {
            $data['ip_address'] = $ipAddress;
        }
        $result = CommonUtil::httpPost($url, json_encode($data), 10, ['Content-Type: application/json']);
        $result = json_decode($result, true);
        if ($result['error_code']) {
            throw new \Exception($result['description']);
        }
        return true;
    }

    /**
     * 查看hook设置信息
     * @param             $token
     * @return mixed
     * @throws \Exception
     */
    public static function getWebhookInfo($token)
    {
        if (empty($token)) {
            throw new \Exception('参数错误');
        }
        $url    = self::API_URL . $token . '/getWebhookInfo';
        $result = CommonUtil::httpPost($url, [], 10);
        $result = json_decode($result, true);
        return $result;
    }

    /**
     * 删除hook设置
     * @param                    $token
     * @return bool|mixed|string
     * @throws \Exception
     */
    public static function deleteWebhook($token)
    {
        if (empty($token)) {
            throw new \Exception('参数错误');
        }
        $url    = self::API_URL . $token . '/deleteWebhook';
        $result = CommonUtil::httpPost($url, [], 10);
        $result = json_decode($result, true);
        return $result;
    }
}
