<?php

namespace App\Services\Common\Chat\MessageType\InteractNotify;

use App\Constants\StatusCode;
use App\Exception\BusinessException;
use App\Services\Common\Chat\MessageType\ChatMessageTypeHandlerInterface;

/**
 * 互动消息-点赞
 * {
 * "content":"Alice 赞了你的评论",                    //点赞内容
 * "object_type": "movie",                          // 点赞对象类型 post|movie|novel|audio|comics|comment
 * "object_id": "p_12345",                          // 对象ID
 * "object_img": "/upload/movie/cover.jpg",         // 被点赞资源封面图
 * "object_title": "你的名字。",                      // 被点赞资源标题
 * "from_user_id": "20002",                         // 点赞者id
 * "link": "/post/12345",                           // 前端跳转路由
 * }
 */
class InteractLikeHandler implements ChatMessageTypeHandlerInterface
{
    public static function getBody(array $msgBody): array
    {
        // 校验必填字段
        $required = ['object_type', 'object_id', 'object_img', 'object_title', 'from_user_id', 'link'];
        foreach ($required as $field) {
            if (empty($msgBody[$field])) {
                throw new BusinessException(StatusCode::PARAMETER_ERROR, "评论消息体缺少字段: {$field}");
            }
        }
        return [
            'content'      => strval(self::getPreview($msgBody)),
            'object_type'  => strval($msgBody['object_type']),
            'object_id'    => strval($msgBody['object_id']),
            'object_img'   => strval($msgBody['object_img']),
            'object_title' => strval($msgBody['object_title']),
            'from_user_id' => strval($msgBody['from_user_id']),
            'link'         => strval($msgBody['link']),
        ];
    }

    public static function getPreview(array $msgBody): string
    {
        if ($msgBody['object_type'] == 'comment') {
            return '赞了你的评论';
        }
        if ($msgBody['object_type'] == 'movie') {
            return '赞了你的视频';
        }
        if ($msgBody['object_type'] == 'post') {
            return '赞了你的帖子';
        }
        if ($msgBody['object_type'] == 'novel') {
            return '赞了你的小说';
        }
        if ($msgBody['object_type'] == 'audio') {
            return '赞了你的音频';
        }
        if ($msgBody['object_type'] == 'comics') {
            return '赞了你的漫画';
        }

        return '';
    }
}
