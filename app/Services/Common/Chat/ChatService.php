<?php

declare(strict_types=1);

namespace App\Services\Common\Chat;

use App\Constants\StatusCode;
use App\Core\Services\BaseService;
use App\Exception\BusinessException;
use App\Models\Common\ChatMessageModel;
use App\Models\Common\ChatModel;
use App\Models\Common\ChatReadModel;
use App\Services\Common\Chat\MessageType\ChatMessage\CallMHandler;
use App\Services\Common\Chat\MessageType\ChatMessage\CallVHandler;
use App\Services\Common\Chat\MessageType\ChatMessage\ImageHandler;
use App\Services\Common\Chat\MessageType\ChatMessage\TextHandler;
use App\Services\Common\Chat\MessageType\ChatMessage\VideoHandler;
use App\Services\Common\Chat\MessageType\InteractNotify\InteractCommentHandler;
use App\Services\Common\Chat\MessageType\InteractNotify\InteractFollowHandler;
use App\Services\Common\Chat\MessageType\InteractNotify\InteractLikeHandler;
use App\Services\Common\Chat\MessageType\InteractNotify\InteractMentionHandler;
use App\Services\Common\Chat\MessageType\SystemNotify\SystemAccountRemoteLoginHandler;
use App\Services\Common\Chat\MessageType\SystemNotify\SystemRechargeHandler;
use App\Services\Common\Chat\MessageType\SystemNotify\SystemVipHandler;
use App\Services\Common\CommonService;
use App\Services\Im\Entity\ImMessageType;
use App\Services\Im\Entity\ImPayloadMessage;
use App\Services\Im\ImService;
use App\Services\Im\Payload\ChatMessageData;
use App\Services\Im\Payload\ChatSystemNotifyData;
use App\Services\User\UserFansService;
use App\Utils\LogUtil;

/**
 * Class ChatService
 */
class ChatService extends BaseService
{
    /**
     * 私聊消息处理器
     * @var string[]
     */
    protected static $singleHandlers = [
        'text'  => TextHandler::class,
        'image' => ImageHandler::class,
        'video' => VideoHandler::class,
        'callv' => CallVHandler::class,
        'callm' => CallMHandler::class,
        //        'recall' => RecallHandler::class,
    ];

    /**
     * 系统消息处理器
     * ImMessageType
     * @var string[]
     */
    protected static $systemHandlers = [
        // 异地登录
        'account.remote_login' => SystemAccountRemoteLoginHandler::class,
        // 会员充值
        'funds.vip' => SystemVipHandler::class,
        // 金币充值
        'funds.recharge' => SystemRechargeHandler::class,
    ];

    /**
     * 互动消息处理器
     * ImMessageType
     * @var string[]
     */
    protected static $interactHandlers = [
        // 评论
        'comment' => InteractCommentHandler::class,
        // 点赞
        'like' => InteractLikeHandler::class,
        // @
        'mention' => InteractMentionHandler::class,
        // 关注
        'follow' => InteractFollowHandler::class,
    ];

    /**
     * 获取会话id
     * @param         $fromId
     * @param         $toId
     * @return string
     */
    public static function getChatId($fromId, $toId)
    {
        $userIds = [$fromId, $toId];
        $chatId  = min($userIds) . '_' . max($userIds);
        return $chatId;
    }

