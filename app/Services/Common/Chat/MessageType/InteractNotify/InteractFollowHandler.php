<?php

namespace App\Services\Common\Chat\MessageType\InteractNotify;

use App\Constants\StatusCode;
use App\Exception\BusinessException;
use App\Services\Common\Chat\MessageType\ChatMessageTypeHandlerInterface;

/**
 * 互动消息-关注
 * {
 *   "content": "关注了你",                // 通知文案
 *   "from_user_id": "20002",             // 关注者ID
 *   "link": "/user/20002"                // 跳转路由（查看用户主页）
 * }
 */
class InteractFollowHandler implements ChatMessageTypeHandlerInterface
{
    public static function getBody(array $msgBody): array
    {
        // 校验必填字段
        $required = ['from_user_id', 'link'];
        foreach ($required as $field) {
            if (empty($msgBody[$field])) {
                throw new BusinessException(StatusCode::PARAMETER_ERROR, "关注消息体缺少字段: {$field}");
            }
        }
        return [
            'content'      => strval(self::getPreview($msgBody)),
            'from_user_id' => strval($msgBody['from_user_id']),
            'link'         => strval($msgBody['link']),
        ];
    }

    public static function getPreview(array $msgBody): string
    {
        return '关注了你';
    }
}
