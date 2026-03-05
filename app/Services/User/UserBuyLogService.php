<?php

namespace App\Services\User;

use App\Constants\StatusCode;
use App\Core\Services\BaseService;
use App\Exception\BusinessException;
use App\Models\User\UserBuyLogModel;

/**
 * 购买记录
 * Class UserBuyLogService
 * @package App\Services
 */
class UserBuyLogService extends BaseService
{
    /**
     * @param            $query
     * @param  string    $filed
     * @return int|mixed
     */
    public static function sum($query, $filed = '$object_money')
    {
        $childNum = UserBuyLogModel::aggregate([
            ['$match' => $query],
            ['$group' => ['_id' => null, 'count' => ['$sum' => $filed]]]
        ]);
        return $childNum ? $childNum['count'] : 0;
    }

    /**
     * 是否购买
     * @param             $userId
     * @param             $objectId
     * @param             $objectType
     * @param  null|mixed $extId
     * @return bool
     */
    public static function has($userId, $objectId, $objectType, $extId = null)
    {
        $id  = md5($userId . '_' . $objectType . '_' . $objectId);
        $row = UserBuyLogModel::findByID($id);
        if (empty($row)) {
            return false;
        }
        if (!empty($extId) && !in_array($extId, $row['ext_ids'])) {
            return false;
        }

        if ($objectType == 'up') {
            return $row['end_time'] > time() ? true : false;
        }
        return true;
    }

    /**
     * 增加记录
     * @param             $orderSn
     * @param             $userModel
     * @param             $objectId
     * @param             $objectType
     * @param             $objectImg
     * @param             $money
     * @param             $moneyOld
     * @param             $position
     * @param             $endTime
     * @param  null|mixed $extId
     * @return mixed
     */
    public static function do($orderSn, $userModel, $objectId, $objectType, $objectImg, $money, $moneyOld, $position = '', $extId = null, $endTime = null)
    {
        $id     = md5($userModel['_id'] . '_' . $objectType . '_' . $objectId);
        $extId  = strval($extId);
        $hasRow = UserBuyLogModel::findByID($id);
        if (empty($hasRow)) {
            $data = [
                '_id'              => $id,
                'order_sn'         => $orderSn,
                'user_id'          => intval($userModel['_id']),
                'username'         => $userModel['username'],
                'channel_name'     => $userModel['channel_name'],
                'object_id'        => strval($objectId),
                'object_type'      => $objectType,
                'object_position'  => $position,
                'object_img'       => $objectImg,
                'object_money'     => doubleval($money),
                'object_money_old' => doubleval($moneyOld),
                'ext_ids'          => $extId ? [$extId] : [],
                'status'           => 0,
                'register_at'      => intval($userModel['register_at']),
                'label'            => date('Y-m-d'),
                'end_time'         => empty($endTime) ? -1 : $endTime * 1
            ];
            return UserBuyLogModel::insert($data);
        }
        if (empty($extId)) {
            throw new BusinessException(StatusCode::DATA_ERROR, '您已经购买过了!');
        }
        $extIds = $hasRow['ext_ids'];
        if (in_array($extId, $extIds)) {
            throw new BusinessException(StatusCode::DATA_ERROR, '您已经购买过了!');
        }
        $extIds[] = $extId;
        return UserBuyLogModel::updateById(['ext_ids' => $extIds], $id);
    }

    /**
     * 购买记录
     * @param        $userId
     * @param        $objectType
     * @param        $page
     * @param        $pageSize
     * @param        $cursor
     * @return array
     */
    public static function getIds($userId, $objectType, $page = 1, $pageSize = 12, $cursor = null)
    {
        $query = ['user_id' => $userId, 'object_type' => $objectType];
        $count = UserBuyLogModel::count($query);
        if (!empty($cursor)) {
            $query['updated_at'] = ['$lt' => intval($cursor)];
            $rows                = UserBuyLogModel::find($query, ['object_id', 'updated_at'], ['updated_at' => -1], 0, $pageSize);
        } else {
            $rows = UserBuyLogModel::find($query, ['object_id', 'updated_at'], ['updated_at' => -1], ($page - 1) * $pageSize, $pageSize);
        }
        $ids = array_column($rows, 'object_id');
        return [
            'ids'          => $ids ?: [],
            'total'        => $count,
            'current_page' => $page,
            'page_size'    => $pageSize,
            'last_page'    => strval(ceil($count / $pageSize)),
            'cursor'       => !empty($rows) ? strval($rows[count($rows) - 1]['updated_at']) : '',
        ];
    }
}
