<?php

namespace App\Services\Common\Chat\MessageType\SystemNotify;

use App\Constants\StatusCode;
use App\Exception\BusinessException;
use App\Services\Common\Chat\MessageType\ChatMessageTypeHandlerInterface;

/**
 * 系统通知-异地登录
 * {
 * "device_type":"设备类型",
 * "ip": "127.0.0.1",                     // ip
 * "location": "上海市",                   // 位置
 * "link": "/post/12345",                 // 前端跳转路由
 * }
 */
class SystemAccountRemoteLoginHandler implements ChatMessageTypeHandlerInterface
{
    public static function getBody(array $msgBody): array
    {
        if (!isset($msgBody['device_type']) || empty($msgBody['ip']) || empty($msgBody['location'])) {
            throw new BusinessException(StatusCode::PARAMETER_ERROR, '消息体结构错误');
        }
        return [
            'device_type' => strval($msgBody['device_type']),
            'ip'          => strval($msgBody['ip']),
            'location'    => strval($msgBody['location']),
            'link'        => '',
        ];
    }

    public static function getPreview(array $msgBody): string
    {
        return '[账号异地登录]';
    }
}
