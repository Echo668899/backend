<?php

namespace App\Repositories\Api;

use App\Constants\StatusCode;
use App\Core\Repositories\BaseRepository;
use App\Exception\BusinessException;
use App\Models\Common\ChatMessageModel;
use App\Models\Common\ChatModel;
use App\Models\Common\ChatReadModel;
use App\Services\Common\ArticleService;
use App\Services\Common\Chat\ChatService;
use App\Services\Common\CommonService;
use App\Services\Common\IpService;
use App\Services\Common\M3u8Service;
use App\Services\User\UserService;
use App\Utils\CommonUtil;

class ChatRepository extends BaseRepository
{
    /**
     * 用户资料
     * 由于消息没返回用户信息,所以需要客户端单独调用
     * @param        $userIds
     * @return array
     */
    public static function profile($userIds)
    {
        $ids = explode(',', $userIds);
        foreach ($ids as $key => $id) {
            if (empty($id)) {
                unset($ids[$key]);
            }
        }
        $result = [];
        if (!empty($ids)) {
            foreach ($ids as $id) {
                $userInfo    = UserService::getInfoFromCache($id);
                $result[$id] = [
                    'id'          => strval($userInfo['id']),
                    'username'    => strval($userInfo['username']),
                    'nickname'    => strval($userInfo['nickname']),
                    'headico'     => strval(CommonService::getCdnUrl($userInfo['headico'])),
                    'is_vip'      => strval($userInfo['is_vip']),
                    'is_up'       => strval($userInfo['is_up']),
                    'is_mer'      => strval($userInfo['is_mer']),
                    'is_official' => strval($userInfo['is_official']),
                    'online'      => strval($userInfo['online']),
                ];
            }
        }
        return $result;
    }

    /**
     * 会话列表
     * @param        $userId
     * @param        $inbox
     * @param        $page
     * @param        $pageSize
     * @return array
     */
    public static function chat($userId, $inbox, $page = 1, $pageSize = 50)
    {
        $userId = strval($userId);// 用字符串
        $query  = [
            'from_id' => $userId,
            'inbox'   => strval($inbox),
        ];
        if ($inbox == 'all') {
            unset($query['inbox']);
        }
        $count = ChatModel::count($query);
        $rows  = ChatModel::find($query, [], ['updated_at' => -1], ($page - 1) * $pageSize, $pageSize);

        // 批量取chat_read游标
        $readMap = [];
        if (!empty($rows)) {
            $readRows = ChatReadModel::find(['chat_id' => ['$in' => array_column($rows, 'chat_id')], 'user_id' => $userId], [], [], 0, count($rows));
            foreach ($readRows as $r) {
                $readMap[$r['_id']] = $r;
            }
        }

        $fromInfo = UserService::getInfoFromCache($userId);
        foreach ($rows as &$row) {
            $toInfo = UserService::getInfoFromCache($row['to_id']);

            $lastRead = $readMap[$row['_id']] ?? [];
            $row      = [
                'chat_type' => strval($row['chat_type']),
                'chat_id'   => strval($row['chat_id']),

                'from_user' => value(function () use ($fromInfo) {
                    return [
                        'id'          => strval($fromInfo['id']),
                        'username'    => strval($fromInfo['username']),
                        'nickname'    => strval($fromInfo['nickname']),
                        'is_up'       => strval($fromInfo['is_up']),
                        'is_vip'      => strval($fromInfo['is_vip']),
                        'is_mer'      => strval($fromInfo['is_mer']),
                        'is_official' => strval($fromInfo['is_official']),
                        'headico'     => strval(CommonService::getCdnUrl($fromInfo['headico'])),
                    ];
                }),
                'to_user' => value(function () use ($toInfo) {
                    return [
                        'id'          => strval($toInfo['id']),
                        'username'    => strval($toInfo['username']),
                        'nickname'    => strval($toInfo['nickname']),
                        'is_up'       => strval($toInfo['is_up']),
                        'is_vip'      => strval($toInfo['is_vip']),
                        'is_mer'      => strval($toInfo['is_mer']),
                        'is_official' => strval($toInfo['is_official']),
                        'headico'     => strval(CommonService::getCdnUrl($toInfo['headico'])),
                    ];
                }),

                'last_msg_id'    => strval($row['last_msg_id']),
                'last_msg_seqid' => strval($row['last_msg_seqid']),
                'preview'        => strval(htmlspecialchars($row['last_msg_preview'], ENT_QUOTES)),

                'last_read_id'    => strval($lastRead['last_read_id'] ?? ''),
                'last_read_seqid' => strval($lastRead['last_read_seqid'] ?? '0'),

                'unread_count' => strval($row['unread_count']),
                'timestamp'    => strval($row['updated_at']),
            ];
            unset($row);
        }
        return [
            'data'         => $rows,
            'total'        => strval($count),
            'current_page' => strval($page),
            'page_size'    => strval($pageSize),
            'last_page'    => strval(ceil($count / $pageSize))
        ];
    }

