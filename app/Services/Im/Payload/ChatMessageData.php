<?php

namespace App\Services\Im\Payload;

use App\Services\Im\Entity\ImMessageData;

/**
 * chat.message
 * 客户端推送结构（带预览、已读、消息体）
 */
class ChatMessageData implements ImMessageData
{
    public string $chat_id;
    public string $from_id;
    public string $to_id;

    public string $msg_id;   // 当前消息ID
    public string $seqid;    // 当前消息序号

    public string $preview;  // 消息预览

    public string $last_read_id;     // 最后已读消息ID
    public string $last_read_seqid;  // 最后已读消息序号

    public string $msg_type; // text|image|video|transfer|location
    public array $msg_body;  // 消息内容（类型体）

    public string $unread_count; // 时间戳（秒）
    public string $timestamp; // 时间戳（秒）

    public function __construct($chatId, $fromId, $toId, $msgId, $seqid, $msgType, array $msgBody, $preview, $lastReadId, $lastReadSeqid, $unreadCount, $timestamp)
    {
        $this->chat_id         = strval($chatId);
        $this->from_id         = strval($fromId);
        $this->to_id           = strval($toId);
        $this->msg_id          = strval($msgId);
        $this->seqid           = strval($seqid);
        $this->msg_type        = strval($msgType);
        $this->msg_body        = $msgBody;
        $this->preview         = strval($preview);
        $this->last_read_id    = strval($lastReadId);
        $this->last_read_seqid = strval($lastReadSeqid);
        $this->unread_count    = strval($unreadCount);
        $this->timestamp       = strval($timestamp);
    }

    public function toArray(): array
    {
        return [
            'chat_id'         => $this->chat_id,
            'from_id'         => $this->from_id,
            'to_id'           => $this->to_id,
            'msg_id'          => $this->msg_id,
            'seqid'           => $this->seqid,
            'msg_type'        => $this->msg_type,
            'msg_body'        => $this->msg_body,
            'preview'         => $this->preview,
            'last_read_id'    => $this->last_read_id,
            'last_read_seqid' => $this->last_read_seqid,
            'unread_count'    => $this->unread_count,
            'timestamp'       => $this->timestamp,
        ];
    }

    public function jsonSerialize(): array
    {
        return $this->toArray();
    }
}
