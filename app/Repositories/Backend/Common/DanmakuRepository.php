<?php

namespace App\Repositories\Backend\Common;

use App\Constants\CommonValues;
use App\Core\Repositories\BaseRepository;
use App\Models\Common\DanmakuModel;
use App\Models\User\UserModel;
use App\Services\User\UserService;
use App\Services\User\UserUpService;

/**
 * Class DanmakuRepository
 * @package App\Repositories\Backend
 */
class DanmakuRepository extends BaseRepository
{
    /**
     * 获取列表
     * @param        $request
     * @return array
     */
    public static function getList($request)
    {
        $page     = self::getRequest($request, 'page', 'int', 1);
        $pageSize = self::getRequest($request, 'pageSize', 'int', 30);
        $sort     = self::getRequest($request, 'sort', 'string', '_id');
        $order    = self::getRequest($request, 'order', 'int', -1);
        $query    = [];
        $filter   = [];

        if ($request['_id']) {
            $filter['_id'] = self::getRequest($request, '_id', 'int');
            $query['_id']  = $filter['_id'];
        }
        if ($request['object_id']) {
            $filter['object_id'] = self::getRequest($request, 'object_id', 'string');
            $query['object_id']  = $filter['object_id'];
        }
        if ($request['object_type']) {
            $filter['object_type'] = self::getRequest($request, 'object_type', 'string');
            $query['object_type']  = $filter['object_type'];
        }
        if (isset($request['user_id']) && $request['user_id'] !== '') {
            $filter['user_id'] = self::getRequest($request, 'user_id', 'int');
            $query['user_id']  = $filter['user_id'];
        }
        if (isset($request['content']) && $request['content'] !== '') {
            $filter['content'] = self::getRequest($request, 'content', 'string');
            $query['content']  = ['$regex' => $filter['content'], '$options' => 'i'];
        }
        if (isset($request['status']) && $request['status'] !== '') {
            $filter['status'] = self::getRequest($request, 'status', 'int');
            $query['status']  = $filter['status'];
        }

        $count = DanmakuModel::count($query);
        $items = DanmakuModel::find($query, [], [$sort => $order], ($page - 1) * $pageSize, $pageSize);
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

            $item['created_at']  = date('Y-m-d H:i', $item['created_at']);
            $item['updated_at']  = date('Y-m-d H:i', $item['updated_at']);
            $item['status_text'] = CommonValues::getCommentStatus($item['status']);

            $items[$index] = $item;
        }

        return [
            'filter'   => $filter,
            'items'    => empty($items) ? [] : array_values($items),
            'count'    => $count,
            'page'     => $page,
            'pageSize' => $pageSize,
        ];
    }

    /**
     * 删除弹幕
     * @param        $id
     * @param  bool  $disabledUser
     * @return mixed
     */
    public static function delete($id, $disabledUser = false)
    {
        $id   = intval($id);
        $data = DanmakuModel::findByID($id);
        if (!$data) {
            return false;
        }
        DanmakuModel::deleteById($id);
        // 用户禁用
        if ($disabledUser) {
            UserService::doDisabled($data['user_id'], '弹幕违规');
            DanmakuModel::delete(['user_id' => intval($data['user_id'])]);
        }
        return true;
    }

    /**
     * 弹幕通过
     * @param       $id
     * @return void
     */
    public static function pass($id)
    {
        $id      = strval($id);
        $danmaku = DanmakuModel::findFirst(['_id' => $id, 'status' => 0]);
        if ($danmaku) {
            DanmakuModel::update(['status' => 1], ['_id' => $id]);
        }
    }
}
