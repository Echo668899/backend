<?php

namespace App\Services\User;

use App\Constants\StatusCode;
use App\Core\Services\BaseService;
use App\Exception\BusinessException;
use App\Jobs\Event\EventBusJob;
use App\Jobs\Event\Payload\User\UserDoFansPayload;
use App\Models\User\UserFansModel;
use App\Models\User\UserModel;
use App\Services\Common\Chat\ChatService;
use App\Services\Common\CommonService;
use App\Services\Common\JobService;

/**
 * 用户粉丝 用户=>用户
 * @property UserService $userService
 */
class UserFansService extends BaseService
{
    public const ACTION_FOLLOW = 'follow';     // 普通关注
    public const ACTION_BLACK  = 'black';     // 拉黑

    /**
     * @param                    $userId
     * @param                    $homeId
     * @param                    $action
     * @return bool
     * @throws BusinessException
     */
    public static function do($userId, $homeId, $action = self::ACTION_FOLLOW)
    {
        $userId = intval($userId);
        $homeId = intval($homeId);

        if ($userId == $homeId) {
            return true;
        }
        if (!in_array($action, [self::ACTION_FOLLOW, self::ACTION_BLACK])) {
            return false;
        }

        // 限流器,超出拉黑
        try {
            if (!CommonService::checkActionLimit('user_follow_' . $userId, 30, 10)) {
                throw new BusinessException(StatusCode::DATA_ERROR, '关注频繁!');
            }
        } catch (\Exception $e) {
            UserService::doDisabled($userId, $e->getMessage());
            return 'none';
        }

        if (UserModel::count(['_id' => $homeId]) == 0) {
            throw  new BusinessException(StatusCode::PARAMETER_ERROR, '关注用户不存在!');
        }

        // 查询对方是否拉黑我
        $reverse     = UserFansModel::findByID("{$homeId}_{$userId}");
        $heBlockedMe = $reverse && $reverse['level'] === 'black';
        if ($heBlockedMe && $action === self::ACTION_FOLLOW) {
            throw  new BusinessException(StatusCode::PARAMETER_ERROR, '无法关注该用户!');
        }

        $_id = "{$userId}_{$homeId}";
        $row = UserFansModel::findByID($_id);

        // 取消行为
        if ($row && $row['level'] === $action) {
            UserFansModel::deleteById($_id);
            UserModel::updateRaw(['$inc' => ['follow' => -1]], ['_id' => $userId]);// 我的关注-1
            UserModel::updateRaw(['$inc' => ['fans' => -1]], ['_id' => $homeId]);// 对方粉丝-1
            UserService::setInfoToCache($homeId);
            return self::getRelationStatus($userId, $homeId);
        }

        // 切换行为
        if ($row && $row['level'] !== $action) {
            // follow → black
            if ($row['level'] === 'follow' && $action === 'black') {
                UserModel::updateRaw(['$inc' => ['follow' => -1]], ['_id' => $userId]);// 我的关注-1
                UserModel::updateRaw(['$inc' => ['fans' => -1]], ['_id' => $homeId]);// 对方粉丝-1
            }

            // black → follow
            if ($row['level'] === 'black' && $action === 'follow') {
                UserModel::updateRaw(['$inc' => ['follow' => 1]], ['_id' => $userId]);// 我的关注+1
                UserModel::updateRaw(['$inc' => ['fans' => 1]], ['_id' => $homeId]);// 对方粉丝+1
            }
            UserFansModel::updateRaw(['$set' => ['level' => $action]], ['_id' => $_id]);
            UserService::setInfoToCache($homeId);

            JobService::create(new EventBusJob(new UserDoFansPayload($userId, $homeId, $action)));
            return self::getRelationStatus($userId, $homeId);
        }

        // 新增行为
        UserFansModel::insert([
            '_id'     => $_id,
            'user_id' => $userId,
            'home_id' => $homeId,
            'level'   => $action
        ]);
        if ($action === self::ACTION_FOLLOW) {
            UserModel::updateRaw(['$inc' => ['follow' => 1]], ['_id' => $userId]);// 我的关注+1
            UserModel::updateRaw(['$inc' => ['fans' => 1]], ['_id' => $homeId]);// 对方粉丝+1

            UserService::setInfoToCache($homeId);
            ChatService::sendInteractNotify($homeId, 'follow', [
                'from_user_id' => $userId,
                'link'         => '/fans'
            ]);
        }
        JobService::create(new EventBusJob(new UserDoFansPayload($userId, $homeId, $action)));
        return self::getRelationStatus($userId, $homeId);
    }

    /**
     * 检查关系
     * @param       $userId
     * @param       $homeId
     * @param       $action
     * @return bool
     */
    public static function has($userId, $homeId, $action = self::ACTION_FOLLOW)
    {
        $userId = intval($userId);
        $homeId = intval($homeId);

        if ($userId == $homeId) {
            return true;
        }
        $_id = "{$userId}_{$homeId}";
        $row = UserFansModel::findByID($_id);
        if (empty($row)) {
            return false;
        }
        return $row['level'] === $action;
    }

