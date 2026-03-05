<?php

namespace App\Services\Report;

use App\Core\Services\BaseService;
use App\Models\Report\ReportAdvLogModel;

/**
 * 广告统计
 */
class ReportAdvLogService extends BaseService
{
    /**
     * @param       $advId
     * @param       $advName
     * @param       $field
     * @param       $channelName
     * @param       $value
     * @return void
     */
    public static function inc($advId, $advName, $field, $channelName, $value = 1)
    {
        self::do($advId, $advName, $field, '_all', $value);

        if (!empty($channelName)) {
            self::do($advId, $advName, $field, $channelName, $value);
        }
    }

    /**
     * @param       $advId
     * @param       $advName
     * @param       $field
     * @param       $parentId
     * @param       $value
     * @return void
     */
    public static function incUser($advId, $advName, $field, $parentId, $value = 1)
    {
        self::do($advId, $advName, $field, '_all_user', $value);

        if (!empty($parentId)) {
            self::do($advId, $advName, $field, $parentId . '_user', $value);
        }
    }

    /**
     * 获取字段统计//渠道侧
     * @param      $date
     * @param      $field
     * @param      $channelName
     * @return int
     */
    public static function getFieldCount($date, $field, $channelName)
    {
        $count = ReportAdvLogModel::aggregate([
            [
                '$match' => [
                    'label'        => $date,
                    'channel_name' => $channelName
                ]
            ],
            [
                '$group' => [
                    '_id'   => null,
                    'count' => ['$sum' => '$' . $field]
                ]
            ]
        ]);
        return intval($count['count'] ?? 0);
    }

    /**
     * 获取字段统计//用户侧
     * @param      $date
     * @param      $field
     * @param      $parentId
     * @return int
     */
    public static function getFieldUserCount($date, $field, $parentId)
    {
        $count = ReportAdvLogModel::aggregate([
            [
                '$match' => [
                    'label'        => $date,
                    'channel_name' => $parentId . '_user'
                ]
            ],
            [
                '$group' => [
                    '_id'   => null,
                    'count' => ['$sum' => '$' . $field]
                ]
            ]
        ]);
        return intval($count['count'] ?? 0);
    }

    /**
     * @param            $advId
     * @param            $advName
     * @param            $field
     * @param            $channelName
     * @param            $value
     * @return true|void
     */
    private static function do($advId, $advName, $field, $channelName, $value = 1)
    {
        $advId = strval($advId);
        if (!in_array($field, ['click'])) {
            return;
        }
        $label   = date('Y-m-d');
        $idValue = md5($label . '_' . $channelName . '_' . $advId);

        ReportAdvLogModel::findAndModify([
            '_id' => $idValue,
        ], [
            '$set' => [
                'name'       => $advName,
                'updated_at' => time(),
            ],
            '$inc' => [
                $field => $value
            ],
            '$setOnInsert' => [
                '_id'          => $idValue,
                'adv_id'       => $advId,
                'label'        => $label,
                'channel_name' => $channelName,
                'created_at'   => time(),
            ]
        ], [], true);
        return true;
    }
}