    /**
     * 发送私聊消息,只管发送,上层判断权限
     * @param                    $fromId      //如果是客服,传递service
     * @param                    $toId
     * @param                    $clientMsgId // 客户端生成的临时ID,用于标记消息,客户端异步发送消息回调用
     * @param                    $msgType
     * @param                    $msgBody
     * @param                    $ext
     * @return array
     * @throws BusinessException
     */
    public static function sendSingleMessage($fromId, $toId, $clientMsgId, $msgType, $msgBody, $ext = null)
    {
        $fromId = strval($fromId);// 用字符串
        $toId   = strval($toId);// 用字符串
        $chatId = self::getChatId($fromId, $toId);
        if ($fromId == $toId) {
            throw new BusinessException(StatusCode::PARAMETER_ERROR, '禁止发给自己');
        }
        // 验证发送方和接收方
        if (self::isValidPeerId($fromId, ['service']) == false || self::isValidPeerId($toId, ['service']) == false) {
            throw new BusinessException(StatusCode::PARAMETER_ERROR, '无效用户ID');
        }
        if (!isset(self::$singleHandlers[$msgType])) {
            throw new BusinessException(StatusCode::PARAMETER_ERROR, '暂不支持该消息类型');
        }
        if (is_string($msgBody)) {
            throw new BusinessException(StatusCode::PARAMETER_ERROR, '消息格式错误');
        }

        // /TODO 可选,增加频率判断 给不同用户发送消息超过频率直接封号

        $msgPreview = (self::$singleHandlers[$msgType])::getPreview($msgBody);
        $msgBody    = (self::$singleHandlers[$msgType])::getBody($msgBody);

        //        ///如果是转账消息 需要先转账 后执行消息处理
        //        if ($msgType == 'transfer') {
        //            UserService::doTransfer($fromId,$toId,$msgBody['text']);
        //        }

        // 由接收方获取和发送方的关系
        if ($fromId == 'service' || $toId == 'service') {
            $relation = 'service';
        } else {
            $relation = UserFansService::getRelationStatus($toId, $fromId);
            if ($relation == 'black' || $relation == 'i_black') {
                throw new BusinessException(StatusCode::PARAMETER_ERROR, '对方已拉黑或你已拉黑对方，发送失败');
            }
        }

        // 获取上次seqid
        $lastMsgRow = ChatMessageModel::findFirst(['chat_id' => $chatId], [], ['_id' => -1]);
        $seqid      = $lastMsgRow ? $lastMsgRow['seqid'] + 1 : 1;
        // 写入消息
        $msgId = ChatMessageModel::insert([
            'chat_id'     => $chatId,
            'from_id'     => $fromId,
            'to_id'       => $toId,
            'seqid'       => $seqid,
            'msg_type'    => $msgType,
            'msg_body'    => $msgBody,
            'is_recalled' => 0,
            'deleted_by'  => [],
            'ext'         => $ext,
        ]);
        /**
         * 写入会话-发送方
         * */
        $chatFromId = $chatId . '_' . $fromId;
        $fromChat   = ChatModel::findAndModify(['_id' => $chatFromId], [
            '$set' => [
                '_id'       => $chatFromId,
                'chat_id'   => $chatId,
                'chat_type' => value(function () use ($fromId, $toId) {
                    if ($fromId == 'service' || $toId == 'service') {
                        return 'service';
                    }
                    return 'single';
                }),
                'from_id'          => $fromId,
                'to_id'            => $toId,
                'last_msg_id'      => $msgId,
                'last_msg_seqid'   => $seqid,
                'last_msg_preview' => $msgPreview,
                'last_msg_role'    => value(function () use ($fromId, $toId) {
                    if ($fromId == 'service') {
                        return 'service';
                    }
                    return 'user';
                }),
                'inbox' => value(function () use ($fromId, $toId) {
                    if ($fromId == 'service' || $toId == 'service') {
                        return 'service';
                    }
                    return 'main';// 发送方写入主收件箱
                }),
                'unread_count' => 0, // 自己未读0
                'updated_at'   => time(),
            ],
            '$setOnInsert' => [
                'top'        => 0,
                'created_at' => time()
            ]
        ], [], true);

        /**
         * 写入会话-接收方
         * 计算未读计数 chat_message.seqid-chat_read.last_msg_seqid
         * */
        $chatToId        = $chatId . '_' . $toId;
        $toChatRead      = ChatReadModel::findByID($chatToId);
        $toChatReadSeqid = $toChatRead ? $toChatRead['last_read_seqid'] : 0;

        $toChat = ChatModel::findAndModify(['_id' => $chatToId], [
            '$set' => [
                '_id'       => $chatToId,
                'chat_id'   => $chatId,
                'chat_type' => value(function () use ($fromId, $toId) {
                    if ($fromId == 'service' || $toId == 'service') {
                        return 'service';
                    }
                    return 'single';
                }),
                'from_id'          => $toId,
                'to_id'            => $fromId,
                'last_msg_id'      => $msgId,
                'last_msg_seqid'   => $seqid,
                'last_msg_preview' => $msgPreview,
                'last_msg_role'    => value(function () use ($fromId, $toId) {
                    if ($fromId == 'service') {
                        return 'service';
                    }
                    return 'user';
                }),
                // 接收方根据关系写入不同收件箱
                'inbox' => value(function () use ($relation) {
                    switch ($relation) {
                        case 'service':
                            return 'service';
                        case 'mutual':
                        case 'follow':
                            return 'main';
                        case 'followed_by':
                        case 'none':
                            return'request';
                    }
                    return 'main';
                }),
                'unread_count' => max(0, $seqid - $toChatReadSeqid),
                'updated_at'   => time(),
            ],
            '$setOnInsert' => [
                'top'        => 0,
                'created_at' => time()
            ]
        ], [], true);

        // 通话发送ice
        if (in_array($msgType, ['callv', 'callm'])) {
            $iceService     = self::getIceServers();
            $msgBody['ice'] = $iceService;
        }
        // /加入job 等待视频转码完成延迟发送 UploadFindJob.chat
        if ($msgType != 'video') {
            try {
                if ($toId != 'service') {
                    if ($msgType == 'image') {
                        $msgBody['url'] = CommonService::getCdnUrl($msgBody['url']);
                    }
                    // /发给接收方
                    ImService::sendToUser(ImService::ACTION_SEND_TO_USER, $fromId, $toId, $msgId, new ImPayloadMessage(
                        ImMessageType::CHAT_MESSAGE,
                        new ChatMessageData(
                            $chatId,
                            $fromId,
                            $toId,
                            $msgId,
                            $seqid,
                            $msgType,
                            $msgBody,
                            $msgPreview,
                            $toChatRead['last_read_id'],
                            $toChatReadSeqid,
                            $toChat['unread_count'],
                            time()
                        ),
                    ));

                    // /多端同步,发给发送方,callm和callv是特殊事件,如果推送会进入客户端websocket事件,发起方和接收方都是自己
                    if (!in_array($msgType, ['callm', 'callv'])) {
                        ImService::sendToUser(ImService::ACTION_SEND_TO_USER, $toId, $fromId, $msgId, new ImPayloadMessage(
                            ImMessageType::CHAT_MESSAGE,
                            new ChatMessageData(
                                $chatId,
                                $fromId,
                                $toId,
                                $msgId,
                                $seqid,
                                $msgType,
                                $msgBody,
                                $msgPreview,
                                $msgId,
                                $seqid,
                                '0',
                                time()
                            ),
                        ));
                    }
                }
            } catch (\Exception $e) {
                LogUtil::error(sprintf('%s in %s line %s', $e->getMessage(), $e->getFile(), $e->getLine()));
            }
        }

        return [
            'client_msg_id' => strval($clientMsgId),
            'chat_id'       => strval($chatId),
            'chat'          => [
                'chat_id'      => strval($chatId),
                'chat_type'    => 'single',
                'from_id'      => strval($fromChat['from_id']),
                'to_id'        => strval($fromChat['to_id']),
                'inbox'        => strval($fromChat['inbox']),
                'unread_count' => strval($fromChat['unread_count']),
            ],
            'message' => [
                'msg_id'    => strval($msgId),
                'chat_id'   => strval($chatId),
                'from_id'   => strval($fromChat['from_id']),
                'to_id'     => strval($fromChat['to_id']),
                'seqid'     => strval($seqid),
                'msg_type'  => strval($msgType),
                'msg_body'  => $msgBody,
                'timestamp' => strval(time())
            ],
        ];
    }

