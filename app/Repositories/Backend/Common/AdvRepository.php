<?php

namespace App\Repositories\Backend\Common;

use App\Constants\CommonValues;
use App\Constants\StatusCode;
use App\Core\Repositories\BaseRepository;
use App\Exception\BusinessException;
use App\Models\Common\AdvModel;
use App\Services\Admin\AdminLogService;
use App\Services\Common\AdvPosService;
use App\Services\Common\AdvService;

class AdvRepository extends BaseRepository
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

        if ($request['_id']) {
            $filter['_id'] = self::getRequest($request, '_id');
            $query['_id']  = $filter['_id'];
        }
        if ($request['name']) {
            $filter['name'] = self::getRequest($request, 'name');
            $query['name']  = ['$regex' => $filter['name'], '$options' => 'i'];
        }
        if ($request['position_code']) {
            $filter['position_code'] = self::getRequest($request, 'position_code');
            $query['position_code']  = $filter['position_code'];
        }
        if ($request['is_disabled'] !== '') {
            $filter['is_disabled'] = self::getRequest($request, 'is_disabled', 'int');
            $query['is_disabled']  = $filter['is_disabled'];
        }
        if ($request['type']) {
            $filter['type'] = self::getRequest($request, 'type');
            $query['type']  = $filter['type'];
        }
        $skip   = ($page - 1) * $pageSize;
        $fields = [];
        $count  = AdvModel::count($query);
        $items  = AdvModel::find($query, $fields, [$sort => $order], $skip, $pageSize);
        $posArr = AdvPosService::getAll();
        foreach ($items as $index => $item) {
            $item['created_at']    = date('Y-m-d H:i', $item['created_at']);
            $item['updated_at']    = date('Y-m-d H:i', $item['updated_at']);
            $item['start_time']    = date('Y-m-d H:i', $item['start_time']);
            $item['end_time']      = date('Y-m-d H:i', $item['end_time']);
            $item['position_name'] = strval($posArr[$item['position_code']]['name']);
            $item['is_disabled']   = CommonValues::getIs($item['is_disabled']);
            $items[$index]         = $item;
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
            'name'          => self::getRequest($data, 'name'),
            'description'   => self::getRequest($data, 'description', 'string', ''),
            'position_code' => self::getRequest($data, 'position_code'),
            'type'          => self::getRequest($data, 'type'),
            'right'         => self::getRequest($data, 'right'),
            'content'       => self::getRequest($data, 'content'),
            'start_time'    => self::getRequest($data, 'start_time'),
            'end_time'      => self::getRequest($data, 'end_time'),
            'link'          => self::getRequest($data, 'link', 'string', ''),
            'sort'          => self::getRequest($data, 'sort', 'int', 0),
            'click'         => self::getRequest($data, 'click', 'int', 0),
            'show_time'     => self::getRequest($data, 'show_time', 'int', 5),
            'is_disabled'   => self::getRequest($data, 'is_disabled', 'int', 0),
        ];
        if (empty($row['name']) || empty($row['position_code']) || empty($row['right'])) {
            throw  new BusinessException(StatusCode::PARAMETER_ERROR, '必填数据错误!');
        }
        if (empty($row['start_time']) || empty($row['end_time'])) {
            throw  new BusinessException(StatusCode::PARAMETER_ERROR, '开始时间或者结束时间错误!');
        }

        $row['start_time'] = strtotime($row['start_time']);
        $row['end_time']   = strtotime($row['end_time']);

        if ($data['_id'] !== '') {
            $row['_id'] = self::getRequest($data, '_id', 'string');
        }
        $result = AdvModel::save($row, false);
        AdminLogService::do(sprintf('操作广告,广告链接%s,广告位置%s,到期时间%s,广告编号%s', $row['link'], $row['position_code'], date('Y-m-d H:i:s', $row['end_time']), empty($row['_id']) ? $result : $row['_id']));
        AdvService::deleteCache();
        return $result;
    }

    /**
     * @param                    $id
     * @return array|mixed
     * @throws BusinessException
     */
    public static function getDetail($id)
    {
        $row = AdvModel::findByID(strval($id));
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
        AdminLogService::do(sprintf('删除广告,广告编号%s', $id));
        return AdvModel::deleteById(strval($id));
    }
}
