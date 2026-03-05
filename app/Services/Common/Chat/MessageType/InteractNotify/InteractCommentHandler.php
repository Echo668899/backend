<?php

namespace App\Services\Common\Chat\MessageType\InteractNotify;

use App\Constants\StatusCode;
use App\Exception\BusinessException;
use App\Services\Common\Chat\MessageType\ChatMessageTypeHandlerInterface;

/**
 * 互动消息-评论
 * {
 * "content":"文字 哈哈，这张照片太棒了！,图片 /xxx/xxx/xxx.jpg", //评论内容
 * "comment_type":"评论类型 text,image",
 * "object_type": "movie",                          // 评论对象类型 post|movie|novel|audio|comics|comment
 * "object_id": "p_12345",                          // 对象ID
 * "object_img": "/upload/movie/cover.jpg",         // 被评论资源封面图
 * "object_title": "你的名字。",                      // 被评论资源标题
 * "comment_id": "c_100",                           // 根评论ID
 * "parent_id": "c_8888",                           // 可选 上级评论ID
 * "from_user_id": "20002",                         // 评论者id
 * "link": "/post/12345",                           // 前端跳转路由
 * }
 */
class InteractCommentHandler implements ChatMessageTypeHandlerInterface
{
    public static function getBody(array $msgBody): array
    {
        // 校验必填字段
        $required = ['comment_type', 'object_type', 'object_id', 'object_img', 'object_title', 'comment_id', 'from_user_id', 'link'];
        foreach ($required as $field) {
            if (empty($msgBody[$field])) {
                throw new BusinessException(StatusCode::PARAMETER_ERROR, "评论消息体缺少字段: {$field}");
            }
        }
        return [
            'content'      => strval(self::getPreview($msgBody)),
            'comment_type' => strval($msgBody['comment_type']),
            'object_type'  => strval($msgBody['object_type']), // post|video|comment
            'object_id'    => strval($msgBody['object_id']),
            'object_img'   => strval($msgBody['object_img']),
            'object_title' => strval($msgBody['object_title']),
            'comment_id'   => strval($msgBody['comment_id']),
            'parent_id'    => strval($msgBody['parent_id']),
            'from_user_id' => strval($msgBody['from_user_id']),
            'link'         => strval($msgBody['link']),
        ];
    }

    public static function getPreview(array $msgBody): string
    {
        // 回复了我
        if ($msgBody['object_type'] == 'comment') {
            if ($msgBody['comment_type'] == 'text') {
                return sprintf('回复: %s', mb_substr($msgBody['content'], 0, 20));
            }
            if ($msgBody['comment_type'] == 'image') {
                return sprintf('回复: %s', '[图片]');
            }
        } else {
            // 评论了我
            if ($msgBody['comment_type'] == 'text') {
                return sprintf('评论了你: %s', mb_substr($msgBody['content'], 0, 20));
            }
            if ($msgBody['comment_type'] == 'image') {
                return sprintf('评论了你: %s', '[图片]');
            }
        }
        return '';
    }
}
