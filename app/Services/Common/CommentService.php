<?php

namespace App\Services\Common;

use App\Constants\StatusCode;
use App\Core\Services\BaseService;
use App\Exception\BusinessException;
use App\Jobs\Event\EventBusJob;
use App\Jobs\Event\Payload\Common\CommentPayload;
use App\Models\Audio\AudioModel;
use App\Models\Comics\ComicsModel;
use App\Models\Common\CommentModel;
use App\Models\Movie\MovieModel;
use App\Models\Novel\NovelModel;
use App\Models\Post\PostModel;
use App\Services\User\UserService;
use App\Utils\CommonUtil;

/**
 * Class CommentService
 * @package App\Services
 */
class CommentService extends BaseService
{
    /**
     * 去评论
     * @param                    $userId
     * @param                    $objectType
     * @param                    $objectId
     * @param                    $content
     * @param                    $time
     * @param                    $isAdmin
     * @param                    $commentType
     * @return array
     * @throws BusinessException
     */
    public static function do($userId, $objectType, $objectId, $content, $time = '', $isAdmin = false, $commentType = 'text')
    {
        if (!in_array($commentType, ['text', 'image'])) {
            throw new BusinessException(StatusCode::DATA_ERROR, '不支持的评论类型!');
        }
        if (empty($objectId) || empty($content)) {
            throw new BusinessException(StatusCode::DATA_ERROR, '请检查必要输入!');
        }

        $userInfo = UserService::getInfoFromCache($userId);
        UserService::checkDisabled($userInfo);

        switch ($commentType) {
            case 'text':
                if (!CommonUtil::checkKeywords($content)) {
                    throw new BusinessException(StatusCode::DATA_ERROR, '内容不能含有关键字和广告!');
                }
                break;
                /* lsj库路径不固定
                 case 'image':
                     if (!substr_count($content, '/media')) {
                         throw new BusinessException(StatusCode::DATA_ERROR, '上传图片格式不对!');
                     }
                     break;*/
        }

        if ($isAdmin == false) {
            // 是否有权限
            if (!in_array('do_comment', UserService::getRights($userInfo))) {
                throw new BusinessException(StatusCode::DATA_ERROR, '您没有权限发表评论!');
            }
            if (!CommonService::checkActionLimit('do_comment_' . $userId, 60, 2)) {
                throw new BusinessException(StatusCode::DATA_ERROR, '发布评论过快,请稍等几分钟!');
            }
        }

        $toUserId = $commentId = $parentId = 0;
        switch ($objectType) {
            case 'movie':
                $model = MovieModel::findByID($objectId);
                if (empty($model)) {
                    throw new BusinessException(StatusCode::DATA_ERROR, '视频已下架!');
                }
                break;
            case 'comics':
                $model = ComicsModel::findByID($objectId);
                if (empty($model)) {
                    throw new BusinessException(StatusCode::DATA_ERROR, '套图已下架!');
                }
                break;
            case 'novel':
                $model = NovelModel::findByID($objectId);
                if (empty($model)) {
                    throw new BusinessException(StatusCode::DATA_ERROR, '小说已下架!');
                }
                break;
            case 'audio':
                $model = AudioModel::findByID($objectId);
                if (empty($model)) {
                    throw new BusinessException(StatusCode::DATA_ERROR, '有声已下架!');
                }
                break;
            case 'post':
                $model = PostModel::findByID($objectId);
                if (empty($model)) {
                    throw new BusinessException(StatusCode::DATA_ERROR, '帖子不存在!');
                }
                break;
            case 'comment':
                $model = CommentModel::findByID($objectId);
                if (empty($model)) {
                    throw new BusinessException(StatusCode::DATA_ERROR, '评论不存在!');
                }
                $objectId   = $model['object_id'];
                $objectType = $model['object_type'];
                $toUserId   = $model['parent_id'] ? $model['from_uid'] : 0;
                $commentId  = $model['comment_id'] ?: $model['_id'];
                $parentId   = $model['_id'];
                break;
            default:
                throw new BusinessException(StatusCode::DATA_ERROR, '不支持该类型!');
        }

        $data = [
            'object_id'    => $objectId,
            'from_uid'     => $userId,
            'to_uid'       => intval($toUserId),
            'comment_id'   => strval($commentId),
            'parent_id'    => strval($parentId),
            'object_type'  => $objectType,
            'content'      => $content,
            'comment_type' => $commentType,
            'love'         => 0,
            'ip'           => CommonUtil::getClientIp(),
            'status'       => $isAdmin ? 1 : 0,
            'child_num'    => 0,
            'time'         => strval($time),
        ];
        $data['_id'] = CommentModel::save($data, false);
        if (empty($data['_id'])) {
            throw new BusinessException(StatusCode::DATA_ERROR, '评论失败!');
        }

        if ($data['status'] == 1) {
            CommonService::updateRedisCounter("{$objectType}_comment_ok_{$objectId}", 1);// 通过+1
            self::handler($userId, $objectId, $objectType, $commentId, time());
        } else {
            CommonService::updateRedisCounter("{$objectType}_comment_no_{$objectId}", 1);// 未通过+1
        }

        JobService::create(new EventBusJob(new CommentPayload($userId, $objectType, $objectId, $content)));
        return self::formatComment($userId, $data, $userInfo);
    }