    /**
     * 发送系统消息(系统→用户)
     * 仅用于系统通知,进入系统收件箱
     * @param  int               $toId    接收用户ID
     * @param  string            $msgType 消息类型 text/image/notify
     * @param  array|string      $msgBody 消息体
     * @param  array|null        $ext     扩展字段
     * @return bool
     * @throws BusinessException
     */
    public static function sendSystemMessage($toId, $msgType, $msgBody, $ext = null)
    {
        $fromId = 'system'; // 系统用户ID固定为system
        $toId   = strval($toId);// 用字符串
        $chatId = 'system_' . $toId;

        if (self::isValidPeerId($toId, []) == false) {
            throw new BusinessException(StatusCode::PARAMETER_ERROR, '无效用户ID');
        }

        if (!isset(self::$systemHandlers[$msgType])) {
            throw new BusinessException(StatusCode::PARAMETER_ERROR, '暂不支持该消息类型');
        }

        if (is_string($msgBody)) {
            throw new BusinessException(StatusCode::PARAMETER_ERROR, '消息格式错误');
        }

        $msgPreview = (self::$systemHandlers[$msgType])::getPreview($msgBody);
        $msgBody    = (self::$systemHandlers[$msgType])::getBody($msgBody);

        // 获取上次 seqid
        $lastMsgRow = ChatMessageModel::findFirst(['chat_id' => $chatId], [], ['_id' => -1]);
        $seqid      = $lastMsgRow ? ($lastMsgRow['seqid'] + 1) : 1;

        // 写入消息表
        $msgId = ChatMessageModel::insert([
            'chat_id'     => $chatId,
            'from_id'     => $fromId,
            'to_id'       => $toId,
            'seqid'       => $seqid,
            'msg_type'    => $msgType,
            'msg_body'    => $msgBody,
            'is_recalled' => 0,
            'deleted_by'  => [],
            'ext'         => $ext,
        ]);

        /**
         * 写入会话-接收方
         * 计算未读计数 chat_message.seqid-chat_read.last_msg_seqid
         * */
        $chatToId        = $chatId . '_' . $toId;
        $toChatRead      = ChatReadModel::findByID($chatToId);
        $toChatReadSeqid = $toChatRead ? $toChatRead['last_read_seqid'] : 0;

        // 系统消息只写接收方会话
        $toChat = ChatModel::findAndModify(['_id' => $chatToId], [
            '$set' => [
                '_id'              => $chatToId,
                'chat_id'          => $chatId,
                'chat_type'        => 'system',
                'from_id'          => $toId,
                'to_id'            => $fromId,
                'last_msg_id'      => $msgId,
                'last_msg_seqid'   => $seqid,
                'last_msg_preview' => $msgPreview,
                'last_msg_role'    => 'system',
                'inbox'            => 'system',
                'unread_count'     => max(0, $seqid - $toChatReadSeqid),
                'updated_at'       => time(),
            ],
            '$setOnInsert' => [
                'top'        => 0,
                'created_at' => time()
            ]
        ], [], true, true);

        try {
            $messageData = new ChatSystemNotifyData(
                $chatId,
                $fromId,
                $toId,
                $msgId,
                $seqid,
                $msgType,
                $msgBody,
                $msgPreview,
                $toChatRead['last_read_id'],
                $toChatReadSeqid,
                $toChat['unread_count'],
                time()
            );

            $payload = new ImPayloadMessage(
                ImMessageType::SYSTEM_NOTIFY,
                $messageData,
            );

            ImService::sendToUser(ImService::ACTION_SEND_TO_USER, $fromId, $toId, $msgId, $payload);
        } catch (\Exception $e) {
        }
        return true;
    }

