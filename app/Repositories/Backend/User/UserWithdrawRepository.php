<?php

declare(strict_types=1);

namespace App\Repositories\Backend\User;

use App\Constants\CommonValues;
use App\Constants\StatusCode;
use App\Core\Repositories\BaseRepository;
use App\Exception\BusinessException;
use App\Models\User\UserModel;
use App\Models\User\UserWithdrawModel;
use App\Services\User\UserService;
use App\Services\User\UserUpService;
use App\Services\User\UserWithdrawService;

/**
 * 提现管理
 *
 * @package App\Repositories\Backend
 */
class UserWithdrawRepository extends BaseRepository
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

        $query  = [];
        $filter = [];

        if ($request['user_id']) {
            $filter['user_id'] = self::getRequest($request, 'user_id', 'int');
            $query['user_id']  = $filter['user_id'];
        }
        if ($request['status'] !== null && $request['status'] !== '') {
            $filter['status'] = self::getRequest($request, 'status', 'int');
            $query['status']  = $filter['status'];
        }
        if ($request['method']) {
            $filter['method'] = self::getRequest($request, 'method', 'string');
            $query['method']  = $filter['method'];
        }

        if ($request['start_time']) {
            $filter['start_time']        = self::getRequest($request, 'start_time');
            $query['created_at']['$gte'] = strtotime($filter['start_time']);
        }
        if ($request['end_time']) {
            $filter['end_time']          = self::getRequest($request, 'end_time');
            $query['created_at']['$lte'] = strtotime($filter['end_time']);
        }

        $skip   = ($page - 1) * $pageSize;
        $fields = [];
        $count  = UserWithdrawModel::count($query);
        $items  = UserWithdrawModel::find($query, $fields, ['_id' => -1], $skip, $pageSize);
        foreach ($items as $index => $item) {
            $userInfo            = UserModel::findByID($item['user_id']);
            $item['nickname']    = $userInfo ? $userInfo['nickname'] : '-';
            $item['is_up']       = UserUpService::has($item['user_id']);
            $item['is_disabled'] = $userInfo ? CommonValues::getIs($userInfo['is_disabled']) : '';

            $item['status_text']   = CommonValues::getWithdrawStatus($item['status']);
            $item['actual_amount'] = format_num(($item['num'] - $item['fee']), 2);
            $item['bank_name']     = empty($item['bank_name']) ? '' : $item['bank_name'];
            $item['num']           = format_num($item['num'], 2);
            $item['fee']           = format_num($item['fee'], 2);
            $item['method_text']   = CommonValues::getWithdrawMethod($item['method']);

            $item['created_at'] = date('Y-m-d H:i', $item['created_at']);
            $item['updated_at'] = date('Y-m-d H:i', $item['updated_at']);
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
     * @param                    $data
     * @return bool|int|mixed
     * @throws BusinessException
     */
    public static function save($data)
    {
        $row = [
            'status'    => self::getRequest($data, 'status', 'int'),
            'error_msg' => self::getRequest($data, 'error_msg', 'string', ''),
        ];
        if ($data['_id'] > 0) {
            $row['_id'] = self::getRequest($data, '_id', 'int');
        }
        $withdraw = UserWithdrawModel::findByID($data['_id']);
        if ($row['status'] == -1 && empty($row['error_msg'])) {
            throw  new BusinessException(StatusCode::PARAMETER_ERROR, '必须填写备注!');
        }

        $result = UserWithdrawService::reviewWithdraw($row['_id'], $row['status'], $row['error_msg']);
        if ($result) {
            if ($row['status'] == 1) {
                UserModel::updateRaw(['$set' => ['withdraw_info' => [
                    'method'       => $withdraw['method'],
                    'account'      => strval($withdraw['account']),
                    'account_name' => strval($withdraw['account_name']),
                    'bank_name'    => strval($withdraw['bank_name']),
                ]]], ['_id' => $withdraw['user_id']]);
            }
        }
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
        $row                  = UserWithdrawModel::findByID(intval($id));
        $userInfo             = UserService::getInfoFromCache(intval($row['user_id']));
        $row['username']      = $userInfo['username'] ?? '';
        $row['phone']         = $userInfo['phone'] ?? '';
        $row['actual_amount'] = format_num(($row['num'] - $row['fee']), 2);
        $row['num']           = format_num($row['num'], 2);
        $row['fee']           = format_num($row['fee'], 2);
        $row['method']        = CommonValues::getWithdrawMethod($row['method']);
        if (empty($row)) {
            throw  new BusinessException(StatusCode::DATA_ERROR, '数据不存在!');
        }

        return $row;
    }
}