    /**
     * 消息列表
     * @param        $userId
     * @param        $toId
     * @param        $fromSeqid //基准seqid
     * @param        $direction //方向 before,加载$fromSeqid之前的数据,after加载$fromSeqid之后的数据
     * @param        $limit
     * @return array
     */
    public static function message($userId, $toId, $fromSeqid = 0, $direction = 'before', $limit = 50)
    {
        $userId    = strval($userId);
        $chatId    = ChatService::getChatId($userId, $toId);
        $fromSeqid = intval($fromSeqid);
        $limit     = max(1, min($limit, 100)); // 防止过大分页

        $query = ['chat_id' => $chatId];

        // 方向控制
        if ($fromSeqid > 0) {
            if ($direction === 'before') {
                // /before 手势从顶部到底部-加载旧消息
                $query['seqid'] = [
                    '$lt' => $fromSeqid
                ];
                $sort = ['seqid' => -1];// 旧消息需要基于当前seqid倒序返回
            } else {
                // /after 手势从底部到顶部-加载新消息
                $query['seqid'] = [
                    '$gt' => $fromSeqid
                ];
                $sort = ['seqid' => 1];
            }
        } else {
            // 初次加载，默认拉最新 limit 条
            $sort = ['seqid' => -1];
        }
        // 排除我删除的消息
        $query['deleted_by'] = ['$ne' => $userId];

        $rows = ChatMessageModel::find($query, [], $sort, 0, $limit + 1);// 多取一条来判断是否还有上下页

        $hasMoreBefore = 0;
        $hasMoreAfter  = 0;

        if ($direction === 'before' || $fromSeqid === 0) {
            /**
             * 旧消息
             * 首次加载：结果是降序，需要反转回升序
             * 多取的第一条代表还有更旧
             */
            if (count($rows) > $limit) {
                $hasMoreBefore = 1;
                array_pop($rows);  // 去掉最旧那条
            }
            $rows = array_reverse($rows); // 升序返回
        } else {
            /**
             * 新消息
             * 结果是升序，无需反转
             * 多取的最后一条代表还有更新
             */
            if (count($rows) > $limit) {
                $hasMoreAfter = 1;
                array_shift($rows); // 去掉第一条多余的
            }
        }

        $isChina = IpService::isChina(CommonUtil::getClientIp());

        foreach ($rows as &$row) {
            $row = [
                'id'       => strval($row['_id']),
                'chat_id'  => strval($row['chat_id']),
                'from_id'  => strval($row['from_id']),
                'is_me'    => $userId == $row['from_id'] ? 'y' : 'n',
                'seqid'    => strval($row['seqid']),
                'msg_type' => value(function () use ($row) {
                    if ($row['is_recalled'] == 1) {
                        return 'recall';
                    }
                    return strval($row['msg_type']);
                }),
                'msg_body' => value(function () use ($row, $userId, $isChina) {
                    if ($row['is_recalled'] == 1) {
                        $row['msg_body']['text'] = $userId == $row['from_id'] ? '您撤回了一条消息' : '对方撤回了一条消息';
                    } elseif ($row['msg_type'] == 'image') {
                        $row['msg_body']['url'] = CommonService::getCdnUrl($row['msg_body']['url']);
                    } elseif ($row['msg_type'] == 'video') {
                        $row['msg_body']['img'] = CommonService::getCdnUrl($row['msg_body']['img']);
                        $row['msg_body']['url'] = M3u8Service::encode($row['msg_body']['url'], $isChina ? 'tencent' : 'aws');
                    } elseif ($row['msg_type'] == 'text') {
                        $row['msg_body']['text'] = htmlspecialchars($row['msg_body']['text'], ENT_QUOTES);
                    }
                    return $row['msg_body'];
                }),
                'timestamp' => strval($row['created_at']), // 前端计算是否显示时间用
                'label'     => strval(CommonUtil::formatChatTime($row['created_at'])), // 前端显示用
            ];
            unset($row);
        }

        return [
            'data'            => $rows,
            'has_more_before' => $hasMoreBefore ? 'y' : 'n',
            'has_more_after'  => $hasMoreAfter ? 'y' : 'n',
            'faq'             => value(function () use ($toId) {
                $result = [];
                // 如果是客服,插入faq
                if ($toId == 'service') {
                    $faqs = ArticleService::getArticleList('faq', 1, 15)['data'];
                    foreach ($faqs as $faq) {
                        $result[] = [
                            'id'      => strval($faq['id']),
                            'img'     => strval(CommonService::getCdnUrl($faq['img'])),
                            'title'   => strval($faq['title']),
                            'content' => strval($faq['content']),
                        ];
                    }
                }
                return $result;
            })
        ];
    }

