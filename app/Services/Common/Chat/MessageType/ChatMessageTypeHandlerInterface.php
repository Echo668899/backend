<?php

namespace App\Services\Common\Chat\MessageType;

interface ChatMessageTypeHandlerInterface
{
    public static function getBody(array $msgBody): array;

    public static function getPreview(array $msgBody): string;
}