    /**
     * 获取关系
     * @param         $userId
     * @param         $homeId
     * @return string
     */
    public static function getRelationStatus($userId, $homeId): string
    {
        if ($userId == $homeId) {
            return 'self';
        }

        $ids  = ["{$userId}_{$homeId}", "{$homeId}_{$userId}"];
        $rows = UserFansModel::find(['_id' => ['$in' => $ids]], ['user_id', 'home_id', 'level'], [], 0, 2);

        $iFollowHim = $heFollowMe = $iBlockedHim = $heBlockedMe = false;

        foreach ($rows as $row) {
            // 我和对方的关系
            if ($row['user_id'] == $userId && $row['home_id'] == $homeId) {
                if ($row['level'] === 'follow') {
                    $iFollowHim = true;
                }// 我关注了对方
                if ($row['level'] === 'black') {
                    $iBlockedHim = true;
                }// 我拉黑了对方
            }
            // 对方和我的关系
            if ($row['user_id'] == $homeId && $row['home_id'] == $userId) {
                if ($row['level'] === 'follow') {
                    $heFollowMe = true;
                }// 对方关注了我
                if ($row['level'] === 'black') {
                    $heBlockedMe = true;
                }// 对方拉黑了我
            }
        }

        // 从“我”的视角
        if ($iBlockedHim) {
            return 'i_black';
        }          // 我拉黑了对方
        if ($iFollowHim && $heFollowMe) {
            return 'mutual';
        } // 互关
        if ($iFollowHim) {
            return 'follow';
        }            // 我关注了对方
        if ($heFollowMe) {
            return 'followed_by';
        }       // 对方关注我
        if ($heBlockedMe) {
            return 'black';
        }            // 对方拉黑我
        return 'none';
    }

    /**
     * 批量获取关系
     * @param        $userId
     * @param  array $homeIds
     * @return array
     */
    public static function getMultiRelationStatus($userId, array $homeIds)
    {
        $result = [];

        if (empty($homeIds)) {
            return $result;
        }

        // 准备 hash set
        $iFollow  = [];
        $heFollow = [];
        $iBlack   = [];
        $heBlack  = [];

        // 1. 我 -> 他们
        $rows = UserFansModel::find(
            ['user_id' => $userId, 'home_id' => ['$in' => $homeIds]],
            ['home_id', 'level']
        );
        foreach ($rows as $row) {
            if ($row['level'] === 'follow') {
                $iFollow[$row['home_id']] = true;
            }
            if ($row['level'] === 'black') {
                $iBlack[$row['home_id']] = true;
            }
        }

        // 2. 他们 -> 我
        $rows = UserFansModel::find(
            ['user_id' => ['$in' => $homeIds], 'home_id' => $userId],
            ['user_id', 'level']
        );
        foreach ($rows as $row) {
            if ($row['level'] === 'follow') {
                $heFollow[$row['user_id']] = true;
            }
            if ($row['level'] === 'black') {
                $heBlack[$row['user_id']] = true;
            }
        }

        // 3. 拼装状态
        foreach ($homeIds as $uid) {
            if ($uid == $userId) {
                $result[$uid] = 'self';
                continue;
            }

            if (isset($iBlack[$uid])) {
                $result[$uid] = 'i_black';
                continue;
            }
            if (isset($iFollow[$uid]) && isset($heFollow[$uid])) {
                $result[$uid] = 'mutual';
                continue;
            }
            if (isset($iFollow[$uid])) {
                $result[$uid] = 'follow';
                continue;
            }
            if (isset($heFollow[$uid])) {
                $result[$uid] = 'followed_by';
                continue;
            }
            if (isset($heBlack[$uid])) {
                $result[$uid] = 'black';
                continue;
            }
            $result[$uid] = 'none';
        }
        return $result;
    }

    /**
     * 获取用户关注ids
     * @param        $userId   (用户id 当前用户)
     * @param        $homeId   (被查看用户id 自己id或别人id)
     * @param        $action
     * @param        $page
     * @param        $pageSize
     * @param  mixed $cursor
     * @return array
     */
    public static function getFollowIds($userId, $homeId, $action, $page = 1, $pageSize = 20, $cursor = '')
    {
        $userId = intval($userId);
        $homeId = intval($homeId);

        $query = ['user_id' => $homeId, 'level' => $action];
        $count = UserFansModel::count($query);
        if (!empty($cursor)) {
            $query['updated_at'] = ['$lt' => intval($cursor)];
            $rows                = UserFansModel::find($query, ['home_id', 'updated_at'], ['updated_at' => -1], 0, $pageSize);
        } else {
            $rows = UserFansModel::find($query, ['home_id', 'updated_at'], ['updated_at' => -1], ($page - 1) * $pageSize, $pageSize);
        }
        $ids = array_column($rows, 'home_id');
        return [
            'ids'          => $ids,
            'total'        => $count,
            'current_page' => $page,
            'page_size'    => $pageSize,
            'last_page'    => strval(ceil($count / $pageSize)),
            'cursor'       => !empty($rows) ? strval($rows[count($rows) - 1]['updated_at']) : '',
        ];
    }

    /**
     * 获取用户粉丝ids
     * @param        $userId
     * @param        $homeId
     * @param        $page
     * @param        $pageSize
     * @param  mixed $cursor
     * @return array
     */
    public static function getFansIds($userId, $homeId, $page = 1, $pageSize = 20, $cursor = '')
    {
        $userId = intval($userId);
        $homeId = intval($homeId);

        $query = ['home_id' => $homeId, 'level' => 'follow'];// 只展示关注我的
        $count = UserFansModel::count($query);
        if (!empty($cursor)) {
            $query['updated_at'] = ['$lt' => intval($cursor)];
            $rows                = UserFansModel::find($query, ['user_id', 'updated_at'], ['updated_at' => -1], 0, $pageSize);
        } else {
            $rows = UserFansModel::find($query, ['user_id', 'updated_at'], ['updated_at' => -1], ($page - 1) * $pageSize, $pageSize);
        }
        $ids = array_column($rows, 'user_id');
        return [
            'ids'          => $ids,
            'total'        => $count,
            'current_page' => $page,
            'page_size'    => $pageSize,
            'last_page'    => strval(ceil($count / $pageSize)),
            'cursor'       => !empty($rows) ? strval($rows[count($rows) - 1]['updated_at']) : '',
        ];
    }
}
