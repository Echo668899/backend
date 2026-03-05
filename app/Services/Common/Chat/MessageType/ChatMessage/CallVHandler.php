<?php

namespace App\Services\Common\Chat\MessageType\ChatMessage;

use App\Services\Common\Chat\MessageType\ChatMessageTypeHandlerInterface;

/**
 * 私聊信息-视频
 * {
 *
 * }
 */
class CallVHandler implements ChatMessageTypeHandlerInterface
{
    public static function getBody(array $msgBody): array
    {
        return [];
    }

    public static function getPreview(array $msgBody): string
    {
        return '[视频通话]';
    }
}
