<?php

namespace App\Repositories\Api;

use App\Core\Repositories\BaseRepository;
use App\Services\Common\DanmakuService;

class DanmakuRepository extends BaseRepository
{
    /**
     * @param                                   $userId
     * @param                                   $objectId
     * @param                                   $objectType
     * @param                                   $pos
     * @param                                   $size
     * @param                                   $color
     * @param                                   $pool
     * @param                                   $content
     * @param                                   $subId
     * @return true
     * @throws \App\Exception\BusinessException
     */
    public static function do($userId, $objectId, $objectType, $pos, $size, $color, $pool, $content, $subId)
    {
        return DanmakuService::do($userId, $objectId, $objectType, $pos, $size, $color, $pool, $content, $subId);
    }

    /**
     * @param                                   $objectId
     * @param                                   $objectType
     * @param                                   $startPos
     * @param                                   $endPos
     * @param                                   $subId
     * @return array
     * @throws \App\Exception\BusinessException
     */
    public static function getList($objectId, $objectType, $startPos, $endPos, $subId)
    {
        return DanmakuService::getList($objectId, $objectType, $startPos, $endPos, $subId);
    }
}
