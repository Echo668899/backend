<?php

declare(strict_types=1);

namespace App\Repositories\Backend\Ai;

use App\Constants\CommonValues;
use App\Constants\StatusCode;
use App\Core\Repositories\BaseRepository;
use App\Exception\BusinessException;
use App\Models\Ai\AiOrderModel;
use App\Models\User\UserModel;
use App\Services\Admin\AdminLogService;
use App\Services\User\UserService;
use App\Services\User\UserUpService;

class AiOrderRepository extends BaseRepository
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

        if ($request['order_sn']) {
            $filter['order_sn'] = self::getRequest($request, 'order_sn');
            $query['order_sn']  = ['$regex' => $filter['order_sn'], '$options' => 'i'];
        }
        if ($request['username']) {
            $filter['username'] = self::getRequest($request, 'username');
            $query['username']  = ['$regex' => $filter['username'], '$options' => 'i'];
        }
        if ($request['status'] !== '') {
            $filter['status'] = self::getRequest($request, 'status', 'int');
            $query['status']  = $filter['status'];
        }
        if ($request['order_type']) {
            $filter['order_type'] = self::getRequest($request, 'order_type');
            $query['order_type']  = $filter['order_type'];
        }

        $skip   = ($page - 1) * $pageSize;
        $fields = [];
        $count  = AiOrderModel::count($query);
        $items  = AiOrderModel::find($query, $fields, [$sort => $order], $skip, $pageSize);

        foreach ($items as $index => $item) {
            $user         = UserModel::findByID(intval($item['user_id']));
            $item['user'] = [
                '_id'         => $user['_id'],
                'username'    => $user['username'],
                'nickname'    => $user['nickname'],
                'headico'     => $user['headico'],
                'is_vip'      => UserService::isVip($item),
                'lang'        => $user['lang'],
                'sex'         => $user['sex'],
                'is_up'       => UserUpService::has($user['_id']),
                'is_disabled' => $user['is_disabled'],
            ];
            $item['user']['group_name'] = $item['is_vip'] ? $item['group_name'] : '';

            $item['created_at']  = date('Y-m-d H:i', $item['created_at']);
            $item['updated_at']  = date('Y-m-d H:i', $item['updated_at']);
            $item['status_text'] = CommonValues::getAiOrderStatus($item['status']);
            $item['order_type']  = CommonValues::getAiTplType($item['order_type']);
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
     * 获取详情
     * @param                    $id
     * @return array|mixed
     * @throws BusinessException
     */
    public static function getDetail($id)
    {
        $row = AiOrderModel::findByID($id);
        if (empty($row)) {
            throw new BusinessException(StatusCode::DATA_ERROR, '数据不存在!');
        }
        $row['extra']      = (array) $row['extra'];
        $row['created_at'] = date('Y-m-d H:i', $row['created_at']);
        $row['updated_at'] = date('Y-m-d H:i', $row['updated_at']);
        return $row;
    }

    /**
     * 删除
     * @param           $id
     * @return bool|int
     */
    public static function delete($id)
    {
        AdminLogService::do(sprintf('删除AI订单,ID:%s', $id));
        return AiOrderModel::delete(['_id' => (int) $id]);
    }
}
