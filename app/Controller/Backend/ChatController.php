<?php

declare(strict_types=1);

namespace App\Controller\Backend;

use App\Controller\BaseBackendController;
use App\Repositories\Backend\Common\ChatRepository;
use App\Services\Common\QuickReplyService;

/**
 * Class ChatController
 * @package App\Controller\Backend
 */
class ChatController extends BaseBackendController
{
    /**
     * 初始化
     */
    public function initialize()
    {
        parent::initialize();
    }

    /**
     * 反馈
     */
    public function feedbackAction()
    {
        $this->checkPermission('/chatFeedback');
        if ($this->isPost()) {
            $_REQUEST['from_id'] = 'service';
            $result              = ChatRepository::getList($_REQUEST);
            $this->sendSuccessResult($result);
        }
        $this->view->pick('chat/list');
    }

    /**
     * 会话详情
     * @throws \App\Exception\BusinessException
     */
    public function detailAction()
    {
        $id = $this->getRequest('_id');
        if (!empty($id)) {
            $result = ChatRepository::getDetail($id);
            $this->view->setVar('row', $result);
        }
        $this->view->setVar('quickMessages', QuickReplyService::getAll());
    }

    /**
     * 会话消息列表
     */
    public function messageAction()
    {
        $rows = ChatRepository::getMessageList($_REQUEST);
        $this->sendSuccessResult($rows);
    }

    /**
     * 发送消息
     * @throws \App\Exception\BusinessException
     */
    public function sendAction()
    {
        $token   = $this->getToken();
        $fromId  = $this->getRequest('from_id', 'string');
        $toId    = $this->getRequest('to_id', 'string');
        $content = $_REQUEST['content'];
        $type    = $this->getRequest('type', 'string');
        if (empty($fromId) || empty($toId) || empty($content) || empty($type)) {
            $this->sendErrorResult('参数错误');
        }
        // 消息体
        if ($type == 'text') {
            $msgBody['text'] = $content;
        } elseif ($type == 'image') {
            // TODO lsj库的没有宽高,tx库的有宽高
            // tx
            //            $msgBody = [
            //                'url' => $content['url'],
            //                'width' => $content['width'],
            //                'height' => $content['height'],
            //            ];

            // lsj
            $msgBody = [
                'url' => $content,
                //                'width' => $content['width'],
                //                'height' => $content['height'],
            ];
        }
        $result = ChatRepository::sendMessage($fromId, $toId, $type, $msgBody, '客服:' . $token['username']);
        if ($result) {
            $this->sendSuccessResult();
        }
        $this->sendErrorResult('保存错误!');
    }
}
