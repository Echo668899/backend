<?php

namespace App\Services\Common\Chat\MessageType\ChatMessage;

use App\Constants\StatusCode;
use App\Exception\BusinessException;
use App\Services\Common\Chat\MessageType\ChatMessageTypeHandlerInterface;

/**
 * 私聊信息-视频
 * {
 * "video_id":"23123", //上传后的id
 * "url":"/xxx/xxx/xx/xx.m3u8", //m3u8 url
 * "img":"/xxx/xxx/xx/xx.jpg", //封面图url
 * "width":"127", //视频宽,可选
 * "height":"100", //视频高,可选
 * "duration":"100", //视频时长
 * }
 */
class VideoHandler implements ChatMessageTypeHandlerInterface
{
    public static function getBody(array $msgBody): array
    {
        // /实际上是uploadId
        if (!isset($msgBody['video_id']) || empty($msgBody['video_id'])) {
            throw new BusinessException(StatusCode::PARAMETER_ERROR, '消息体错误');
        }
        //                if(!isset($msgBody['url'])||empty($msgBody['url'])){
        //                    throw new BusinessException(StatusCode::PARAMETER_ERROR,'消息体错误');
        //                }
        //                if(!isset($msgBody['size'])||empty($msgBody['size'])){
        //                    throw new BusinessException(StatusCode::PARAMETER_ERROR,'消息体错误');
        //                }
        //                if(!isset($msgBody['duration'])||empty($msgBody['duration'])){
        //                    throw new BusinessException(StatusCode::PARAMETER_ERROR,'消息体错误');
        //                }
        return [
            'video_id' => strval('upload_' . $msgBody['video_id']),
            'url'      => '',
            'img'      => '',
            'width'    => '',
            'height'   => '',
            'duration' => '',
        ];
    }

    public static function getPreview(array $msgBody): string
    {
        return '[视频]';
    }
}