    /**
     * 发送消息
     * @param                             $fromId
     * @param                             $toId
     * @param                             $msgType
     * @param                             $msgBody
     * @param  mixed                      $msgId
     * @return array
     * @throws BusinessException
     * @throws \Phalcon\Storage\Exception
     */
    public static function sendMessage($fromId, $toId, $msgId, $msgType, $msgBody)
    {
        if (empty($toId) || empty($msgId)) {
            throw new BusinessException(StatusCode::PARAMETER_ERROR, '参数错误');
        }
        // /在这里做权限判断
        $fromInfo = UserService::getInfoFromCache($fromId);
        UserService::checkDisabled($fromInfo);
        if ($toId != 'service') {
            $toInfo = UserService::getInfoFromCache($toId);
            UserService::checkDisabled($toInfo);

            if (!in_array('do_chat', UserService::getRights($fromInfo))) {
                throw new BusinessException(StatusCode::DATA_ERROR, '您没有权限发送私信!');
            }
            if (in_array($msgType, ['callv', 'callm']) && !in_array('do_chat_call', UserService::getRights($fromInfo))) {
                throw new BusinessException(StatusCode::DATA_ERROR, '您没有权限发送音视频通话!');
            }
        }

        return ChatService::sendSingleMessage($fromId, $toId, $msgId, $msgType, $msgBody);
    }

    /**
     * 阅读消息
     * @param       $userId
     * @param       $chatId
     * @param       $lastMsgId
     * @return bool
     */
    public static function doReadMessage($userId, $chatId, $lastMsgId)
    {
        return ChatService::doReadMessage($userId, $chatId, $lastMsgId);
    }

    /**
     * 删除会话
     * @param       $userId
     * @param       $chatIds
     * @return bool
     */
    public static function doDelChat($userId, $chatIds)
    {
        return ChatService::doDelChat($userId, $chatIds);
    }

    /**
     * 删除消息
     * @param       $userId
     * @param       $msgIds
     * @return bool
     */
    public static function doDelMessage($userId, $msgIds)
    {
        return ChatService::doDelMessage($userId, $msgIds);
    }

    /**
     * 撤回消息
     * @param                                   $fromId
     * @param                                   $msgId
     * @param  mixed                            $userId
     * @return bool|mixed
     * @throws \App\Exception\BusinessException
     */
    public static function doRecallMessage($userId, $msgId)
    {
        return ChatService::doRecallMessage($userId, $msgId);
    }
}
