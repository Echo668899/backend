<?php

declare(strict_types=1);

namespace App\Repositories\Backend\User;

use App\Constants\CommonValues;
use App\Constants\StatusCode;
use App\Core\Repositories\BaseRepository;
use App\Exception\BusinessException;
use App\Models\User\UserProductModel;

/**
 * 金币套餐
 * @package App\Repositories\Backend
 */
class UserProductRepository extends BaseRepository
{
    /**
     * 获取列表
     * @param        $request
     * @return array
     */
    public static function getList($request)
    {
        $page     = self::getRequest($request, 'page', 'int', 1);
        $pageSize = self::getRequest($request, 'pageSize', 'int', 15);
        $sort     = self::getRequest($request, 'sort', 'string', '_id');
        $order    = self::getRequest($request, 'order', 'int', -1);
        $query    = [];
        $filter   = [];

        if ($request['name']) {
            $filter['name'] = self::getRequest($request, 'name');
            $query['name']  = ['$regex' => $filter['name'], '$options' => 'i'];
        }
        if ($request['type']) {
            $filter['type'] = self::getRequest($request, 'type');
            $query['type']  = $filter['type'];
        }
        if ($request['is_disabled'] !== null) {
            $filter['is_disabled'] = self::getRequest($request, 'is_disabled', 'int');
            $query['is_disabled']  = $filter['is_disabled'];
        }

        $skip   = ($page - 1) * $pageSize;
        $fields = [];
        $count  = UserProductModel::count($query);
        $items  = UserProductModel::find($query, $fields, [$sort => $order], $skip, $pageSize);
        foreach ($items as $index => $item) {
            $item['created_at']  = date('Y-m-d H:i', $item['created_at']);
            $item['updated_at']  = date('Y-m-d H:i', $item['updated_at']);
            $item['type']        = CommonValues::getUserProductType($item['type']);
            $item['is_disabled'] = CommonValues::getIs($item['is_disabled']);
            $items[$index]       = $item;
        }

        return [
            'filter'   => $filter,
            'items'    => empty($items) ? [] : array_values($items),
            'count'    => $count,
            'page'     => $page,
            'pageSize' => $pageSize
        ];
    }

    /**
     * 保存数据
     * @param                    $data
     * @return bool|int|mixed
     * @throws BusinessException
     */
    public static function save($data)
    {
        $row = [
            'name'        => self::getRequest($data, 'name'),
            'type'        => self::getRequest($data, 'type'),
            'num'         => self::getRequest($data, 'num', 'int', 0),
            'gift_num'    => self::getRequest($data, 'gift_num', 'int', 0),
            'vip_num'     => self::getRequest($data, 'vip_num', 'int', 0),
            'price'       => self::getRequest($data, 'price', 'double', 0),
            'sort'        => self::getRequest($data, 'sort', 'int', 0),
            'price_tips'  => self::getRequest($data, 'price_tips', 'string', ''),
            'description' => self::getRequest($data, 'description', 'string', ''),
            'is_disabled' => self::getRequest($data, 'is_disabled', 'int', 0),
            'tips'        => self::getRequest($data, 'tips', 'string', ''),
        ];
        if (empty($row['name']) || empty($row['type']) || empty($row['num']) || empty($row['price'])) {
            throw  new BusinessException(StatusCode::PARAMETER_ERROR, '参数错误!');
        }

        if ($data['_id'] > 0) {
            $row['_id'] = self::getRequest($data, '_id', 'int');
        }
        $result = UserProductModel::save($row);
        return $result;
    }

    /**
     * 获取详情
     * @param                    $id
     * @return mixed
     * @throws BusinessException
     */
    public static function getDetail($id)
    {
        $row = UserProductModel::findByID(intval($id));
        if (empty($row)) {
            throw  new BusinessException(StatusCode::DATA_ERROR, '数据不存在!');
        }
        return $row;
    }

    /**
     * 删除
     * @param        $id
     * @return mixed
     */
    public static function delete($id)
    {
        return UserProductModel::deleteById(intval($id));
    }
}
