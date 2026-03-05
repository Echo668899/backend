<?php

namespace App\Controller\Api;

use App\Controller\BaseApiController;
use App\Repositories\Api\ChatRepository;

class ChatController extends BaseApiController
{
    /**
     * 用户资料
     * 由于消息没返回用户信息,所以需要客户端单独调用
     * @return void
     */
    public function profileAction()
    {
        $userIds = $this->getRequest('ids', 'string');
        $result  = ChatRepository::profile($userIds);
        $this->sendSuccessResult($result);
    }

    /**
     * 会话列表
     * @return void
     */
    public function chatAction()
    {
        $fromId = $this->getUserId();
        $page   = $this->getRequest('page', 'int', 1);
        $inbox  = $this->getRequest('inbox', 'string', 'all');
        $result = ChatRepository::chat($fromId, $inbox, $page);
        $this->sendSuccessResult($result);
    }

    /**
     * 消息列表
     * @return void
     */
    public function messageAction()
    {
        $userId = $this->getUserId();
        // 这里不用chat_id是因为客户端发起聊天的时候要加载列表,新发起的聊天是没有chat_id的
        $toId      = $this->getRequest('to_id', 'string');
        $fromSeqid = $this->getRequest('seqid', 'int', 0);
        $direction = $this->getRequest('direction', 'string', 'before');
        $limit     = $this->getRequest('limit', 'int', 50);

        $result = ChatRepository::message($userId, $toId, $fromSeqid, $direction, $limit);
        $this->sendSuccessResult($result);
    }

    /**
     * 发送私聊消息
     * @return void
     * @throws \App\Exception\BusinessException
     * @throws \Phalcon\Storage\Exception
     */
    public function sendMessageAction()
    {
        $userId  = $this->getUserId();
        $toId    = $this->getRequest('to_id', 'string');
        $msgId   = $this->getRequest('client_msg_id', 'string');
        $msgType = $this->getRequest('msg_type', 'string');
        $msgBody = $_REQUEST['msg_body'] ?? [];
        $result  = ChatRepository::sendMessage($userId, $toId, $msgId, $msgType, $msgBody);
        $this->sendSuccessResult($result);
    }

    /**
     * 删除会话
     */
    public function doDelChatAction()
    {
        $userId  = $this->getUserId();
        $chatIds = $this->getRequest('ids');// 逗号分割的会话id
        $result  = ChatRepository::doDelChat($userId, $chatIds);
        $this->sendSuccessResult($result ? 'y' : 'n');
    }

    /**
     * 删除消息
     * @return void
     */
    public function doDelMessageAction()
    {
        $userId = $this->getUserId();
        $msgIds = $this->getRequest('ids');// 逗号分割的消息id
        $result = ChatRepository::doDelMessage($userId, $msgIds);
        $this->sendSuccessResult($result ? 'y' : 'n');
    }

    /**
     * 撤回消息
     * @return void
     * @throws \App\Exception\BusinessException
     */
    public function doRecallMessageAction()
    {
        $userId = $this->getUserId();
        $msgId  = $this->getRequest('msg_id', 'int');
        $result = ChatRepository::doRecallMessage($userId, $msgId);
        $this->sendSuccessResult($result ? 'y' : 'n');
    }

    /**
     * 阅读消息
     * @return void
     */
    public function doReadMessageAction()
    {
        $userId = $this->getUserId();
        $chatId = $this->getRequest('chat_id', 'string');
        $msgId  = $this->getRequest('msg_id', 'int');
        $result = ChatRepository::doReadMessage($userId, $chatId, $msgId);
        $this->sendSuccessResult($result ? 'y' : 'n');
    }
}