    /**
     * 发送交互通知(系统代发, 用于用户行为提醒)
     * @param  string            $toId    接收方用户ID
     * @param  string            $msgType 通知类型: comment|like|mention|follow|danmaku
     * @param  array             $msgBody 消息体
     * @param  array|null        $ext     扩展字段
     * @return bool
     * @throws BusinessException
     */
    public static function sendInteractNotify($toId, $msgType, array $msgBody, $ext = null)
    {
        $fromId = 'interact'; // 系统用户ID固定为interact
        $toId   = strval($toId);// 用字符串
        $chatId = 'interact_' . $toId;

        if (self::isValidPeerId($toId, []) == false) {
            throw new BusinessException(StatusCode::PARAMETER_ERROR, '无效用户ID');
        }
        if (!isset(self::$interactHandlers[$msgType])) {
            throw new BusinessException(StatusCode::PARAMETER_ERROR, '暂不支持该消息类型');
        }
        if (is_string($msgBody)) {
            throw new BusinessException(StatusCode::PARAMETER_ERROR, '消息格式错误');
        }

        $msgPreview = (self::$interactHandlers[$msgType])::getPreview($msgBody);
        $msgBody    = (self::$interactHandlers[$msgType])::getBody($msgBody);

        // 特殊逻辑,关注无需入库
        if ($msgType == 'follow') {
            $seqid                  = 0;
            $msgId                  = time();
            $toChatRead             = '';
            $toChatReadSeqid        = 0;
            $toChat['unread_count'] = 0;
        } else {
            // 获取上次 seqid
            $lastMsgRow = ChatMessageModel::findFirst(['chat_id' => $chatId], [], ['_id' => -1]);
            $seqid      = $lastMsgRow ? ($lastMsgRow['seqid'] + 1) : 1;

            // 写入消息表
            $msgId = ChatMessageModel::insert([
                'chat_id'     => $chatId,
                'from_id'     => $fromId,
                'to_id'       => $toId,
                'seqid'       => $seqid,
                'msg_type'    => $msgType,
                'msg_body'    => $msgBody,
                'is_recalled' => 0,
                'deleted_by'  => [],
                'ext'         => $ext,
            ]);

            /**
             * 写入会话-接收方
             * 计算未读计数 chat_message.seqid-chat_read.last_msg_seqid
             * */
            $chatToId        = $chatId . '_' . $toId;
            $toChatRead      = ChatReadModel::findByID($chatToId);
            $toChatReadSeqid = $toChatRead ? $toChatRead['last_read_seqid'] : 0;

            // 系统消息只写接收方会话
            $toChat = ChatModel::findAndModify(['_id' => $chatToId], [
                '$set' => [
                    '_id'              => $chatToId,
                    'chat_id'          => $chatId,
                    'chat_type'        => 'system',
                    'from_id'          => $toId,
                    'to_id'            => $fromId,
                    'last_msg_id'      => $msgId,
                    'last_msg_seqid'   => $seqid,
                    'last_msg_preview' => $msgPreview,
                    'last_msg_role'    => 'system',
                    'inbox'            => 'system',
                    'unread_count'     => max(0, $seqid - $toChatReadSeqid),
                    'updated_at'       => time(),
                ],
                '$setOnInsert' => [
                    'top'        => 0,
                    'created_at' => time()
                ]
            ], [], true, true);
        }

        try {
            $messageData = new \App\Services\Im\Payload\ChatInteractNotifyData(
                $chatId,
                $fromId,
                $toId,
                $msgId,
                $seqid,
                $msgType,
                $msgBody,
                $msgPreview,
                $toChatRead['last_read_id'],
                $toChatReadSeqid,
                $toChat['unread_count'],
                time()
            );

            $payload = new ImPayloadMessage(
                ImMessageType::INTERACT_NOTIFY,
                $messageData
            );

            ImService::sendToUser(ImService::ACTION_SEND_TO_USER, $fromId, $toId, $msgId, $payload);
        } catch (\Exception $e) {
            LogUtil::error(sprintf('%s in %s line %s', $e->getMessage(), $e->getFile(), $e->getLine()));
        }

        return true;
    }

