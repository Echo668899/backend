<?php

namespace App\Services\Common;

use App\Constants\CacheKey;
use App\Core\Services\BaseService;
use App\Models\Common\ChannelApkModel;

/**
 * 渠道包
 */
class ChannelApkService extends BaseService
{
    /**
     * 删除缓存
     * @return void
     * @throws \Phalcon\Storage\Exception
     */
    public static function deleteCache()
    {
        cache()->delete(CacheKey::CHANNEL_APK);
    }

    /**
     * @return array
     * @throws \Phalcon\Storage\Exception
     */
    public static function getAll()
    {
        $keyName = CacheKey::CHANNEL_APK;
        $result  = cache()->get($keyName);
        if (is_null($result)) {
            $query  = ['is_disabled' => 0];
            $result = ChannelApkModel::find($query, ['_id', 'code', 'line_code', 'dual_link', 'android_link', 'ios_link'], [], 0, 2000);
            cache()->set($keyName, $result, 300);
        }

        $rows = [];
        foreach ($result as $item) {
            $rows[] = [
                'code'         => strval($item['code']),
                'line_code'    => strval($item['line_code']),
                'dual_link'    => strval($item['dual_link']),
                'android_link' => strval($item['android_link']),
                'ios_link'     => strval($item['ios_link']),
                'is_auto'      => strval($item['is_auto'] ? 'y' : 'n'),
            ];
        }
        return $rows;
    }

    /**
     * @param                             $deviceType
     * @param                             $lineCode
     * @return mixed|string
     * @throws \Phalcon\Storage\Exception
     */
    public static function getApk($deviceType, $lineCode = '')
    {
        if ($deviceType == 'ios') {
            return '';
        }
        $rows = self::getAll();
        if (empty($rows)) {
            return '';
        }

        foreach ($rows as $index => $row) {
            if (empty($lineCode)) {
                continue;
            }
            if ($row['line_code'] != $lineCode) {
                unset($rows[$index]);
            }
        }
        $rows = array_values($rows);
        if (empty($rows)) {
            return '';
        }

        $row = $rows[array_rand($rows)];
        return $row['dual_link'] ?: $row['android_link'];
    }
}
