<?php

namespace App\Services\Common\Chat\MessageType\InteractNotify;

use App\Constants\StatusCode;
use App\Exception\BusinessException;
use App\Services\Common\Chat\MessageType\ChatMessageTypeHandlerInterface;

/**
 * 互动消息 - @我
 * {
 *   "content": "Alice 在帖子中提到了你",  // @内容
 *   "mention_type": "post",             // @发生位置类型: post|comment|danmaku
 *   "object_type": "post",              // 所属主对象类型 post|movie|novel|audio|comics
 *   "object_id": "p_12345",             // 主对象ID
 *   "object_img": "/xxx/xxx/xxx.jpg",   // 主对象图片
 *   "object_title": "海边日落",          // 主内容标题
 *   "from_user_id": "20002",            // 触发者ID
 *   "link": "/post/12345#c_100"         // 跳转路由（可锚定位置）
 * }
 */
class InteractMentionHandler implements ChatMessageTypeHandlerInterface
{
    public static function getBody(array $msgBody): array
    {
        // 校验必填字段
        $required = ['content', 'mention_type', 'object_type', 'object_id', 'object_img', 'object_title', 'from_user_id', 'link'];
        foreach ($required as $field) {
            if (empty($msgBody[$field])) {
                throw new BusinessException(StatusCode::PARAMETER_ERROR, "@消息体缺少字段: {$field}");
            }
        }
        // 统一结构
        return [
            'content'      => strval($msgBody['content']),
            'mention_type' => strval($msgBody['mention_type']),
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
        $type  = $msgBody['mention_type'] ?? 'post';
        $title = $msgBody['object_title'] ?? '';

        // @发生位置语义化
        switch ($type) {
            case 'comment':
                $preview = '在评论中提到了你';
                break;
            case 'danmaku':
                $preview = '在弹幕中提到了你';
                break;
            case 'post':
            default:
                $preview = '在帖子中提到了你';
                break;
        }

        // 组合展示标题
        if ($title !== '') {
            return sprintf('%s：%s', $preview, mb_substr($title, 0, 20));
        }
        return $preview;
    }
}