    /**
     * 阅读消息
     * 分两个阶段调用,
     * 详情中定时上报(如果每阅读一个就调用接口,太过于频繁,定时5秒上报一次最新阅读到的消息id)
     * 退出会话详情的时候
     * @param       $userId
     * @param       $chatId
     * @param       $lastMsgId
     * @return bool
     */
    public static function doReadMessage($userId, $chatId, $lastMsgId)
    {
        $lastMsgId = intval($lastMsgId);
        $userId    = strval($userId);
        $chatId    = strval($chatId);

        $chatMessageRow = ChatMessageModel::findByID($lastMsgId);
        if (empty($chatMessageRow)) {
            return false;
        }

        if ($chatMessageRow['from_id'] != $userId && $chatMessageRow['to_id'] != $userId) {
            return false;
        }

        $_id = $chatId . '_' . $userId;
        // 更新我的游标
        $chatReadRow = ChatReadModel::findAndModify(
            [
                '_id' => $_id,
            ],
            [
                '$set' => [
                    'updated_at' => time(),
                ],
                // $max 确保游标只前进不后退
                '$max' => [
                    'last_read_id'    => intval($chatMessageRow['_id']),
                    'last_read_seqid' => intval($chatMessageRow['seqid']),
                ],
                '$setOnInsert' => [
                    '_id'     => $_id,
                    'chat_id' => strval($chatId),
                    'user_id' => $userId,
                ]
            ],
            [],
            true,
            true
        );

        // 更新会话计数
        $chatRow = ChatModel::findByID($_id);
        ChatModel::findAndModify(
            [
                '_id' => $_id
            ],
            [
                '$set' => [
                    'unread_count' => max(0, $chatRow['last_msg_seqid'] - $chatReadRow['last_read_seqid']),
                ]
            ]
        );
        return true;
    }

    /**
     * 删除会话
     * @param       $userId
     * @param       $chatIds
     * @return true
     */
    public static function doDelChat($userId, $chatIds)
    {
        $userId = strval($userId);
        $ids    = explode(',', $chatIds);
        foreach ($ids as $key => $id) {
            if (empty($id)) {
                unset($ids[$key]);
            }
        }

        if (!empty($ids)) {
            $ids = array_values($ids);
            ChatModel::delete([
                '_id' => [
                    '$in' => value(function () use ($ids, $userId) {
                        foreach ($ids as &$cid) {
                            $cid = $cid . '_' . $userId;
                            unset($cid);
                        }
                        return $ids;
                    })
                ]
            ]);

            // 同步删除消息
            ChatMessageModel::updateRaw(
                [
                    '$addToSet' => [
                        'deleted_by' => $userId
                    ]
                ],
                [
                    'chat_id' => [
                        '$in' => $ids
                    ]
                ]
            );
        }
        return true;
    }

