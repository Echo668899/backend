<?php

namespace App\Repositories\Backend\Admin;

use App\Constants\CommonValues;
use App\Constants\StatusCode;
use App\Core\Repositories\BaseRepository;
use App\Exception\BusinessException;
use App\Models\Admin\AdminRoleModel;

class AdminRoleRepository extends BaseRepository
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
        if (isset($request['is_disabled']) && $request['is_disabled'] !== '') {
            $filter['is_disabled'] = self::getRequest($request, 'is_disabled', 'int');
            $query['is_disabled']  = $filter['is_disabled'] * 1;
        }

        $skip   = ($page - 1) * $pageSize;
        $fields = [];

        $count = AdminRoleModel::count($query);
        $items = AdminRoleModel::find($query, $fields, [$sort => $order], $skip, $pageSize);
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
     * 保存数据
     * @param                 $data
     * @return bool|int|mixed
     */
    public static function save($data)
    {
        if (empty($data['name']) || empty($data['rights'])) {
            throw  new BusinessException(StatusCode::PARAMETER_ERROR, '参数错误!');
        }
        $rights = self::getRequest($data, 'rights');
        if (is_array($rights)) {
            $rights = array_values($rights);
        }
        $row = [
            'name'        => self::getRequest($data, 'name'),
            'rights'      => $rights,
            'is_disabled' => self::getRequest($data, 'is_disabled', 'int', 0),
            'description' => self::getRequest($data, 'description', 'string', ''),
        ];
        if ($data['_id'] > 0) {
            $row['_id'] = self::getRequest($data, '_id', 'int');
        }
        return AdminRoleModel::save($row, true);
    }

    /**
     * 获取详情
     * @param        $id
     * @return mixed
     */
    public static function getDetail($id)
    {
        $row = AdminRoleModel::findByID(intval($id));
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
        return AdminRoleModel::deleteById(intval($id));
    }
}
