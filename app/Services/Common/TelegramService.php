<?php

namespace App\Services\Common;


use App\Core\Services\BaseService;
use App\Utils\CommonUtil;


class TelegramService extends BaseService
{
    const API_URL = 'https://api.telegram.org/bot';

    /**
     * 获取更新列表
     * @param $token
     * @return mixed
     * @throws \Exception
     */
    public static function getUpdates($token)
    {
        if(empty($token)){
            throw new \Exception('参数错误');
        }
        $url = self::API_URL.$token."/getUpdates";
        $result = CommonUtil::httpPost($url, [],10);
        $result = json_decode($result,true);
        return $result;
    }

    /**
     * 发送消息-文字
     * @param $token
     * @param $chatId
     * @param $text
     * @return bool
     * @throws \Exception
     */
    public static function sendMessage($token,$chatId,$text)
    {
        if(empty($token)){
            throw new \Exception('参数错误');
        }
        $url = self::API_URL.$token.'/';
        $result = CommonUtil::httpPost($url, json_encode([
            'method'    => 'sendMessage',
            'chat_id'   => $chatId,
            'text'      => $text,
            'parse_mode'=>'HTML',
        ]),10, array("Content-Type: application/json"));

        $result = json_decode($result,true);
        if($result['error_code']){
            throw new \Exception($result['description']);
        }
        return true;
    }

    /**
     * @param $token
     * @param $chatId
     * @param $filePath
     * @param $caption
     * @return true
     * @throws \Exception
     */
    public static function sendFile($token, $chatId, $filePath, $caption = '')
    {
        if (empty($token) || empty($filePath)) {
            throw new \Exception('参数错误');
        }

        if (!file_exists($filePath)) {
            throw new \Exception('文件不存在: ' . $filePath);
        }

        // 注意：发文件不能走 JSON，必须 multipart
        $url = self::API_URL . $token . '/sendDocument';

        $ch = curl_init();
        curl_setopt_array($ch, [
            CURLOPT_URL            => $url,
            CURLOPT_POST           => true,
            CURLOPT_POSTFIELDS     => [
                'chat_id'   => $chatId,
                'caption'   => $caption,
                'parse_mode'=> 'HTML',
                'document'  => new \CURLFile($filePath),
            ],
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_TIMEOUT        => 30,
        ]);

        $result = curl_exec($ch);

        if ($result === false) {
            $error = curl_error($ch);
            curl_close($ch);
            throw new \Exception('curl错误: ' . $error);
        }

        curl_close($ch);

        $result = json_decode($result, true);

        // 保持和你原来一样的错误处理方式
        if (!empty($result['error_code'])) {
            throw new \Exception($result['description']);
        }

        if (isset($result['ok']) && $result['ok'] === false) {
            throw new \Exception($result['description']);
        }

        return true;
    }

    /**
     * 设置回调url
     *
     * 回调获取内容
     *
     * @param $token
     * @param $hookUrl
     * @param $ipAddress //指定ip
     * @return bool
     * @throws \Exception
     */
    public static function setWebHook($token, $hookUrl,$ipAddress='')
    {
        if(empty($token)||empty($hookUrl)){
            throw new \Exception('参数错误');
        }
        $url = self::API_URL.$token.'/';

        $data=[
            'method'    => 'setWebhook',
            'url'       => $hookUrl,
        ];
        if(!empty($ipAddress)){
            $data['ip_address'] = $ipAddress;
        }
        $result = CommonUtil::httpPost($url, json_encode($data),10, array("Content-Type: application/json"));
        $result = json_decode($result,true);
        if($result['error_code']){
            throw new \Exception($result['description']);
        }
        return true;
    }

    /**
     * 查看hook设置信息
     * @param $token
     * @return mixed
     * @throws \Exception
     */
    public static function getWebhookInfo($token)
    {
        if(empty($token)){
            throw new \Exception('参数错误');
        }
        $url = self::API_URL.$token."/getWebhookInfo";
        $result = CommonUtil::httpPost($url, [],10);
        $result = json_decode($result,true);
        return $result;
    }

    /**
     * 删除hook设置
     * @param $token
     * @return bool|mixed|string
     * @throws \Exception
     */
    public static function deleteWebhook($token)
    {
        if(empty($token)){
            throw new \Exception('参数错误');
        }
        $url = self::API_URL.$token."/deleteWebhook";
        $result = CommonUtil::httpPost($url, [],10);
        $result = json_decode($result,true);
        return $result;
    }

}
