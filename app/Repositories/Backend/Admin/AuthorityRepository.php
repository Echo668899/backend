<?php

namespace App\Repositories\Backend\Admin;

use App\Constants\CommonValues;
use App\Constants\StatusCode;
use App\Core\Repositories\BaseRepository;
use App\Exception\BusinessException;
use App\Models\Admin\AuthorityModel;

class AuthorityRepository extends BaseRepository
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
        if ($request['parent_id'] !== null && $request['parent_id'] !== '') {
            $filter['parent_id'] = self::getRequest($request, 'parent_id', 'int');
            $query['parent_id']  = $filter['parent_id'];
        }
        if ($request['is_menu'] !== null && $request['is_menu'] !== '') {
            $filter['is_menu'] = self::getRequest($request, 'is_menu', 'int');
            $query['is_menu']  = $filter['is_menu'];
        }
        if ($request['key']) {
            $filter['key'] = self::getRequest($request, 'key');
            $query['key']  = ['$regex' => $filter['key'], '$options' => 'i'];
        }

        $skip   = ($page - 1) * $pageSize;
        $fields = [];
        $count  = AuthorityModel::count($query);
        $items  = AuthorityModel::find($query, $fields, [$sort => $order], $skip, $pageSize);
        foreach ($items as $index => $item) {
            $item['created_at'] = date('Y-m-d H:i:s', $item['created_at']);
            $item['updated_at'] = date('Y-m-d H:i:s', $item['updated_at']);
            $item['is_menu']    = CommonValues::getIs($item['is_menu']);
            $items[$index]      = $item;
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
     * @param                                             $data
     * @return bool|int|mixed|\MongoDB\BSON\ObjectId|null
     * @throws BusinessException
     */
    public static function save($data)
    {
        if (empty($data['name']) || empty($data['key'])) {
            throw  new BusinessException(StatusCode::PARAMETER_ERROR);
        }

        $key       = self::getRequest($data, 'key');
        $checkItem = AuthorityModel::findFirst(['key' => $key]);
        if ($checkItem && $checkItem['_id'] != $data['_id']) {
            throw  new BusinessException(StatusCode::DATA_ERROR, '当前key已经存在!');
        }
        $row = [
            'name'       => self::getRequest($data, 'name'),
            'key'        => self::getRequest($data, 'key'),
            'parent_id'  => self::getRequest($data, 'parent_id', 'int', 0),
            'sort'       => self::getRequest($data, 'sort', 'int', 0),
            'class_name' => self::getRequest($data, 'class_name', 'string', ''),
            'is_menu'    => self::getRequest($data, 'is_menu', 'int', 0),
            'link'       => self::getRequest($data, 'link', 'string', ''),
        ];
        if (!empty($data['_id'])) {
            $row['_id'] = self::getRequest($data, '_id', 'int');
        }
        return AuthorityModel::save($row, true);
    }

    /**
     * 获取详情
     * @param        $id
     * @return mixed
     */
    public static function getDetail($id)
    {
        $row = AuthorityModel::findByID(intval($id));
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
        return AuthorityModel::deleteById(intval($id));
    }
}
