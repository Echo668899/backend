<?php

namespace App\Repositories\Backend\Common;

use App\Constants\CommonValues;
use App\Constants\StatusCode;
use App\Core\Repositories\BaseRepository;
use App\Exception\BusinessException;
use App\Models\Common\ChannelApkModel;
use App\Services\Common\ChannelApkService;

/**
 * 渠道包
 */
class ChannelApkRepository extends BaseRepository
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
        if ($request['code'] !== null && $request['code'] !== '') {
            $filter['code'] = self::getRequest($request, 'code', 'string');
            $query['code']  = ['$regex' => $filter['code'], '$options' => 'i'];
        }
        if ($request['line_code'] !== null && $request['line_code'] !== '') {
            $filter['line_code'] = self::getRequest($request, 'line_code', 'string');
            $query['line_code']  = $filter['line_code'];
        }
        if ($request['is_auto'] !== null && $request['is_auto'] !== '') {
            $filter['is_auto'] = self::getRequest($request, 'is_auto', 'int');
            $query['is_auto']  = $filter['is_auto'];
        }
        if ($request['is_disabled'] !== null && $request['is_disabled'] !== '') {
            $filter['is_disabled'] = self::getRequest($request, 'is_disabled', 'int');
            $query['is_disabled']  = $filter['is_disabled'];
        }

        $skip   = ($page - 1) * $pageSize;
        $fields = [];
        $count  = ChannelApkModel::count($query);
        $items  = ChannelApkModel::find($query, $fields, [$sort => $order], $skip, $pageSize);
        foreach ($items as $index => $item) {
            $item['created_at']   = date('Y-m-d H:i', $item['created_at']);
            $item['updated_at']   = date('Y-m-d H:i', $item['updated_at']);
            $item['android_link'] = $item['android_link'] ?: '-';
            $item['ios_link']     = $item['ios_link'] ?: '-';
            $item['dual_link']    = $item['dual_link'] ?: '-';
            $item['is_disabled']  = CommonValues::getIs($item['is_disabled']);
            $item['is_auto']      = CommonValues::getIs($item['is_auto']);
            $items[$index]        = $item;
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
     * 获取详情
     * @param                    $id
     * @return mixed
     * @throws BusinessException
     */
    public static function getDetail($id)
    {
        $result = ChannelApkModel::findByID(intval($id));
        if (empty($result)) {
            throw  new BusinessException(StatusCode::DATA_ERROR, '数据不存在!');
        }
        return $result;
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
            'code'         => self::getRequest($data, 'code', 'string'),
            'line_code'    => self::getRequest($data, 'line_code', 'string'),
            'dual_link'    => self::getRequest($data, 'dual_link', 'string'),
            'android_link' => self::getRequest($data, 'android_link', 'string'),
            'ios_link'     => self::getRequest($data, 'ios_link', 'string'),
            'is_auto'      => self::getRequest($data, 'is_auto', 'int', 0),
            'is_disabled'  => self::getRequest($data, 'is_disabled', 'int', 0),
        ];
        if (empty($row['name']) || empty($data['code'])) {
            throw  new BusinessException(StatusCode::PARAMETER_ERROR, '参数错误!');
        }
        if (empty($row['dual_link']) && empty($row['android_link']) && empty($row['ios_link'])) {
            throw  new BusinessException(StatusCode::PARAMETER_ERROR, '至少有一个线路不能为空!');
        }
        if ($data['_id'] > 0) {
            $row['_id'] = self::getRequest($data, '_id', 'int');
        }

        $hasRow = ChannelApkModel::findFirst(['code' => $row['code']]);
        if ($hasRow && $hasRow['_id'] != $row['_id']) {
            throw  new BusinessException(StatusCode::PARAMETER_ERROR, '标识已存在!');
        }

        $result = ChannelApkModel::save($row);
        ChannelApkService::deleteCache();
        return $result;
    }

    /**
     * 删除
     * @param        $id
     * @return mixed
     */
    public static function delete($id)
    {
        return ChannelApkModel::deleteByID(intval($id));
    }
}
