<?php

declare(strict_types=1);

namespace App\Repositories\Backend\User;

use App\Constants\CommonValues;
use App\Constants\StatusCode;
use App\Core\Repositories\BaseRepository;
use App\Exception\BusinessException;
use App\Models\Activity\ActivityModel;
use App\Models\User\UserGroupModel;

/**
 * 用户组管理
 * @package App\Repositories\Backend
 */
class UserGroupRepository extends BaseRepository
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

        $skip   = ($page - 1) * $pageSize;
        $fields = [];
        $count  = UserGroupModel::count($query);
        $items  = UserGroupModel::find($query, $fields, [$sort => $order], $skip, $pageSize);
        foreach ($items as $index => $item) {
            $item['created_at']  = date('Y-m-d H:i', $item['created_at']);
            $item['updated_at']  = date('Y-m-d H:i', $item['updated_at']);
            $item['activity_id'] = value(function () use ($item) {
                if (empty($item['activity_id'])) {
                }
                $activityRow = ActivityModel::findByID($item['activity_id']);
                return $activityRow ? $activityRow['name'] : '-';
            });
            $item['is_disabled'] = CommonValues::getIs($item['is_disabled']);
            $item['group']       = $item['group'] . ' | ' . CommonValues::getUserGroupType($item['group']);

            $items[$index] = $item;
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
            'name'         => self::getRequest($data, 'name', 'string'),
            'description'  => self::getRequest($data, 'description', 'string'),
            'is_disabled'  => self::getRequest($data, 'is_disabled', 'int', 0),
            'sort'         => self::getRequest($data, 'sort', 'int', 0),
            'img'          => self::getRequest($data, 'img', 'string', ''),
            'icon'         => self::getRequest($data, 'icon', 'string', ''),
            'group'        => self::getRequest($data, 'group', 'string', ''),
            'rate'         => self::getRequest($data, 'rate', 'int', 100),
            'coupon_num'   => self::getRequest($data, 'coupon_num', 'int', 0),
            'price'        => self::getRequest($data, 'price', 'double', 0),
            'old_price'    => self::getRequest($data, 'old_price', 'double', 0),
            'day_num'      => self::getRequest($data, 'day_num', 'int', 0),
            'gift_num'     => self::getRequest($data, 'gift_num', 'int', 0),
            'download_num' => self::getRequest($data, 'download_num', 'int', 0),
            'day_tips'     => self::getRequest($data, 'day_tips', 'string', ''),
            'price_tips'   => self::getRequest($data, 'price_tips', 'string', ''),
            'activity_id'  => self::getRequest($data, 'activity_id', 'string', ''),
            'tips'         => self::getRequest($data, 'tips', 'string', ''),
            'right'        => $data['right']
        ];
        if ($row['rate'] < 0 || $row['rate'] > 100) {
            throw  new BusinessException(StatusCode::PARAMETER_ERROR, '折扣范围取值错误!');
        }
        if (empty($row['name']) || empty($row['day_num'])) {
            throw  new BusinessException(StatusCode::PARAMETER_ERROR, '组名或者天数配置错误!');
        }
        if ($row['day_num'] > 3650) {
            throw  new BusinessException(StatusCode::PARAMETER_ERROR, '最大天数不能超过3650!');
        }
        if ($data['_id'] > 0) {
            $row['_id'] = self::getRequest($data, '_id', 'int');
        }
        return UserGroupModel::save($row);
    }

    /**
     * 获取详情
     * @param                    $id
     * @return mixed
     * @throws BusinessException
     */
    public static function getDetail($id)
    {
        $row = UserGroupModel::findByID(intval($id));
        if (empty($row)) {
            throw  new BusinessException(StatusCode::PARAMETER_ERROR, '数据不存在!');
        }
        $row['right'] = array_merge($row['right']['show'] ?? [], $row['right']['logic'] ?? []);
        return $row;
    }

    /**
     * 删除订单
     * @param        $id
     * @return mixed
     */
    public static function delete($id)
    {
        return UserGroupModel::deleteById(intval($id));
    }
}
