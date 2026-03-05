<?php

namespace App\Repositories\Backend\Common;

use App\Constants\CommonValues;
use App\Constants\StatusCode;
use App\Core\Repositories\BaseRepository;
use App\Exception\BusinessException;
use App\Models\Common\AdvPosModel;
use App\Services\Admin\AdminLogService;

class AdvPosRepository extends BaseRepository
{
    /**
     * @param        $request
     * @return array
     */
    public static function getList($request)
    {
        $page     = self::getRequest($request, 'page', 'int', 1);
        $pageSize = self::getRequest($request, 'pageSize', 'int', 15);

        $query  = [];
        $filter = [];

        if ($request['name']) {
            $filter['name'] = self::getRequest($request, 'name');
            $query['name']  = ['$regex' => $filter['name'], '$options' => 'i'];
        }
        if ($request['code']) {
            $filter['code'] = self::getRequest($request, 'code');
            $query['code']  = $filter['code'];
        }

        $skip   = ($page - 1) * $pageSize;
        $fields = [];
        $count  = AdvPosModel::count($query);
        $items  = AdvPosModel::find($query, $fields, ['created_at' => -1], $skip, $pageSize);
        foreach ($items as $index => $item) {
            $item['created_at']  = date('Y-m-d H:i', $item['created_at']);
            $item['updated_at']  = date('Y-m-d H:i', $item['updated_at']);
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
     * @param                      $data
     * @return bool|int|mixed|null
     * @throws BusinessException
     */
    public static function save($data)
    {
        $code = self::getRequest($data, 'code');
        $name = self::getRequest($data, 'name');
        if (empty($code) || empty($name)) {
            throw  new BusinessException(StatusCode::PARAMETER_ERROR, '参数错误!');
        }
        $checkRow = AdvPosModel::findFirst(['code' => $data['code']]);
        if ($checkRow && $checkRow['_id'] != $data['_id']) {
            throw  new BusinessException(StatusCode::DATA_ERROR, '广告位标识不能重复!');
        }
        $row = [
            'name'        => $name,
            'code'        => $code,
            'is_disabled' => self::getRequest($data, 'is_disabled', 'int', 0),
            'width'       => self::getRequest($data, 'width', 'int', 0),
            'height'      => self::getRequest($data, 'height', 'int', 0),
        ];
        if ($data['_id'] > 0) {
            $row['_id'] = self::getRequest($data, '_id', 'int');
        }

        $result = AdvPosModel::save($row);
        AdminLogService::do(sprintf('保存广告位 名称%s,Code%s,编号%s', $name, $code, empty($row['_id']) ? $result : $row['_id']));
        return $result;
    }

    /**
     * @param                    $id
     * @return array|mixed
     * @throws BusinessException
     */
    public static function getDetail($id)
    {
        $row = AdvPosModel::findByID(intval($id));
        if (empty($row)) {
            throw  new BusinessException(StatusCode::DATA_ERROR, '数据不存在!');
        }
        return $row;
    }

    /**
     * @param           $id
     * @return bool|int
     */
    public static function delete($id)
    {
        AdminLogService::do(sprintf('删除广告位 编号%s', $id));
        return AdvPosModel::deleteById(intval($id));
    }
}
