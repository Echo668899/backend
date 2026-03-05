<?php

declare(strict_types=1);

namespace App\Repositories\Backend\User;

use App\Constants\CommonValues;
use App\Constants\StatusCode;
use App\Core\Repositories\BaseRepository;
use App\Exception\BusinessException;
use App\Models\User\UserCodeModel;
use App\Services\User\UserCodeService;
use App\Services\User\UserGroupService;
use App\Services\User\UserProductService;

/**
 * 兑换码管理
 * @package App\Repositories\Backend
 */
class UserCodeRepository extends BaseRepository
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
        if (isset($request['status']) && $request['status'] !== '') {
            $filter['status'] = self::getRequest($request, 'status', 'int');
            $query['status']  = $filter['status'];
        }
        if ($request['code_key']) {
            $filter['code_key'] = self::getRequest($request, 'code_key');
            $query['code_key']  = $filter['code_key'];
        }
        if ($request['code']) {
            $filter['code'] = self::getRequest($request, 'code');
            $query['code']  = $filter['code'];
        }
        if ($request['type']) {
            $filter['type'] = self::getRequest($request, 'type', 'string');
            $query['type']  = $filter['type'];
        }

        $userGroups    = UserGroupService::getAll();
        $productGroups = UserProductService::getAll();
        $skip          = ($page - 1) * $pageSize;
        $fields        = [];
        $count         = UserCodeModel::count($query);
        $items         = UserCodeModel::find($query, $fields, [$sort => $order], $skip, $pageSize);
        foreach ($items as $index => $item) {
            $item['expired_at']  = date('Y-m-d H:i', $item['expired_at']);
            $item['created_at']  = date('Y-m-d H:i', $item['created_at']);
            $item['updated_at']  = date('Y-m-d H:i', $item['updated_at']);
            $item['status']      = CommonValues::getUserCodeStatus($item['status']);
            $item['type']        = CommonValues::getUserCodeType($item['type']);
            $item['object_name'] = $item['type'] == 'point' ? $productGroups[$item['object_id']]['name'] : $userGroups[$item['object_id']]['name'];
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
            'type'        => self::getRequest($data, 'type', 'string', ''),
            'object_id'   => self::getRequest($data, 'vip_id', 'int', 0),
            'num'         => self::getRequest($data, 'num', 'int', 1),
            'can_use_num' => self::getRequest($data, 'can_use_num', 'int', 1),
            'add_num'     => self::getRequest($data, 'add_num', 'int', 1),
            'expired_at'  => self::getRequest($data, 'expired_at'),
            'status'      => 0,
        ];
        if ($row['type'] == 'point') {
            $row['object_id'] = self::getRequest($data, 'coin_id', 'int', 0);
        }
        if (empty($row['name']) || empty($row['num']) || empty($row['object_id']) || empty($row['can_use_num'])
            || empty($row['add_num']) || empty($row['expired_at'])) {
            throw  new BusinessException(StatusCode::PARAMETER_ERROR, '参数错误!');
        }

        if ($row['num'] > 10000) {
            throw  new BusinessException(StatusCode::PARAMETER_ERROR, '请勿一次生成1万条以上!');
        }

        if ($data['_id'] > 0) {
            throw  new BusinessException(StatusCode::PARAMETER_ERROR, '兑换码不能修改,如需修改只能联系技术!');
        }
        return UserCodeService::save($row);
    }

    /**
     * 更新数据
     * @param                 $data
     * @return bool|int|mixed
     */
    public static function update($data)
    {
        return UserCodeModel::updateById($data, $data['_id']);
    }

    /**
     * @param $id
     */
    public static function delete($id)
    {
        return UserCodeModel::deleteById(intval($id));
    }
}
