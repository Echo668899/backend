<?php

namespace App\Repositories\Backend\User;

use App\Constants\CommonValues;
use App\Constants\StatusCode;
use App\Core\Repositories\BaseRepository;
use App\Exception\BusinessException;
use App\Models\User\UserModel;
use App\Models\User\UserUpModel;
use App\Services\Admin\AdminUserService;
use App\Services\User\AccountService;
use App\Services\User\UserGroupService;
use App\Services\User\UserService;
use App\Services\User\UserUpService;
use App\Utils\CommonUtil;
use App\Utils\LogUtil;

/**
 * Class UserRepository
 * @package App\Repositories\Backend
 */
class UserRepository extends BaseRepository
{
    /**
     * 获取列表
     * @param        $request
     * @return array
     */
    public static function getList($request = [])
    {
        $page     = self::getRequest($request, 'page', 'int', 1);
        $pageSize = self::getRequest($request, 'pageSize', 'int', 30);
        $sort     = self::getRequest($request, 'sort', 'string', '_id');
        $order    = self::getRequest($request, 'order', 'int', -1);

        $query  = [];
        $filter = [];

        if ($request['user_id']) {
            $filter['user_id'] = self::getRequest($request, 'user_id', 'int');
            $query['_id']      = $filter['user_id'];
        }
        if ($request['parent_id']) {
            $filter['parent_id'] = self::getRequest($request, 'parent_id', 'int');
            $query['parent_id']  = $filter['parent_id'];
        }
        if ($request['username']) {
            $filter['username'] = self::getRequest($request, 'username');
            $query['username']  = $filter['username'];
        }
        if ($request['nickname']) {
            $filter['nickname'] = self::getRequest($request, 'nickname');
            $query['nickname']  = ['$regex' => $filter['nickname'], '$options' => 'i'];
        }
        if ($request['phone']) {
            $filter['phone'] = self::getRequest($request, 'phone');
            $query['phone']  = $filter['phone'];
        }
        if (isset($request['is_disabled']) && $request['is_disabled'] !== '') {
            $filter['is_disabled'] = self::getRequest($request, 'is_disabled', 'int');
            $query['is_disabled']  = $filter['is_disabled'];
        }
        if ($request['device_type']) {
            $filter['device_type'] = self::getRequest($request, 'device_type', 'string');
            $query['device_type']  = $filter['device_type'];
        }
        if ($request['group_id']) {
            $filter['group_id'] = self::getRequest($request, 'group_id', 'int');
            $query['group_id']  = $filter['group_id'];
        }
        if ($request['group_dark_id']) {
            $filter['group_dark_id'] = self::getRequest($request, 'group_dark_id', 'int');
            $query['group_dark_id']  = $filter['group_dark_id'];
        }
        if ($request['is_up']) {
            $upIds = UserUpService::getAll();
            if (!empty($query['_id'])) {
                if (in_array($query['_id'], $upIds)) {
                    $upIds = [$query['_id']];
                } else {
                    $upIds = [];
                }
            }
            $query['_id'] = ['$in' => $upIds];
        }
        if ($request['channel_name']) {
            $filter['channel_name'] = self::getRequest($request, 'channel_name', 'string');
            $query['channel_name']  = $filter['channel_name'];
        }
        if ($request['start_time']) {
            $filter['start_time']         = self::getRequest($request, 'start_time');
            $query['register_at']['$gte'] = strtotime($filter['start_time']);
        }
        if ($request['end_time']) {
            $filter['end_time']           = self::getRequest($request, 'end_time');
            $query['register_at']['$lte'] = strtotime($filter['end_time']);
        }
        if ($request['register_ip'] && empty($query['register_at'])) {
            $query['register_at']['$gte'] = strtotime('-10 days');
        }

        $skip   = ($page - 1) * $pageSize;
        $fields = [];
        $count  = UserModel::count($query);
        $items  = UserModel::find($query, $fields, [$sort => $order], $skip, $pageSize);
        foreach ($items as $index => $item) {
            $isVip               = UserService::isVip($item);
            $isDarkVip           = UserService::isVip($item, true);
            $item['first_pay']   = $item['first_pay'] ? date('y-m-d H:i:s', $item['first_pay']) : '-';
            $item['last_pay']    = $item['last_pay'] ? date('y-m-d H:i:s', $item['last_pay']) : '-';
            $item['login_at']    = date('y-m-d H:i:s', $item['login_at']);
            $item['updated_at']  = date('y-m-d H:i:s', $item['updated_at']);
            $item['register_at'] = date('y-m-d H:i:s', $item['register_at']);

            $item['group_name']       = $isVip ? $item['group_name'] : '-';
            $item['group_start_time'] = $isVip && $item['group_start_time'] ? date('Y-m-d H:i', $item['group_start_time']) : '-';
            $item['group_end_time']   = $isVip && $item['group_end_time'] ? date('Y-m-d H:i', $item['group_end_time']) : '-';

            $item['group_dark_name']       = $isDarkVip ? $item['group_dark_name'] : '-';
            $item['group_dark_start_time'] = $isDarkVip && $item['group_dark_start_time'] ? date('Y-m-d H:i', $item['group_start_time']) : '-';
            $item['group_dark_end_time']   = $isDarkVip && $item['group_dark_end_time'] ? date('Y-m-d H:i', $item['group_end_time']) : '-';

            $item['parent_id']    = $item['parent_id'] ?: '-';
            $item['is_disabled']  = CommonValues::getIs($item['is_disabled']);
            $item['sex']          = CommonValues::getUserSex($item['sex']);
            $item['is_up']        = UserUpService::has($item['_id']);
            $item['channel_name'] = $item['channel_name'] ?: '-';
            $item['phone']        = CommonUtil::filterPhone(CommonUtil::formatPhone($item['phone']));
            unset($item['sign']);
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
     * @param        $userId1
     * @param        $userId2
     * @return array
     */
    public static function getAccList($userId1, $userId2)
    {
        $query['_id'] = ['$in' => [intval($userId1), intval($userId2)]];
        $filter       = [];
        $fields       = [];
        $count        = UserModel::count($query);
        $items        = UserModel::find($query, $fields, [], 0, 10);
        foreach ($items as $index => $item) {
            $isVip                    = UserService::isVip($item);
            $isDarkVip                = UserService::isVip($item, true);
            $item['updated_at']       = date('m-d H:i', $item['updated_at']);
            $item['register_at']      = date('m-d H:i:s', $item['register_at']);
            $item['last_at']          = date('Y-m-d H:i:s', $item['last_at']);
            $item['group_name']       = $isVip ? $item['group_name'] : '-';
            $item['group_start_time'] = $isVip ? date('Y-m-d H:i', $item['group_start_time']) : '-';
            $item['group_end_time']   = $isVip ? date('Y-m-d H:i', $item['group_end_time']) : '-';

            $item['group_dark_name']       = $isDarkVip ? $item['group_dark_name'] : '-';
            $item['group_dark_start_time'] = $isDarkVip && $item['group_dark_start_time'] ? date('Y-m-d H:i', $item['group_start_time']) : '-';
            $item['group_dark_end_time']   = $isDarkVip && $item['group_dark_end_time'] ? date('Y-m-d H:i', $item['group_end_time']) : '-';

            $items[$index] = $item;
        }

        return [
            'filter'   => $filter,
            'items'    => empty($items) ? [] : array_values($items),
            'count'    => $count,
            'page'     => 1,
            'pageSize' => 10
        ];
    }

    /**
     * @param                    $userId
     * @return mixed
     * @throws BusinessException
     */
    public static function getDetail($userId)
    {
        $userId = intval($userId);
        $row    = UserModel::findByID($userId);
        if (empty($row)) {
            throw  new BusinessException(StatusCode::DATA_ERROR, '数据不存在!');
        }
        $userUp = UserUpModel::findByID($userId);

        $row['up_category']       = $userUp['categories'] ?: '';
        $row['movie_fee_rate']    = $userUp['movie_fee_rate'] ?: '0';
        $row['post_fee_rate']     = $userUp['post_fee_rate'] ?: '0';
        $row['movie_money_limit'] = $userUp['movie_money_limit'] ?: '100';
        $row['movie_upload_num']  = $userUp['movie_upload_num'] ?: '10';
        $row['post_upload_num']   = $userUp['post_upload_num'] ?: '10';

        $row['first_pay']        = $row['first_pay'] ? date('Y-m-d H:i:s', $row['first_pay']) : '-';
        $row['last_pay']         = $row['last_pay'] ? date('Y-m-d H:i:s', $row['last_pay']) : '-';
        $row['group_start_time'] = $row['group_start_time'] ? date('Y-m-d H:i:s', $row['group_start_time']) : '';
        $row['group_end_time']   = $row['group_end_time'] ? date('Y-m-d H:i:s', $row['group_end_time']) : '';

        $row['group_dark_start_time'] = $row['group_dark_start_time'] ? date('Y-m-d H:i:s', $row['group_dark_start_time']) : '';
        $row['group_dark_end_time']   = $row['group_dark_end_time'] ? date('Y-m-d H:i:s', $row['group_dark_end_time']) : '';

        $row['right'] = $row['group_rate'] == 0 ? 'money' : '';
        return $row;
    }

    /**
     * 编辑用户
     * @param                    $data
     * @return bool|int|mixed
     * @throws BusinessException
     */
    public static function save($data)
    {
        $row = [
            '_id'           => self::getRequest($data, '_id', 'int'),
            'nickname'      => self::getRequest($data, 'nickname', 'string'),
            'sign'          => self::getRequest($data, 'sign', 'string'),
            'headico'       => self::getRequest($data, 'headico', 'string'),
            'headbg'        => self::getRequest($data, 'headbg', 'string'),
            'tag'           => self::getRequest($data, 'tag', 'string'),
            'is_disabled'   => self::getRequest($data, 'is_disabled', 'int', 0),
            'group_id'      => self::getRequest($data, 'group_id', 'int', 0),
            'group_dark_id' => self::getRequest($data, 'group_dark_id', 'int', 0),
            'transfer_id'   => self::getRequest($data, 'transfer_id', 'int', 0),
            'error_msg'     => self::getRequest($data, 'error_msg', 'string', ''),
            'withdraw_fee'  => self::getRequest($data, 'withdraw_fee', 'int', 0),
        ];
        // /up主才有的配置
        $upRow = [
            '_id'               => self::getRequest($data, '_id', 'int'),
            'nickname'          => self::getRequest($data, 'nickname', 'string'),
            'headico'           => self::getRequest($data, 'headico', 'string'),
            'category'          => self::getRequest($data, 'up_category', 'string', ''),
            'post_fee_rate'     => self::getRequest($data, 'post_fee_rate', 'int', 0),
            'post_upload_num'   => self::getRequest($data, 'post_upload_num', 'int', 0),
            'movie_fee_rate'    => self::getRequest($data, 'movie_fee_rate', 'int', 0),
            'movie_money_limit' => self::getRequest($data, 'movie_money_limit', 'int', 0),
            'movie_upload_num'  => self::getRequest($data, 'movie_upload_num', 'int', 0),
        ];

        $right = self::getRequest($data, 'right', 'string', '');
        if ($row['is_disabled'] && empty($row['error_msg'])) {
            throw new BusinessException(StatusCode::DATA_ERROR, '请填写禁用原因!');
        }

        $userInfo = UserModel::findByID($row['_id']);
        if ($data['group_id'] > 0) {
            $group                 = UserGroupService::getInfo($row['group_id']);
            $row['group_name']     = strval($group['name']);
            $row['group_icon']     = strval($group['icon']);
            $row['group_rate']     = $right == 'money' ? 0 : intval($group['rate']);
            $row['group_end_time'] = $data['group_end_time'] ? intval(strtotime($data['group_end_time'])) : 0;
            if (empty($userInfo['group_start_time'])) {
                $row['group_start_time'] = time();
            }
            $row['right'] = $group['right']['logic'] ?? [];
        } else {
            $row['group_end_time'] = $data['group_end_time'] ? intval(strtotime($data['group_end_time'])) : 0;
            $row['group_name']     = '';
            $row['group_icon']     = '';
            $row['group_rate']     = $right == 'money' ? 0 : 100;
            $row['right']          = [];
        }

        if ($data['group_dark_id'] > 0) {
            $group                      = UserGroupService::getInfo($row['group_dark_id']);
            $row['group_dark_name']     = strval($group['name']);
            $row['group_dark_rate']     = $right == 'money' ? 0 : intval($group['rate']);
            $row['group_dark_end_time'] = $data['group_dark_end_time'] ? intval(strtotime($data['group_dark_end_time'])) : 0;
            if (empty($userInfo['group_dark_start_time'])) {
                $row['group_dark_start_time'] = time();
            }
        } else {
            $row['group_dark_end_time'] = $data['group_dark_end_time'] ? intval(strtotime($data['group_dark_end_time'])) : 0;
            $row['group_dark_name']     = '';
            $row['group_dark_rate']     = $right == 'money' ? 0 : 100;
        }

        $result = UserModel::save($row);
        UserUpService::do(
            $upRow['_id'],
            $upRow['nickname'],
            $upRow['headico'],
            $upRow['category'],
            boolval($upRow['category']),
            $upRow['post_fee_rate'],
            $upRow['post_upload_num'],
            $upRow['movie_fee_rate'],
            $upRow['movie_money_limit'],
            $upRow['movie_upload_num'],
        );

        UserService::setInfoToCache($row['_id']);
        return $result;
    }

    /**
     * 找回账号
     * @param                    $oldUserId
     * @param                    $newUserId
     * @return bool
     * @throws BusinessException
     */
    public static function findAccount($oldUserId, $newUserId)
    {
        $user1 = UserModel::findByID(intval($oldUserId));
        $user2 = UserModel::findByID(intval($newUserId));
        if (empty($user1) || empty($user2) || $user1['is_disabled'] || $user2['is_disabled']) {
            throw new BusinessException(StatusCode::PARAMETER_ERROR, '用户不存在或者已被禁用');
        }
        if ($user1['account'] == $user2['account']) {
            throw new BusinessException(StatusCode::PARAMETER_ERROR, '当前账号和待找回的账号一样!');
        }
        try {
            UserService::doChangeDevice($user1, $user2);
            UserService::setInfoToCache($oldUserId);
            UserService::setInfoToCache($newUserId);
            return true;
        } catch (\Exception $e) {
            throw new BusinessException(StatusCode::PARAMETER_ERROR, $e->getMessage());
        }
    }

    /**
     * 后台充值
     * @param                    $userId
     * @param                    $num
     * @param                    $type
     * @param  string            $remark
     * @return bool
     * @throws BusinessException
     */
    public static function doRecharge($userId, $num, $type, $remark = '')
    {
        $num = intval($num);

        $token = AdminUserService::getToken();
        if (empty($token)) {
            throw new BusinessException(StatusCode::DATA_ERROR, '异常操作!');
        }
        $user = UserModel::findByID(intval($userId));
        if (empty($user)) {
            throw new BusinessException(StatusCode::DATA_ERROR, '用户不存在!');
        }
        $remark = $remark ?: '系统充值';
        if ($type == 'point') {
            return self::doPointRecharge($user, $num, $token['user_id'], $token['username'], $remark);
        }
        return false;
    }

    /**
     * 生成用户
     * @param                    $num
     * @return bool
     * @throws BusinessException
     */
    public static function create($num)
    {
        for ($i = 0; $i < $num; $i++) {
            $uniqid   = uniqid();
            $deviceId = 'system_' . $uniqid;
            UserService::register('username', $deviceId, "phone_{$uniqid}", 'android', '1.0', '', 'system');
            LogUtil::info("Create user {$deviceId} ok!");
        }
        return true;
    }

    /**
     * 充值金币
     * @param         $user
     * @param         $num
     * @param         $adminId
     * @param         $adminName
     * @param         $balanceField
     * @param  string $remark
     * @return bool
     */
    protected static function doPointRecharge($user, $num, $adminId, $adminName, $remark = '')
    {
        try {
            $orderSn = CommonUtil::createOrderNo('AR');
            if ($num > 0) {
                AccountService::addBalance($user, $orderSn, $num, 1, 'balance', $remark, "id:{$adminId} name:{$adminName}");
            } else {
                AccountService::reduceBalance($user, $orderSn, $num, 1, 'balance', $remark, "id:{$adminId} name:{$adminName}");
            }
            return true;
        } catch (\Exception $e) {
            LogUtil::error(sprintf('%s in %s line %s', $e->getMessage(), $e->getFile(), $e->getLine()));
        }
        return false;
    }
}
