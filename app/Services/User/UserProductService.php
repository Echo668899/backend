<?php

declare(strict_types=1);

namespace App\Services\User;

use App\Constants\CacheKey;
use App\Core\Services\BaseService;
use App\Models\User\UserProductModel;

/**
 *  金币套餐
 * @package App\Services
 */
class UserProductService extends BaseService
{
    /**
     * 获取所有可用产品
     * @param  string $type
     * @return array
     */
    public static function getEnableAll($type = 'point')
    {
        $result = self::getAll($type);
        foreach ($result as $index => $item) {
            if ($item['is_disabled'] == 'y') {
                unset($result[$index]);
            }
        }
        return array_values($result);
    }

    /**
     * 获取所有
     * @param  mixed $type
     * @return array
     */
    public static function getAll($type = '')
    {
        $result = cache()->get(CacheKey::USER_PRODUCT);
        if ($result == null) {
            $result = UserProductModel::find([], [], ['sort' => -1], 0, 1000);
            cache()->set(CacheKey::USER_PRODUCT, $result, 180);
        }

        $rows = [];
        foreach ($result as $item) {
            if (empty($type) || $type == $item['type']) {
                $rows[$item['_id']] = [
                    'id'          => strval($item['_id']),
                    'name'        => strval($item['name']),
                    'type'        => strval($item['type']),
                    'num'         => strval($item['num'] * 1),
                    'gift_num'    => strval($item['gift_num'] * 1),
                    'vip_num'     => strval($item['vip_num'] * 1),
                    'description' => strval($item['description']),
                    'price_tips'  => strval($item['price_tips']),
                    'price'       => strval($item['price'] * 1),
                    'tips'        => strval($item['tips']),
                    'is_disabled' => $item['is_disabled'] ? 'y' : 'n'
                ];
            }
        }
        return $rows;
    }

    /**
     * 获取产品组信息
     * @param             $productId
     * @return mixed|null
     */
    public static function getInfo($productId)
    {
        $result = self::getAll();
        return $result[$productId] ?? null;
    }
}
