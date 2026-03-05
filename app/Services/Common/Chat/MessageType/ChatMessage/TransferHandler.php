<?php

namespace App\Services\Common\Chat\MessageType\ChatMessage;

use App\Constants\StatusCode;
use App\Exception\BusinessException;
use App\Services\Common\Chat\MessageType\ChatMessageTypeHandlerInterface;

/**
 * 私聊信息-转账
 * {
 * "url":"/xxx/xxx/xx/jpg", //图片url
 * "width":"127", //图片宽,可选
 * "height":"100", //图片高,可选
 * }
 */
class TransferHandler implements ChatMessageTypeHandlerInterface
{
    public static function getBody(array $msgBody): array
    {
        if (!isset($msgBody['text']) || empty($msgBody['text'])) {
            throw new BusinessException(StatusCode::PARAMETER_ERROR, '消息体错误');
        }
        return [
            'text' => $msgBody['text']
        ];
    }

    public static function getPreview(array $msgBody): string
    {
        return '[转账]';
    }
}
