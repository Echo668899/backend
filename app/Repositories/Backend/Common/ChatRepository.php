<?php

declare(strict_types=1);

namespace App\Repositories\Backend\Common;

use App\Constants\StatusCode;
use App\Core\Repositories\BaseRepository;
use App\Exception\BusinessException;
use App\Models\Common\ChatMessageModel;
use App\Models\Common\ChatModel;
use App\Models\User\UserModel;
use App\Services\Common\Chat\ChatService;
use App\Services\User\UserService;
use App\Utils\CommonUtil;

/**
 * 消息
 * @package App\Repositories\Backend
 */
class ChatRepository extends BaseRepository
{
    /**
     * @param        $request
     * @return array
     */
    public static function getList($request = [])
    {
        $page     = self::getRequest($request, 'page', 'int', 1);
        $pageSize = self::getRequest($request, 'pageSize', 'int', 30);
        $sort     = self::getRequest($request, 'sort', 'string', 'updated_at');
        $order    = self::getRequest($request, 'order', 'int', -1);
        $query    = [];
        $filter   = [];

        if ($request['from_id']) {
            $filter['from_id'] = self::getRequest($request, 'from_id', 'string');
            $query['from_id']  = $filter['from_id'];
        }

        if (isset($request['status']) && $request['status'] !== '') {
            $filter['status'] = self::getRequest($request, 'status', 'int');
            if ($filter['status'] == 0) {
                // 'chat_type' = 'service' &&  'last_msg_role' = 'user' 说明消息未处理
                $query['last_msg_role'] = 'user';
            } else {
                $query['last_msg_role'] = 'service';
            }
        }

        if ($request['to_id']) {
            $filter['to_id'] = self::getRequest($request, 'to_id', 'string');
            $query['to_id']  = $filter['to_id'];
        }

        if ($request['start_time']) {
            $filter['start_time']        = self::getRequest($request, 'start_time', 'string');
            $query['created_at']['$gte'] = strtotime($filter['start_time']);
        }
        if ($request['end_time']) {
            $filter['end_time']          = self::getRequest($request, 'end_time', 'string');
            $query['created_at']['$lte'] = strtotime($filter['end_time']);
        }
        $skip  = ($page - 1) * $pageSize;
        $count = ChatModel::count($query);
        $items = ChatModel::find($query, [], [$sort => $order], $skip, $pageSize);

        foreach ($items as $index => $item) {
            $homeInfo                 = UserModel::findByID(intval($item['to_id']));
            $item['nickname']         = $homeInfo['nickname'];
            $item['headico']          = $homeInfo['headico'];
            $item['device_type']      = $homeInfo['device_type'];
            $item['last_msg_preview'] = htmlspecialchars($item['last_msg_preview'], ENT_QUOTES);

            $item['status']     = $item['last_msg_role'] == 'user' ? '未处理' : '已处理';
            $item['updated_at'] = CommonUtil::ucTimeAgo($item['updated_at']);
            $items[$index]      = $item;
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
     * 会话详情
     * @param                    $id
     * @return mixed
     * @throws BusinessException
     */
    public static function getDetail($id)
    {
        $row = ChatModel::findByID(strval($id));
        if (empty($row)) {
            throw  new BusinessException(StatusCode::DATA_ERROR, '数据不存在!');
        }

        $user                    = UserModel::findByID(intval($row['to_id']));
        $isVip                   = UserService::isVip($user);
        $row['group_name']       = $isVip ? $user['group_name'] : '';
        $row['group_start_time'] = $isVip ? date('Y-m-d H:i', $user['group_start_time']) : '';
        $row['group_end_time']   = $isVip ? date('Y-m-d H:i', $user['group_end_time']) : '';
        $row['nickname']         = $user['nickname'] ?? '';
        $row['device_version']   = $user['device_version'] ?? '';
        $row['device_type']      = $user['device_type'] ?? '';
        $row['headico']          = $user['headico'] ?? '';
        return $row;
    }

    /**
     * @param  array       $request
     * @return array|mixed
     */
    public static function getMessageList($request = [])
    {
        $page     = self::getRequest($request, 'page', 'int', 1);
        $pageSize = self::getRequest($request, 'pageSize', 'int', 15);
        $fromId   = self::getRequest($request, 'from_id', 'string', );
        $toId     = self::getRequest($request, 'to_id', 'string');

        $chatId = ChatService::getChatId($fromId, $toId);

        $skip   = ($page - 1) * $pageSize;
        $fields = [];
        $query  = ['chat_id' => $chatId];
        $items  = ChatMessageModel::find($query, $fields, ['_id' => -1], $skip, $pageSize);
        foreach ($items as $index => $item) {
            $user = UserModel::findByID(intval($item['to_id']));
            if ($item['msg_type'] == 'text') {
                $item['msg_body']['text'] = htmlspecialchars($item['msg_body']['text'], ENT_QUOTES);
            }
            $item['nickname']   = $user['nickname'] ?? '';
            $item['headico']    = $user['headico'] ?? '';
            $item['is_my']      = $item['to_id'] == $fromId ? 'y' : 'n';
            $item['time_label'] = CommonUtil::ucTimeAgo($item['created_at']);
            $items[$index]      = $item;
        }
        return array_reverse($items);
    }

    /**
     * @param                    $fromId
     * @param                    $toId    ,
     * @param                    $msgType
     * @param                    $msgBody
     * @param                    $ext
     * @return array
     * @throws BusinessException
     */
    public static function sendMessage($fromId, $toId, $msgType, $msgBody, $ext)
    {
        return ChatService::sendSingleMessage($fromId, $toId, uniqid(), $msgType, $msgBody, $ext);
    }
}
