<?php

namespace App\Services\Common;

use App\Core\Services\BaseService;
use App\Models\Common\ChannelModel;

/**
 * 渠道
 */
class ChannelService extends BaseService
{
    /**
     * 获取所有渠道
     * @return array
     */
    public static function getAll()
    {
        $result    = [];
        $query     = ['is_disabled' => 0, 'last_bind' => ['$gte' => strtotime('-1day')]];
        $count     = ChannelModel::count($query);
        $pageSize  = 1000;
        $totalPage = ceil($count / $pageSize);
        for ($page = 1; $page <= $totalPage; $page++) {
            $skip  = ($page - 1) * $pageSize;
            $items = ChannelModel::find($query, ['code'], [], $skip, $pageSize);
            foreach ($items as $item) {
                $result[] = $item['code'];
            }
        }
        return $result;
    }

    /**
     * 绑定渠道
     * @param $channelName
     */
    public static function bindChannel($channelName)
    {
        if (strlen($channelName) > 15) {
            return;
        }
        $channelId = md5($channelName);
        $result    = ChannelModel::findByID($channelId);
        if ($result) {
            ChannelModel::updateById(['last_bind' => time()], $channelId);
        } else {
            ChannelModel::insert([
                '_id'         => $channelId,
                'code'        => $channelName,
                'name'        => $channelName,
                'is_disabled' => 0,
                'last_bind'   => time()
            ]);
        }
    }
}
