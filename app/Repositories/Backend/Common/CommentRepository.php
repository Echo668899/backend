<?php

namespace App\Repositories\Backend\Common;

use App\Constants\CommonValues;
use App\Core\Repositories\BaseRepository;
use App\Exception\BusinessException;
use App\Models\Comics\ComicsModel;
use App\Models\Common\CommentModel;
use App\Models\Movie\MovieModel;
use App\Models\Post\PostModel;
use App\Models\User\UserModel;
use App\Services\Common\CommentService;
use App\Services\Common\CommonService;
use App\Services\User\UserService;
use App\Services\User\UserUpService;

/**
 * Class CommentRepository
 * @package App\Repositories\Backend
 */
class CommentRepository extends BaseRepository
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
            $filter['_id'] = self::getRequest($request, '_id', 'string');
            $query['_id']  = $filter['_id'];
        }
        if ($request['object_id']) {
            $filter['object_id'] = self::getRequest($request, 'object_id', 'string');
            $query['object_id']  = $filter['object_id'];
        }
        if ($request['comment_id'] != '') {
            $filter['comment_id'] = self::getRequest($request, 'comment_id', 'string');
            $query['comment_id']  = $filter['comment_id'];
        }
        if ($request['object_type']) {
            $filter['object_type'] = self::getRequest($request, 'object_type', 'string');
            $query['object_type']  = $filter['object_type'];
        }
        if ($request['comment_type']) {
            $filter['comment_type'] = self::getRequest($request, 'comment_type', 'string');
            $query['comment_type']  = $filter['comment_type'];
        }
        if (isset($request['from_uid']) && $request['from_uid'] !== '') {
            $filter['from_uid'] = self::getRequest($request, 'from_uid', 'int');
            $query['from_uid']  = $filter['from_uid'];
        }
        if (isset($request['status']) && $request['status'] !== '') {
            $filter['status'] = self::getRequest($request, 'status', 'int');
            $query['status']  = $filter['status'];
        }
        if (isset($request['content']) && $request['content'] !== '') {
            $filter['content']    = self::getRequest($request, 'content', 'string');
            $query['content']     = ['$regex' => $filter['content'], '$options' => 'i'];
            $query['object_type'] = 'text';
        }

        $count = CommentModel::count($query);
        $items = CommentModel::find($query, [], [$sort => $order], ($page - 1) * $pageSize, $pageSize);
        foreach ($items as $index => $item) {
            $user         = UserModel::findByID(intval($item['from_uid']));
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
     * 评论通过
     * @param                    $id
     * @throws BusinessException
     */
    public static function pass($id)
    {
        $id      = strval($id);
        $comment = CommentModel::findFirst(['_id' => $id, 'status' => 0]);
        if ($comment) {
            CommentModel::update(['status' => 1], ['_id' => $id]);
            CommonService::updateRedisCounter("{$comment['object_type']}_comment_ok_{$comment['object_id']}", 1);// 通过+1
            CommonService::updateRedisCounter("{$comment['object_type']}_comment_no_{$comment['object_id']}", -1);// 未通过-1
            CommentService::handler($comment['from_uid'], $comment['object_id'], $comment['object_type'], $comment['comment_id'], $comment['created_at']);
        }
    }

    /**
     * 删除评论
     * @param        $id
     * @param  bool  $disabledUser
     * @return mixed
     */
    public static function delete($id, $disabledUser = false)
    {
        $comment = CommentModel::findByID($id);
        // /如果是二级评论,减去二级数量
        if ($comment['parent_id'] == 0) {
            $replyOkCount = CommentModel::count(['comment_id' => $comment['_id'], 'status' => 1]);
            $replyNoCount = CommentModel::count(['comment_id' => $comment['_id'], 'status' => 0]);
            // 删除二级
            CommentModel::delete(['comment_id' => $comment['_id']]);
        } else {
            $replyOkCount = $replyNoCount = 0;
        }
        // /减去本身
        if ($comment['status']) {
            $replyOkCount = $replyOkCount + 1;
        } else {
            $replyNoCount = $replyNoCount + 1;
        }

        $commentOk = CommonService::updateRedisCounter("{$comment['object_type']}_comment_ok_{$comment['object_id']}", -1 * $replyOkCount);
        $commentNo = CommonService::updateRedisCounter("{$comment['object_type']}_comment_no_{$comment['object_id']}", -1 * $replyNoCount);

        // 删除本身
        CommentModel::deleteById($comment['_id']);
        switch ($comment['object_type']) {
            case 'movie':
                MovieModel::updateRaw(['$set' => ['comment' => ($commentOk + $commentNo)]], ['_id' => strval($comment['object_id'])]);
                break;
            case 'comics':
                ComicsModel::updateRaw(['$set' => ['comment' => ($commentOk + $commentNo)]], ['_id' => strval($comment['object_id'])]);
                break;
            case 'post':
                PostModel::updateRaw(['$set' => ['comment' => ($commentOk + $commentNo)]], ['_id' => strval($comment['object_id'])]);
                break;
        }

        // 用户
        if ($disabledUser) {
            UserService::doDisabled($comment['from_uid'], '评论违规');
        }
        return true;
    }

    /**
     * @param                    $userId
     * @param                    $objectType
     * @param                    $objectId
     * @param                    $content
     * @return array
     * @throws BusinessException
     */
    public static function doComment($userId, $objectType, $objectId, $content)
    {
        return CommentService::do($userId, $objectType, $objectId, $content, '', true);
    }
}
