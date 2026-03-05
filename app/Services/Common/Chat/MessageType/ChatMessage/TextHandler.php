<?php

namespace App\Services\Common\Chat\MessageType\ChatMessage;

use App\Constants\StatusCode;
use App\Exception\BusinessException;
use App\Services\Common\Chat\MessageType\ChatMessageTypeHandlerInterface;

/**
 * 私聊信息-文字
 * {
 * "text":"文字 哈哈，这张照片太棒了！", //内容
 * }
 */
class TextHandler implements ChatMessageTypeHandlerInterface
{
    public static function getBody(array $msgBody): array
    {
        if (empty($msgBody['text'])) {
            throw new BusinessException(StatusCode::PARAMETER_ERROR, '文本不能为空');
        }
        if (mb_strlen($msgBody['text']) > 500) {
            throw new BusinessException(StatusCode::PARAMETER_ERROR, '文本过长');
        }
        return ['text' => $msgBody['text']];
    }

    public static function getPreview(array $msgBody): string
    {
        $text = $msgBody['text'] ?? '';
        return mb_strlen($text) > 50 ? mb_substr($text, 0, 50) . '...' : $text;
    }
}
