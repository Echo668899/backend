<?php

namespace App\Repositories\Backend\Activity;

use App\Constants\CommonValues;
use App\Constants\StatusCode;
use App\Core\Repositories\BaseRepository;
use App\Exception\BusinessException;
use App\Models\Activity\ActivityModel;

class ActivityRepository extends BaseRepository
{
    /**
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
        if ($request['right']) {
            $filter['right'] = self::getRequest($request, 'right');
            $query['right']  = $filter['right'];
        }
        if ($request['tpl_id']) {
            $filter['tpl_id'] = self::getRequest($request, 'tpl_id');
            $query['tpl_id']  = $filter['tpl_id'];
        }
        if (isset($request['is_disabled']) && $request['is_disabled'] !== '') {
            $filter['is_disabled'] = self::getRequest($request, 'is_disabled', 'int');
            $query['is_disabled']  = $filter['is_disabled'];
        }
        $skip   = ($page - 1) * $pageSize;
        $fields = [];
        $count  = ActivityModel::count($query);
        $items  = ActivityModel::find($query, $fields, [$sort => $order], $skip, $pageSize);
        foreach ($items as $index => $item) {
            $item['created_at']  = date('Y-m-d H:i', $item['created_at']);
            $item['updated_at']  = date('Y-m-d H:i', $item['updated_at']);
            $item['start_time']  = date('Y-m-d H:i', $item['start_time']);
            $item['end_time']    = date('Y-m-d H:i', $item['end_time']);
            $item['is_disabled'] = CommonValues::getIs($item['is_disabled']);
            $item['right']       = CommonValues::getActivityRight($item['right']);
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
     * @param                      $data
     * @return bool|int|mixed|null
     * @throws BusinessException
     */
    public static function save($data)
    {
        $row = [
            'name'        => self::getRequest($data, 'name'),
            'description' => self::getRequest($data, 'description', 'string', ''),
            'img_x'       => self::getRequest($data, 'img_x', 'string', ''),
            'start_time'  => self::getRequest($data, 'start_time'),
            'end_time'    => self::getRequest($data, 'end_time'),
            'tpl_id'      => self::getRequest($data, 'tpl_id'),
            'tpl_config'  => $data['tpl_config'],
            'right'       => self::getRequest($data, 'right', 'string'),
            'sort'        => self::getRequest($data, 'sort', 'int', 0),
            'is_disabled' => self::getRequest($data, 'is_disabled', 'int', 0),
        ];

        if (empty($row['name']) || empty($row['tpl_id']) || empty($row['tpl_config']) || !in_array($row['right'], ['all', 'normal', 'vip'])) {
            throw  new BusinessException(StatusCode::PARAMETER_ERROR, '必填数据错误!');
        }
        if (empty($row['start_time']) || empty($row['end_time'])) {
            throw  new BusinessException(StatusCode::PARAMETER_ERROR, '开始时间或者结束时间错误!');
        }

        $row['start_time'] = strtotime($row['start_time']);
        $row['end_time']   = strtotime($row['end_time']);

        if (!empty($data['_id'])) {
            $row['_id'] = self::getRequest($data, '_id', 'string');
        }
        $result = ActivityModel::save($row, false);
        return $result;
    }

    /**
     * @param                    $id
     * @return array|mixed
     * @throws BusinessException
     */
    public static function getDetail($id)
    {
        $row = ActivityModel::findByID($id);
        if (empty($row)) {
            throw  new BusinessException(StatusCode::DATA_ERROR, '数据不存在!');
        }
        $row['start_time'] = date('Y-m-d H:i:s', $row['start_time']);
        $row['end_time']   = date('Y-m-d H:i:s', $row['end_time']);
        return $row;
    }

    /**
     * 删除订单
     * @param           $id
     * @return bool|int
     */
    public static function delete($id)
    {
        return ActivityModel::update(['is_disabled' => 1], ['_id' => $id]);
    }
}