    /**
     * 删除消息
     * @param       $userId
     * @param       $msgIds //逗号分割的消息ids
     * @return bool
     */
    public static function doDelMessage($userId, $msgIds)
    {
        $userId = strval($userId);
        $ids    = explode(',', $msgIds);
        foreach ($ids as $key => $id) {
            if (empty($id)) {
                unset($ids[$key]);
            }
        }
        if (!empty($ids)) {
            $ids = array_map('intval', $ids);
            $ids = array_values($ids);
            ChatMessageModel::updateRaw(
                [
                    '$addToSet' => [
                        'deleted_by' => $userId
                    ]
                ],
                [
                    '_id' => [
                        '$in' => $ids
                    ],
                ]
            );
        }
        return true;
    }

    /**
     * 撤回消息
     * @param                    $userId
     * @param                    $msgId
     * @return bool
     * @throws BusinessException
     */
    public static function doRecallMessage($userId, $msgId)
    {
        $messageRow = ChatMessageModel::findByID(intval($msgId));
        if (empty($messageRow) || $messageRow['from_id'] != $userId && $messageRow['to_id'] != $userId) {
            return false;
        }
        if ($messageRow['from_id'] != $userId) {
            throw new BusinessException(StatusCode::PARAMETER_ERROR, '只能撤回自己发送的消息');
        }
        $timeoutSeconds = 60 * 5;
        if (time() - $messageRow['created_at'] > $timeoutSeconds) {
            throw new BusinessException(StatusCode::PARAMETER_ERROR, '只能撤回5分钟内的消息');
        }

        $chatId = $messageRow['chat_id'];
        $fromId = $messageRow['from_id'];
        $toId   = $messageRow['to_id'];

        // 检查这条消息是否是双方 chat 的最后一条
        $chatFrom = ChatModel::findById($chatId . '_' . $fromId);
        $chatTo   = ChatModel::findById($chatId . '_' . $toId);

        if ($chatFrom && $chatFrom['last_msg_id'] == $msgId) {
            ChatModel::findAndModify(
                ['_id' => $chatId . '_' . $fromId],
                ['$set' => ['last_msg_preview' => '您撤回了一条消息']],
                [],
                false,
                true
            );
        }

        if ($chatTo && $chatTo['last_msg_id'] == $msgId) {
            ChatModel::findAndModify(
                ['_id' => $chatId . '_' . $toId],
                ['$set' => ['last_msg_preview' => '对方撤回了一条消息']],
                [],
                false,
                true
            );
        }

        $result = ChatMessageModel::updateById(['is_recalled' => 1], $msgId);
        try {
            //            $body = self::checkMsgBody('recall', []);
            //            ImService::sendToUser($fromId, $messageRow['to_id'], $msgId, $body);
        } catch (\Exception $e) {
        }
        return $result;
    }

    /**
     * 获取未读消息总数
     * @param            $userId
     * @return int|mixed
     */
    public static function getUnreadCount($userId)
    {
        $count = ChatModel::aggregate([
            [
                '$match' => [
                    'from_id' => strval($userId),
                ]
            ],
            [
                '$group' => [
                    '_id'   => null,
                    'count' => ['$sum' => '$unread_count'],
                ]
            ]
        ]);
        return $count['count'] ?? 0;
    }

    /**
     * 验证id
     * @param  string $id
     * @param  mixed  $ids
     * @return bool
     */
    private static function isValidPeerId($id, $ids = []): bool
    {
        return in_array($id, $ids) || preg_match('/^\d+$/', $id);
    }

    /**
     * @return array
     */
    private static function getIceServers()
    {
        $result = [];
        foreach (env()->toArray() as $key => $config) {
            if (strpos($key, 'imserver.ice.') === false) {
                continue;
            }
            $result[] = ['urls' => strval($config['turn']), 'username' => strval($config['username']), 'credential' => strval($config['password'])];
            $result[] = ['urls' => strval($config['stun'])];
        }
        return $result;
    }
}
