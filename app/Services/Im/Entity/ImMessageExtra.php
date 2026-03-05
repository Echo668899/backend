<?php

namespace App\Services\Im\Entity;

/**
 * 扩展,链路追踪等
 */
class ImMessageExtra implements \JsonSerializable
{
    public $server_time;
    public $ack_id;
    public $trace_id;

    public function __construct($serverTime = null, $ackId = null, $traceId = null)
    {
        $this->server_time = $serverTime;
        $this->ack_id      = $ackId;
        $this->trace_id    = $traceId;
    }

    public function jsonSerialize()
    {
        return array_filter([
            'server_time' => $this->server_time,
            'ack_id'      => $this->ack_id,
            'trace_id'    => $this->trace_id,
        ], function ($v) {
            return $v !== null;
        });
    }
}
