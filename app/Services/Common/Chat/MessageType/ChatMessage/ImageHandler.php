<?php

namespace App\Services\Common\Chat\MessageType\ChatMessage;

use App\Constants\StatusCode;
use App\Exception\BusinessException;
use App\Services\Common\Chat\MessageType\ChatMessageTypeHandlerInterface;

/**
 * 私聊信息-图片
 * {
 * "url":"/xxx/xxx/xx/jpg", //图片url
 * "width":"127", //图片宽,可选
 * "height":"100", //图片高,可选
 * }
 */
class ImageHandler implements ChatMessageTypeHandlerInterface
{
    public static function getBody(array $msgBody): array
    {
        if (!isset($msgBody['url']) || empty($msgBody['url'])) {
            throw new BusinessException(StatusCode::PARAMETER_ERROR, '图片地址不存在');
        }
        if (!isset($msgBody['width']) || empty($msgBody['width'])) {
            throw new BusinessException(StatusCode::PARAMETER_ERROR, '图片宽度错误');
        }
        if (!isset($msgBody['height']) || empty($msgBody['height'])) {
            throw new BusinessException(StatusCode::PARAMETER_ERROR, '图片高度错误');
        }
        return [
            'url'    => $msgBody['url'],
            'width'  => strval($msgBody['width'] ?? ''),
            'height' => strval($msgBody['height'] ?? ''),
        ];
    }

    public static function getPreview(array $msgBody): string
    {
        return '[图片]';
    }
}