    /**
     * 评论事件
     * @param                    $userId
     * @param                    $objectId
     * @param                    $objectType
     * @param                    $commentId
     * @param                    $commentTime
     * @throws BusinessException
     */
    public static function handler($userId, $objectId, $objectType, $commentId, $commentTime)
    {
        $objectId = strval($objectId);
        switch ($objectType) {
            case 'movie':
                MovieModel::updateRaw(['$inc' => ['comment' => 1]], ['_id' => $objectId]);
                break;
            case 'post':
                PostModel::updateRaw(['$inc' => ['comment' => 1], '$set' => ['last_comment' => time()]], ['_id' => $objectId]);
                break;
            case 'comics':
                ComicsModel::updateRaw(['$inc' => ['comment' => 1]], ['_id' => $objectId]);
                break;
            case 'novel':
                NovelModel::updateRaw(['$inc' => ['comment' => 1]], ['_id' => $objectId]);
                break;
            case 'audio':
                AudioModel::updateRaw(['$inc' => ['comment' => 1]], ['_id' => $objectId]);
                break;
        }
        if ($commentId > 0) {
            $comment = CommentModel::findByID($commentId);
            $update  = ['$inc' => ['child_num' => 1]];
            if ($comment['updated_at'] < $commentTime) {
                $update['$set'] = ['updated_at' => $commentTime];
            }
            CommentModel::updateRaw($update, ['_id' => $commentId]);
        }
    }

    /**
     * 评论列表
     * @param                    $userId
     * @param                    $objectId
     * @param                    $objectType
     * @param  int               $page
     * @param  int               $pageSize
     * @return array
     * @throws BusinessException
     */
    public static function getCommentList($userId, $objectId, $objectType, $page = 1, $pageSize = 15)
    {
        if (!in_array($objectType, ['movie', 'novel', 'audio', 'comics', 'post'])) {
            throw new BusinessException(StatusCode::DATA_ERROR, '不支持该类型!');
        }
        $skip = ($page - 1) * $pageSize;

        $query = ['object_id' => $objectId, 'object_type' => $objectType, 'comment_id' => '0', 'status' => 1];
        $count = CommentModel::count($query);
        $rows  = CommentModel::find($query, [], ['updated_at' => -1, 'love' => -1, '_id' => -1, ], $skip, $pageSize);
        foreach ($rows as &$row) {
            $userInfo = UserService::getInfoFromCache($row['from_uid']);
            $userInfo = $userInfo ?: UserService::getInfoFromCache(0);
            $row      = self::formatComment($userId, $row, $userInfo);
            unset($row);
        }
        /*if($page==1){
            $userInfo = UserService::getInfoFromCache(-1);
            $defaultRow = array(
                '_id' => strval(0),
                'created_at' => time(),
                'status' => 1,
                'comment_type' => 'text',
                'content' => "官方提示您,请勿相信评论区中任何约会QQ,微信等联系方式\n---------此评论系统生成,无法回复",
            );
            array_unshift($rows,self::formatComment($userId,$defaultRow,$userInfo));
        }*/

        return [
            'data'         => $rows,
            'total'        => strval($count),
            'current_page' => strval($page),
            'page_size'    => strval($pageSize),
            'last_page'    => strval(ceil($count / $pageSize))
        ];
    }

    /**
     * 回复列表
     * @param        $commentId
     * @param  int   $page
     * @param  int   $pageSize
     * @return array
     */
    public static function getReplyList($commentId, $page = 1, $pageSize = 4)
    {
        $skip = ($page - 1) * $pageSize;
        $rows = CommentModel::find(['comment_id' => strval($commentId), 'status' => 1], [], ['_id' => -1], $skip, $pageSize);
        foreach ($rows as &$row) {
            $userInfo = UserService::getInfoFromCache($row['from_uid']);
            $userInfo = $userInfo ?: UserService::getInfoFromCache(0);
            $row      = self::formatComment(null, $row, $userInfo);
            unset($row);
        }
        return $rows;
    }

    /**
     * 格式化的评论列表
     * @param        $userId
     * @param        $row
     * @param        $userInfo
     * @return array
     */
    public static function formatComment($userId, $row, $userInfo)
    {
        return [
            'id'       => strval($row['_id']),
            'user_id'  => strval($userInfo['id']),
            'nickname' => strval($userInfo['nickname']),
            'headico'  => CommonService::getCdnUrl($userInfo['headico']),
            'type'     => strval($row['comment_type']),
            'content'  => value(function () use ($row) {
                if ($row['comment_type'] == 'image') {
                    return CommonService::getCdnUrl($row['content']);
                }
                return $row['content'];
            }),
            'label'    => CommonUtil::ucTimeAgo($row['created_at'] ?: time()),
            'online'   => strval($userInfo['online']),
            'is_up'    => strval($userInfo['is_up']),
            'love'     => strval($row['love'] ?: 0),
            'has_love' => !empty($userId) ? (CommentLoveService::has($userId, $row['_id']) ? 'y' : 'n') : 'n',

            // 评论
            'child_num' => strval($row['child_num'] ?: 0),
            'child'     => !empty($row['child_num']) ? self::getReplyList($row['_id'], 1, 4) : [],

            // /回复
            'to_uid'   => strval($row['to_uid']),
            'to_uname' => value(function () use ($row) {
                if ($row['to_uid']) {
                    $userInfo = UserService::getInfoFromCache($row['to_uid']);
                    if (empty($userInfo) || $userInfo['is_disabled'] == 1) {
                        return '已销号';
                    }
                    return $userInfo['nickname'];
                }
                return '';
            })
        ];
    }
}
