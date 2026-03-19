<?php

namespace App\Controller\Api;

use App\Controller\BaseApiController;
use App\Exception\BusinessException;
use App\Repositories\Api\UserRepository;
use App\Repositories\Api\MovieCusRepository;

class UserController extends BaseApiController
{

    /**
     * up主内容列表
     * @return void
     */
    public function upFilterAction(){
        //long_video short_video cartoon comics novel posts
        $type = $this->getRequest('type', 'string', 'long_video');
        $userId = $this->getUserId();
        $order = $this->getRequest('order', 'string', 'hot');
        $page = $this->getRequest('page', 'int', 1);
        $pageSize = $this->getRequest('pageSize', 'int', 12);

        $res = MovieCusRepository::getUpContent($userId, $type, $order, $page, $pageSize);
        $this->sendSuccessResult($res);
    }

    /**
     * 个人信息
     * @return void
     */
    public function infoAction()
    {
        $userId = $this->getUserId();
        $result = UserRepository::getInfo($userId);
        $this->sendSuccessResult($result);
    }

    /**
     * 个人主页
     * @return void
     * @throws \App\Exception\BusinessException
     */
    public function homeAction()
    {
        $userId = $this->getUserId(false);
        $homeId = $this->getRequest('home_id');
        $result = UserRepository::getHome($userId, $homeId);
        $this->sendSuccessResult($result);
    }

    /**
     * 创作者
     * @return void
     */
    public function creatorAction()
    {
        $userId = $this->getUserId(true);
        $result = UserRepository::getCreator($userId);
        $this->sendSuccessResult($result);
    }


    /**
     * 头像库
     * @return void
     */
    public function imagesAction()
    {
        $result = UserRepository::getHeadImages();
        $this->sendSuccessResult($result);
    }

    /**
     * 批量更新
     * @return void
     * @throws BusinessException
     * @throws \Phalcon\Storage\Exception
     */
    public function doUpdateAction()
    {
        $userId = $this->getUserId();
        $fields = $this->getRequest('fields');
        UserRepository::doMultipleUpdate($userId, $fields);
        $this->sendSuccessResult();
    }

    /**
     * 修改密码
     * @return void
     * @throws \App\Exception\BusinessException
     */
    public function passwordAction()
    {
        $userId = $this->getUserId();
        $oldPassword = $this->getRequest('old_password', 'string');
        $newPassword = $this->getRequest('new_password', 'string');
        $result = UserRepository::changePassword($userId, $oldPassword, $newPassword);
        $this->sendSuccessResult($result);
    }

    /**
     * 用户关注
     */
    public function doFollowAction()
    {
        $userId = $this->getUserId();
        $homeId = $this->getRequest('home_id');
        $action = $this->getRequest('action','string','follow');
        if (empty($homeId) || $homeId < 1) {
            $this->sendErrorResult('请检查参数!');
        }
        $result = UserRepository::doFollow($userId, $homeId,$action);
        $this->sendSuccessResult(['status'=>$result]);
    }

    /**
     * 关注列表
     */
    public function followAction()
    {
        $userId = $this->getUserId();
        $page = $this->getRequest('page', 'int', 1);
        $homeId = $this->getRequest('home_id', 'string');
        $action = $this->getRequest('action','string','follow');
        $cursor = $this->getRequest('cursor', 'string');
        if (empty($homeId)) {
            $homeId = $userId;
        }
        $result = UserRepository::getFollowList($userId, $homeId,$action, $page,$cursor);
        $this->sendSuccessResult($result);
    }

    /**
     * 粉丝列表
     */
    public function fansAction()
    {
        $userId = $this->getUserId();
        $page = $this->getRequest('page', 'int', 1);
        $homeId = $this->getRequest('home_id', 'string');
        $cursor = $this->getRequest('cursor', 'string');
        if (empty($homeId)) {
            $homeId = $userId;
        }
        $result = UserRepository::getFansList($userId, $homeId,$page,$cursor);
        $this->sendSuccessResult($result);
    }

    /**
     * 收藏板块
     * @return void
     * @throws BusinessException
     */
    public function doFavoriteAction()
    {
        $userId = $this->getUserId();
        $objectType = $this->getRequest('object_type', 'string', '');
        $objectId = $this->getRequest('object_id', 'string', '');
        $result = UserRepository::doFavorite($userId, $objectType,$objectId);
        $this->sendSuccessResult($result?'y':'n');
    }

    /**
     * 收藏的板块列表
     * @return void
     */
    public function favoriteAction()
    {
        $userId = $this->getUserId();
        $objectType = $this->getRequest('object_type', 'string', '');
        $page = $this->getRequest('page', 'int', 1);
        $result = UserRepository::favorite($userId, $objectType,$page);
        $this->sendSuccessResult($result);
    }

    /**
     * 会员页面
     */
    public function vipAction()
    {
        $userId = $this->getUserId();
        $group = $this->getRequest('group', 'string', 'normal');
        $result = UserRepository::vipInfo($userId, $group);
        $this->sendSuccessResult($result);
    }

    /**
     * 去购买
     * @throws BusinessException
     */
    public function doVipAction()
    {
        $userId = $this->getUserId();
        $groupId = $this->getRequest('group_id', 'int');
        $paymentId = $this->getRequest('payment_id', 'string');
        if (empty($groupId)) {
            $this->sendErrorResult('请选择购买套餐!');
        }
        if (empty($paymentId)) {
            $this->sendErrorResult('请选择正确支付方式!');
        }
        $result = UserRepository::doVip($userId, $groupId, $paymentId);
        $this->sendSuccessResult($result);
    }

