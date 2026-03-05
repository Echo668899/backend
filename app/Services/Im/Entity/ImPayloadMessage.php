<?php

namespace App\Services\Im\Entity;

/**
 * 消息封装实体
 */
class ImPayloadMessage implements \JsonSerializable
{
    protected $type;
    protected $data;
    protected $extra;

    public function __construct($type, ImMessageData $data, ImMessageExtra $extra = null)
    {
        $this->type  = $type;
        $this->data  = $data;
        $this->extra = $extra;
    }

    public function jsonSerialize()
    {
        return array_filter([
            'type'  => $this->type,
            'data'  => $this->data,
            'extra' => $this->extra,
        ], function ($v) {
            return $v !== null;
        });
    }
}
