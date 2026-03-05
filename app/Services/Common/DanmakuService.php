<?php

namespace App\Services\Common;

use App\Constants\StatusCode;
use App\Core\Services\BaseService;
use App\Exception\BusinessException;
use App\Jobs\Event\EventBusJob;
use App\Jobs\Event\Payload\Common\DanmakuPayload;
use App\Models\Comics\ComicsChapterModel;
use App\Models\Comics\ComicsModel;
use App\Models\Common\DanmakuModel;
use App\Models\Movie\MovieModel;
use App\Models\Novel\NovelChapterModel;
use App\Models\Novel\NovelModel;
use App\Services\User\UserService;

/**
 * Class DanmakuService
 * @package App\Services
 */
class DanmakuService extends BaseService
{
    /**
     * 弹幕列表
     * @param                    $objectId
     * @param                    $objectType
     * @param                    $startPos   //弹幕位置,movie为区间开始,comics和novel为0
     * @param                    $endPos     //弹幕位置,movie为区间结束,comics和novel为100
     * @param                    $subId
     * @return array
     * @throws BusinessException
     */
    public static function getList($objectId, $objectType, $startPos, $endPos, $subId = '')
    {
        if (empty($objectId) || empty($objectType)) {
            throw new BusinessException(StatusCode::DATA_ERROR, '请检查必要输入!');
        }

        $query = [
            'object_id'   => strval($objectId),
            'object_type' => strval($objectType),
            'status'      => 1,
            'pos'         => ['$gte' => $startPos, '$lt' => $endPos]
        ];
        if (!empty($subId)) {
            $query['sub_id'] = strval($subId);
        }
        $result = [];
        $num    = DanmakuModel::count($query);
        $rows   = DanmakuModel::find($query, [], ['time' => 1], 0, $num);
        foreach ($rows as $key => $value) {
            $result[$key] = [
                'id'      => strval($value['_id']),
                'user_id' => strval($value['user_id']),
                'pos'     => strval($value['pos']),
                'size'    => strval($value['size']),
                'color'   => strval($value['color']),
                'pool'    => strval($value['pool']),
                'content' => strval($value['content']),
            ];
        }
        return $result;
    }

    /**
     * 去彈幕
     * @param                    $userId
     * @param                    $objectId   //资源id
     * @param                    $objectType //资源类型
     * @param                    $pos        //弹幕位置,movie为播放进度/s,comics和novel为该章节的阅读进度 0-100
     * @param                    $size       //文字大小
     * @param                    $color
     * @param                    $pool
     * @param                    $content
     * @param                    $subId      //章节id
     * @return true
     * @throws BusinessException
     */
    public static function do($userId, $objectId, $objectType, $pos, $size, $color, $pool, $content, $subId = '')
    {
        $objectId = strval($objectId);
        $subId    = strval($subId);
        if ($pos === '' || empty($objectId) || empty($objectType) || empty($content)) {
            throw new BusinessException(StatusCode::DATA_ERROR, '请检查必要输入!');
        }

        if ($size < 12 || $size > 20) {
            throw new BusinessException(StatusCode::DATA_ERROR, '弹幕字号仅能12至20!');
        }
        $userInfo = UserService::getInfoFromCache($userId);
        UserService::checkDisabled($userInfo);
        // 是否有权限
        if (!in_array('do_danmaku', UserService::getRights($userInfo))) {
            throw new BusinessException(StatusCode::DATA_ERROR, '您没有权限发送弹幕!');
        }
        if (!CommonService::checkActionLimit('do_danmaku_' . $userId, 60, 5)) {
            throw new BusinessException(StatusCode::DATA_ERROR, '发布弹幕过快,请稍等几分钟!');
        }

        switch ($objectType) {
            case 'movie':
                $objectRow = MovieModel::findByID($objectId);
                $linkIds   = array_column($objectRow['links'], 'id');
                if (!empty($subId) && in_array($subId, $linkIds) == false) {
                    throw new BusinessException(StatusCode::DATA_ERROR, '内容不存在!');
                }
                break;
            case 'comics':
                $objectRow = ComicsModel::findByID($objectId);
                // 对章节的弹幕
                if (!empty($subId)) {
                    if (ComicsChapterModel::count(['_id' => $subId, 'comics_id' => $objectId]) == 0) {
                        throw new BusinessException(StatusCode::DATA_ERROR, '章节不存在!');
                    }
                }
                break;
            case 'novel':
                $objectRow = NovelModel::findByID($objectId);
                // 对章节的弹幕
                if (!empty($subId)) {
                    if (NovelChapterModel::count(['_id' => $subId, 'novel_id' => $objectId]) == 0) {
                        throw new BusinessException(StatusCode::DATA_ERROR, '章节不存在!');
                    }
                }
                break;
            case 'audio':
            case 'post':
                throw new BusinessException(StatusCode::DATA_ERROR, '该板块不支持弹幕!');
                break;
        }
        if (empty($objectRow)) {
            throw new BusinessException(StatusCode::DATA_ERROR, '内容不存在!');
        }

        DanmakuModel::insert([
            'user_id'     => intval($userId),
            'object_id'   => strval($objectId),
            'sub_id'      => strval($subId),
            'object_type' => strval($objectType),
            'pos'         => intval($pos),
            'status'      => kProdMode ? 0 : 1,
            'size'        => intval($size),
            'color'       => intval($color),
            'pool'        => intval($pool),
            'content'     => $content
        ]);
        JobService::create(new EventBusJob(new DanmakuPayload($userId, $objectType, $objectId, $pos)));
        return true;
    }
}