    /**
     * 金币充值
     * @throws BusinessException
     */
    public function rechargeAction()
    {
        $userId = $this->getUserId();
        $type = $this->getRequest('type', 'string', 'point');
        if (!in_array($type, ['point'])) {
            $this->sendErrorResult('参数错误');
        }
        $result = UserRepository::rechargeInfo($userId);
        $this->sendSuccessResult($result);
    }

    /**
     * 购买金币
     * @throws BusinessException
     */
    public function doRechargeAction()
    {
        $userId = $this->getUserId();
        $productId = $this->getRequest('group_id', 'int');
        $paymentId = $this->getRequest('payment_id', 'string');
        $type = $this->getRequest('type', 'string', 'point');
        if (empty($productId)) {
            $this->sendErrorResult('请选择购买套餐!');
        }
        if (empty($paymentId)) {
            $this->sendErrorResult('请选择正确支付方式!');
        }
        $result = UserRepository::doRecharge($type, $userId, $productId, $paymentId);
        $this->sendSuccessResult($result);
    }

    /**
     * 余额日志
     * @return void
     */
    public function accountLogAction()
    {
        $userId = $this->getUserId();
        $page = $this->getRequest('page', 'int', 1);
        $field = $this->getRequest('field', 'string');
        $cursor = $this->getRequest('cursor', 'string');
        $result = UserRepository::getAccountLog($userId,$field, $page,$cursor);
        $this->sendSuccessResult($result);
    }

    /**
     * 兑换码
     * @return void
     */
    public function doCodeAction(){
        $userId = $this->getUserId();
        $code = $this->getRequest('code', 'string');
        $result = UserRepository::doCode($userId,$code);
        $this->sendSuccessResult($result);

    }

    /**
     * 兑换码记录
     * @return void
     */
    public function codeLogAction(){
        $userId = $this->getUserId();
        $page = $this->getRequest('page', 'int',1);
        $cursor = $this->getRequest('cursor', 'string');
        $result = UserRepository::codeLog($userId,$page,20,$cursor);
        $this->sendSuccessResult($result);
    }

    /**
     * 订单记录
     * @return void
     */
    public function orderLogAction()
    {
        $userId = $this->getUserId();
        $page = $this->getRequest('page', 'int',1);
        $type = $this->getRequest('type','string');
        $result = UserRepository::getOrderLog($userId,$type,$page);
        $this->sendSuccessResult($result);
    }

    /**
     * 分享信息
     * @return void
     */
    public function shareInfoAction()
    {
        $userId = $this->getUserId();
        $result = UserRepository::getShareInfo($userId);
        $this->sendSuccessResult($result);
    }

    /**
     * 邀请列表
     * @return void
     */
    public function shareLogAction()
    {
        $userId = $this->getUserId();
        $page = $this->getRequest('page','int',1);
        $cursor = $this->getRequest('cursor', 'string');
        $result = UserRepository::getShareLog($userId,$page,20,$cursor);
        $this->sendSuccessResult($result);
    }


    /**
     * 客户端心跳-活跃
     * 实现当前在线人数,xx人在看
     * 客户端定时心跳,30s一次
     * 无需响应
     * @return void
     */
    public function doActiveAction()
    {
        $userId = $this->getUserId();
        $route = $this->getRequest('route','string');
        $params = $this->getRequest('params','string');
        UserRepository::doActive($userId, $route, $params);
        /*$this->sendSuccessResult();//没有响应的意义,浪费出口带宽*/
    }

    /**
     * 客户端心跳-获取活跃人数
     * @return void
     */
    public function getActiveAction()
    {
        $userId = $this->getUserId(false);
        $route = $this->getRequest('route','string');
        $params = $this->getRequest('params','string');
        $result = UserRepository::getActive($route, $params);
        $this->sendSuccessResult($result);
    }

    /**
     * 去提现
     * @return void
     */
    public function doWithdrawAction()
    {
        $userId = $this->getUserId();
        $method = $this->getRequest('method','string');//提现方式
        $bankName = $this->getRequest('bank_name','string');//银行名称
        $accountName = $this->getRequest('account_name','string');//账号名称
        $account = $this->getRequest('account','string');//账号
        $num = $this->getRequest('num','int');//提现数量
        $balanceField = $this->getRequest('field','string','balance');
        UserRepository::doWithdraw($userId,$method,$bankName,$accountName,$account,$num,$balanceField);
        $this->sendSuccessResult();
    }


    /**
     * 提现记录
     * @return void
     */
    public function withdrawLogAction()
    {
        $userId = $this->getUserId();
        $page = $this->getRequest('page', 'int', 1);
        $field = $this->getRequest('field', 'string');
        $result = UserRepository::getWithdrawLog($userId,$field, $page);
        $this->sendSuccessResult($result);
    }

    /**
     * 获取客服链接
     * @return void
     * @throws \Exception
     */
    public function getCustomerUrlAction()
    {
        $userId = $this->getUserId();
        $result = UserRepository::getCustomerUrl($userId);
        $this->sendSuccessResult($result);
    }
}
