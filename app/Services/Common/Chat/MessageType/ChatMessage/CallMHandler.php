<?php

namespace App\Services\Common\Chat\MessageType\ChatMessage;

use App\Services\Common\Chat\MessageType\ChatMessageTypeHandlerInterface;

/**
 * 私聊信息-语音
 * {
 *
 * }
 */
class CallMHandler implements ChatMessageTypeHandlerInterface
{
    public static function getBody(array $msgBody): array
    {
        return [];
    }

    public static function getPreview(array $msgBody): string
    {
        return '[语音通话]';
    }
}
