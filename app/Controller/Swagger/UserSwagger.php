<?php

namespace App\Controller\Swagger;

use App\Controller\BaseApiController;
use App\Exception\BusinessException;

/**
 * @OA\Tag(name="用户管理", description="用户相关接口")
 */
class UserSwagger extends BaseApiController
{
    /**
     * 个人信息
     * @return void
     *
     * @OA\Post(
     *     path="/user/info",
     *     summary="个人信息",
     *     tags={"用户管理"},
     *
     *     @OA\Parameter(ref="#/components/parameters/HeaderVersion"),
     *     @OA\Parameter(ref="#/components/parameters/HeaderDeviceType"),
     *     @OA\Parameter(ref="#/components/parameters/HeaderTime"),
     *     @OA\Parameter(ref="#/components/parameters/HeaderDebugKey"),
     *     @OA\Parameter(ref="#/components/parameters/HeaderRequestId"),
     *     @OA\Parameter(ref="#/components/parameters/HeaderToken"),
     *     @OA\Parameter(ref="#/components/parameters/HeaderDeviceId"),
     *
     *     @OA\RequestBody(
     *          required=true,
     *          description="JSON格式数据",
     *          @OA\JsonContent(
     *              required={"deviceId", "token"},
     *              @OA\Property(property="deviceId", type="string", description="非调试时，必传设备ID"),
     *              @OA\Property(property="token", type="string", description="登陆后需传"),
     *          )
     *      ),
     *     @OA\Response(
     *      response=200,
     *      description="success",
     *      @OA\JsonContent(
     *          allOf={
     *              @OA\Schema(ref="#/components/schemas/ApiResponseEmpty")
     *          }
     *      )
     *   )
     *  )
     */
    public function infoAction()
    {
    }

    /**
     * 个人主页
     * @return void
     * @throws \App\Exception\BusinessException
     *
     * @OA\Post(
     *     path="/user/home",
     *     summary="个人主页",
     *     tags={"用户管理"},
     *
     *     @OA\Parameter(ref="#/components/parameters/HeaderVersion"),
     *     @OA\Parameter(ref="#/components/parameters/HeaderDeviceType"),
     *     @OA\Parameter(ref="#/components/parameters/HeaderTime"),
     *     @OA\Parameter(ref="#/components/parameters/HeaderDebugKey"),
     *     @OA\Parameter(ref="#/components/parameters/HeaderRequestId"),
     *     @OA\Parameter(ref="#/components/parameters/HeaderToken"),
     *     @OA\Parameter(ref="#/components/parameters/HeaderDeviceId"),
     *
     *     @OA\RequestBody(
     *          required=true,
     *          description="JSON格式数据",
     *          @OA\JsonContent(
     *              required={"deviceId", "token", "data"},
     *              @OA\Property(property="deviceId", type="string", description="非调试时，必传设备ID"),
     *              @OA\Property(property="token", type="string", description="登陆后需传"),
     *              @OA\Property(property="data", type="object", description="业务数据",
     *                 required={"home_id"},
     *                 @OA\Property(property="home_id", type="string", description="用户ID")
     *              )
     *          )
     *      ),
     *     @OA\Response(
     *      response=200,
     *      description="success",
     *      @OA\JsonContent(
     *          allOf={
     *              @OA\Schema(ref="#/components/schemas/ApiResponseEmpty")
     *          }
     *      )
     *   )
     *  )
     */
    public function homeAction()
    {
    }

    /**
     * 创作者
     * @return void
     *
     * @throws BusinessException
     * @OA\Post(
     *     path="/user/creator",
     *     summary="创作者信息",
     *     tags={"用户管理"},
     *
     *     @OA\Parameter(ref="#/components/parameters/HeaderVersion"),
     *     @OA\Parameter(ref="#/components/parameters/HeaderDeviceType"),
     *     @OA\Parameter(ref="#/components/parameters/HeaderTime"),
     *     @OA\Parameter(ref="#/components/parameters/HeaderDebugKey"),
     *     @OA\Parameter(ref="#/components/parameters/HeaderRequestId"),
     *     @OA\Parameter(ref="#/components/parameters/HeaderToken"),
     *     @OA\Parameter(ref="#/components/parameters/HeaderDeviceId"),
     *
     *     @OA\RequestBody(
     *          required=true,
     *          description="JSON格式数据",
     *          @OA\JsonContent(
     *              required={"deviceId", "token"},
     *              @OA\Property(property="deviceId", type="string", description="非调试时，必传设备ID"),
     *              @OA\Property(property="token", type="string", description="登陆后需传"),
     *          )
     *      ),
     *
     *     @OA\Response(
     *      response=200,
     *      description="success",
     *      @OA\JsonContent(
     *          allOf={
     *              @OA\Schema(ref="#/components/schemas/ApiResponseEmpty")
     *          }
     *      )
     *   )
     *  )
     */
    public function creatorAction()
    {
    }

    /**
     * 头像库
     * @return void
     *
     * @OA\Post(
     *     path="/user/images",
     *     summary="头像库列表",
     *     tags={"用户管理"},
     *
     *     @OA\Parameter(ref="#/components/parameters/HeaderVersion"),
     *     @OA\Parameter(ref="#/components/parameters/HeaderDeviceType"),
     *     @OA\Parameter(ref="#/components/parameters/HeaderTime"),
     *     @OA\Parameter(ref="#/components/parameters/HeaderDebugKey"),
     *     @OA\Parameter(ref="#/components/parameters/HeaderRequestId"),
     *     @OA\Parameter(ref="#/components/parameters/HeaderToken"),
     *     @OA\Parameter(ref="#/components/parameters/HeaderDeviceId"),
     *
     *     @OA\RequestBody(
     *          required=true,
     *          description="JSON格式数据",
     *          @OA\JsonContent(
     *              required={"deviceId"},
     *              @OA\Property(property="deviceId", type="string", description="非调试时，必传设备ID"),
     *          )
     *      ),
     *
     *     @OA\Response(
     *      response=200,
     *      description="success",
     *      @OA\JsonContent(
     *          allOf={
     *              @OA\Schema(ref="#/components/schemas/ApiResponseEmpty")
     *          }
     *      )
     *   )
     *  )
     */
    public function imagesAction()
    {
    }

    /**
     * 批量更新
     * @return void
     * @throws BusinessException
     * @throws \Phalcon\Storage\Exception
     *
     * @OA\Post(
     *     path="/user/doUpdate",
     *     summary="批量更新用户信息",
     *     tags={"用户管理"},
     *
     *     @OA\Parameter(ref="#/components/parameters/HeaderVersion"),
     *     @OA\Parameter(ref="#/components/parameters/HeaderDeviceType"),
     *     @OA\Parameter(ref="#/components/parameters/HeaderTime"),
     *     @OA\Parameter(ref="#/components/parameters/HeaderDebugKey"),
     *     @OA\Parameter(ref="#/components/parameters/HeaderRequestId"),
     *     @OA\Parameter(ref="#/components/parameters/HeaderToken"),
     *     @OA\Parameter(ref="#/components/parameters/HeaderDeviceId"),
     *
     *     @OA\RequestBody(
     *          required=true,
     *          description="JSON格式数据",
     *          @OA\JsonContent(
     *              required={"deviceId", "token", "data"},
     *              @OA\Property(property="deviceId", type="string", description="非调试时，必传设备ID"),
     *              @OA\Property(property="token", type="string", description="登陆后需传"),
     *              @OA\Property(property="data", type="object", description="业务数据",
     *                 @OA\Property(property="fields", type="object", description="更新字段")
     *              )
     *          )
     *      ),
     *     @OA\Response(
     *      response=200,
     *      description="success",
     *      @OA\JsonContent(
     *          allOf={
     *              @OA\Schema(ref="#/components/schemas/ApiResponseEmpty")
     *          }
     *      )
     *   )
     *  )
     */
    public function doUpdateAction()
    {
    }

    /**
     * 修改密码
     * @return void
     * @throws \App\Exception\BusinessException
     *
     * @OA\Post(
     *     path="/user/password",
     *     summary="修改密码",
     *     tags={"用户管理"},
     *
     *     @OA\Parameter(ref="#/components/parameters/HeaderVersion"),
     *     @OA\Parameter(ref="#/components/parameters/HeaderDeviceType"),
     *     @OA\Parameter(ref="#/components/parameters/HeaderTime"),
     *     @OA\Parameter(ref="#/components/parameters/HeaderDebugKey"),
     *     @OA\Parameter(ref="#/components/parameters/HeaderRequestId"),
     *     @OA\Parameter(ref="#/components/parameters/HeaderToken"),
     *     @OA\Parameter(ref="#/components/parameters/HeaderDeviceId"),
     *
     *     @OA\RequestBody(
     *          required=true,
     *          description="JSON格式数据",
     *          @OA\JsonContent(
     *              required={"deviceId", "token", "data"},
     *              @OA\Property(property="deviceId", type="string", description="非调试时，必传设备ID"),
     *              @OA\Property(property="token", type="string", description="登陆后需传"),
     *              @OA\Property(property="data", type="object", description="业务数据",
     *                 @OA\Property(property="old_password", type="string", description="旧密码"),
     *                 @OA\Property(property="new_password", type="string", description="新密码")
     *              )
     *          )
     *      ),
     *     @OA\Response(
     *      response=200,
     *      description="success",
     *      @OA\JsonContent(
     *          allOf={
     *              @OA\Schema(ref="#/components/schemas/ApiResponseEmpty")
     *          }
     *      )
     *   )
     *  )
     */
    public function passwordAction()
    {
    }

    /**
     * 用户关注
     *
     * @OA\Post(
     *     path="/user/doFollow",
     *     summary="用户关注操作",
     *     tags={"用户管理"},
     *
     *     @OA\Parameter(ref="#/components/parameters/HeaderVersion"),
     *     @OA\Parameter(ref="#/components/parameters/HeaderDeviceType"),
     *     @OA\Parameter(ref="#/components/parameters/HeaderTime"),
     *     @OA\Parameter(ref="#/components/parameters/HeaderDebugKey"),
     *     @OA\Parameter(ref="#/components/parameters/HeaderRequestId"),
     *     @OA\Parameter(ref="#/components/parameters/HeaderToken"),
     *     @OA\Parameter(ref="#/components/parameters/HeaderDeviceId"),
     *
     *     @OA\RequestBody(
     *          required=true,
     *          description="JSON格式数据",
     *          @OA\JsonContent(
     *              required={"deviceId", "token", "data"},
     *              @OA\Property(property="deviceId", type="string", description="非调试时，必传设备ID"),
     *              @OA\Property(property="token", type="string", description="登陆后需传"),
     *              @OA\Property(property="data", type="object", description="业务数据",
     *                 required={"home_id"},
     *                 @OA\Property(property="home_id", type="string", description="被关注用户ID"),
     *                 @OA\Property(property="action", type="string", default="follow", description="操作类型")
     *              )
     *          )
     *      ),
     *     @OA\Response(
     *      response=200,
     *      description="success",
     *      @OA\JsonContent(
     *          allOf={
     *              @OA\Schema(ref="#/components/schemas/ApiResponseEmpty")
     *          }
     *      )
     *   )
     *  )
     */
    public function doFollowAction()
    {
    }

    /**
     * 关注列表
     *
     * @OA\Post(
     *     path="/user/follow",
     *     summary="关注列表",
     *     tags={"用户管理"},
     *
     *     @OA\Parameter(ref="#/components/parameters/HeaderVersion"),
     *     @OA\Parameter(ref="#/components/parameters/HeaderDeviceType"),
     *     @OA\Parameter(ref="#/components/parameters/HeaderTime"),
     *     @OA\Parameter(ref="#/components/parameters/HeaderDebugKey"),
     *     @OA\Parameter(ref="#/components/parameters/HeaderRequestId"),
     *     @OA\Parameter(ref="#/components/parameters/HeaderToken"),
     *     @OA\Parameter(ref="#/components/parameters/HeaderDeviceId"),
     *
     *     @OA\RequestBody(
     *          required=true,
     *          description="JSON格式数据",
     *          @OA\JsonContent(
     *              required={"deviceId", "token", "data"},
     *              @OA\Property(property="deviceId", type="string", description="非调试时，必传设备ID"),
     *              @OA\Property(property="token", type="string", description="登陆后需传"),
     *              @OA\Property(property="data", type="object", description="业务数据",
     *                 required={"home_id"},
     *                 @OA\Property(property="home_id", type="integer", description="主页ID"),
     *                 @OA\Property(property="action", type="string", default="follow", description=""),
     *                 @OA\Property(property="page", type="integer", default=1, description="页码"),
     *                 @OA\Property(property="cursor", type="string", description="更新时间")
     *              )
     *          )
     *      ),
     *     @OA\Response(
     *      response=200,
     *      description="success",
     *      @OA\JsonContent(
     *          allOf={
     *              @OA\Schema(ref="#/components/schemas/ApiResponseEmpty")
     *          }
     *      )
     *   )
     *  )
     */
    public function followAction()
    {
    }

    /**
     * 粉丝列表
     *
     * @OA\Post(
     *     path="/user/fans",
     *     summary="粉丝列表",
     *     tags={"用户管理"},
     *
     *     @OA\Parameter(ref="#/components/parameters/HeaderVersion"),
     *     @OA\Parameter(ref="#/components/parameters/HeaderDeviceType"),
     *     @OA\Parameter(ref="#/components/parameters/HeaderTime"),
     *     @OA\Parameter(ref="#/components/parameters/HeaderDebugKey"),
     *     @OA\Parameter(ref="#/components/parameters/HeaderRequestId"),
     *     @OA\Parameter(ref="#/components/parameters/HeaderToken"),
     *     @OA\Parameter(ref="#/components/parameters/HeaderDeviceId"),
     *
     *     @OA\RequestBody(
     *          required=true,
     *          description="JSON格式数据",
     *          @OA\JsonContent(
     *              required={"deviceId", "token", "data"},
     *              @OA\Property(property="deviceId", type="string", description="非调试时，必传设备ID"),
     *              @OA\Property(property="token", type="string", description="登陆后需传"),
     *              @OA\Property(property="data", type="object", description="业务数据",
     *                 required={"home_id"},
     *                 @OA\Property(property="home_id", type="integer", description="用户ID"),
     *                 @OA\Property(property="page", type="integer", default=1, description="页码"),
     *                 @OA\Property(property="cursor", type="string", description="更新时间")
     *              )
     *          )
     *      ),
     *     @OA\Response(
     *      response=200,
     *      description="success",
     *      @OA\JsonContent(
     *          allOf={
     *              @OA\Schema(ref="#/components/schemas/ApiResponseEmpty")
     *          }
     *      )
     *   )
     *  )
     */
    public function fansAction()
    {
    }

    /**
     * 收藏板块
     * @return void
     * @throws BusinessException
     *
     * @OA\Post(
     *     path="/user/doFavorite",
     *     summary="收藏/取消收藏操作",
     *     tags={"用户管理"},
     *
     *     @OA\Parameter(ref="#/components/parameters/HeaderVersion"),
     *     @OA\Parameter(ref="#/components/parameters/HeaderDeviceType"),
     *     @OA\Parameter(ref="#/components/parameters/HeaderTime"),
     *     @OA\Parameter(ref="#/components/parameters/HeaderDebugKey"),
     *     @OA\Parameter(ref="#/components/parameters/HeaderRequestId"),
     *     @OA\Parameter(ref="#/components/parameters/HeaderToken"),
     *     @OA\Parameter(ref="#/components/parameters/HeaderDeviceId"),
     *
     *     @OA\RequestBody(
     *          required=true,
     *          description="JSON格式数据",
     *          @OA\JsonContent(
     *              required={"deviceId", "token", "data"},
     *              @OA\Property(property="deviceId", type="string", description="非调试时，必传设备ID"),
     *              @OA\Property(property="token", type="string", description="登陆后需传"),
     *              @OA\Property(property="data", type="object", description="业务数据",
     *                 required={"object_type", "object_id"},
     *                 @OA\Property(property="object_type", type="string", description="对象类型"),
     *                 @OA\Property(property="object_id", type="string", description="对象ID")
     *              )
     *          )
     *      ),
     *     @OA\Response(
     *      response=200,
     *      description="success",
     *      @OA\JsonContent(
     *          allOf={
     *              @OA\Schema(ref="#/components/schemas/ApiResponseEmpty")
     *          }
     *      )
     *   )
     *  )
     */
    public function doFavoriteAction()
    {
    }

    /**
     * 收藏的板块列表
     * @return void
     *
     * @OA\Post(
     *     path="/user/favorite",
     *     summary="收藏列表",
     *     tags={"用户管理"},
     *
     *     @OA\Parameter(ref="#/components/parameters/HeaderVersion"),
     *     @OA\Parameter(ref="#/components/parameters/HeaderDeviceType"),
     *     @OA\Parameter(ref="#/components/parameters/HeaderTime"),
     *     @OA\Parameter(ref="#/components/parameters/HeaderDebugKey"),
     *     @OA\Parameter(ref="#/components/parameters/HeaderRequestId"),
     *     @OA\Parameter(ref="#/components/parameters/HeaderToken"),
     *     @OA\Parameter(ref="#/components/parameters/HeaderDeviceId"),
     *
     *     @OA\RequestBody(
     *          required=true,
     *          description="JSON格式数据",
     *          @OA\JsonContent(
     *              required={"deviceId", "token", "data"},
     *              @OA\Property(property="deviceId", type="string", description="非调试时，必传设备ID"),
     *              @OA\Property(property="token", type="string", description="登陆后需传"),
     *              @OA\Property(property="data", type="object", description="业务数据",
     *                 required={"object_type"},
     *                 @OA\Property(property="object_type", type="string", description="对象类型"),
     *                 @OA\Property(property="page", type="integer", default=1, description="页码")
     *              )
     *          )
     *      ),
     *     @OA\Response(
     *      response=200,
     *      description="success",
     *      @OA\JsonContent(
     *          allOf={
     *              @OA\Schema(ref="#/components/schemas/ApiResponseEmpty")
     *          }
     *      )
     *   )
     *  )
     */
    public function favoriteAction()
    {
    }

    /**
     * 会员页面
     *
     * @OA\Post(
     *     path="/user/vip",
     *     summary="会员页面信息",
     *     tags={"用户管理"},
     *
     *     @OA\Parameter(ref="#/components/parameters/HeaderVersion"),
     *     @OA\Parameter(ref="#/components/parameters/HeaderDeviceType"),
     *     @OA\Parameter(ref="#/components/parameters/HeaderTime"),
     *     @OA\Parameter(ref="#/components/parameters/HeaderDebugKey"),
     *     @OA\Parameter(ref="#/components/parameters/HeaderRequestId"),
     *     @OA\Parameter(ref="#/components/parameters/HeaderToken"),
     *     @OA\Parameter(ref="#/components/parameters/HeaderDeviceId"),
     *
     *     @OA\RequestBody(
     *          required=true,
     *          description="JSON格式数据",
     *          @OA\JsonContent(
     *              required={"deviceId", "token", "data"},
     *              @OA\Property(property="deviceId", type="string", description="非调试时，必传设备ID"),
     *              @OA\Property(property="token", type="string", description="登陆后需传"),
     *              @OA\Property(property="data", type="object", description="业务数据",
     *                 @OA\Property(property="group", type="string", default="normal", description="会员组别")
     *              )
     *          )
     *      ),
     *
     *     @OA\Response(
     *      response=200,
     *      description="success",
     *      @OA\JsonContent(
     *          allOf={
     *              @OA\Schema(ref="#/components/schemas/ApiResponseEmpty")
     *          }
     *      )
     *   )
     *  )
     */
    public function vipAction()
    {
    }

    /**
     * 去购买
     * @throws BusinessException
     *
     * @OA\Post(
     *     path="/user/do-vip",
     *     summary="购买会员",
     *     tags={"用户管理"},
     *
     *     @OA\Parameter(ref="#/components/parameters/HeaderVersion"),
     *     @OA\Parameter(ref="#/components/parameters/HeaderDeviceType"),
     *     @OA\Parameter(ref="#/components/parameters/HeaderTime"),
     *     @OA\Parameter(ref="#/components/parameters/HeaderDebugKey"),
     *     @OA\Parameter(ref="#/components/parameters/HeaderRequestId"),
     *     @OA\Parameter(ref="#/components/parameters/HeaderToken"),
     *     @OA\Parameter(ref="#/components/parameters/HeaderDeviceId"),
     *
     *     @OA\RequestBody(
     *          required=true,
     *          description="JSON格式数据",
     *          @OA\JsonContent(
     *              required={"deviceId", "token", "data"},
     *              @OA\Property(property="deviceId", type="string", description="非调试时，必传设备ID"),
     *              @OA\Property(property="token", type="string", description="登陆后需传"),
     *              @OA\Property(property="data", type="object", description="业务数据",
     *                 required={"group_id", "payment_id"},
     *                 @OA\Property(property="group_id", type="integer", description="会员套餐ID"),
     *                 @OA\Property(property="payment_id", type="string", description="支付方式ID")
     *              )
     *          )
     *      ),
     *     @OA\Response(
     *      response=200,
     *      description="success",
     *      @OA\JsonContent(
     *          allOf={
     *              @OA\Schema(ref="#/components/schemas/ApiResponseEmpty")
     *          }
     *      )
     *   )
     *  )
     */
    public function doVipAction()
    {
    }

    /**
     * 金币充值
     * @throws BusinessException
     *
     * @OA\Post(
     *     path="/user/recharge",
     *     summary="金币充值页面信息",
     *     tags={"用户管理"},
     *
     *     @OA\Parameter(ref="#/components/parameters/HeaderVersion"),
     *     @OA\Parameter(ref="#/components/parameters/HeaderDeviceType"),
     *     @OA\Parameter(ref="#/components/parameters/HeaderTime"),
     *     @OA\Parameter(ref="#/components/parameters/HeaderDebugKey"),
     *     @OA\Parameter(ref="#/components/parameters/HeaderRequestId"),
     *     @OA\Parameter(ref="#/components/parameters/HeaderToken"),
     *     @OA\Parameter(ref="#/components/parameters/HeaderDeviceId"),
     *
     *     @OA\RequestBody(
     *          required=true,
     *          description="JSON格式数据",
     *          @OA\JsonContent(
     *              required={"deviceId", "token", "data"},
     *              @OA\Property(property="deviceId", type="string", description="非调试时，必传设备ID"),
     *              @OA\Property(property="token", type="string", description="登陆后需传"),
     *              @OA\Property(property="data", type="object", description="业务数据",
     *                 required={"type"},
     *                 @OA\Property(property="type", type="string", default="point", description="充值类型")
     *              )
     *          )
     *      ),
     *     @OA\Response(
     *      response=200,
     *      description="success",
     *      @OA\JsonContent(
     *          allOf={
     *              @OA\Schema(ref="#/components/schemas/ApiResponseEmpty")
     *          }
     *      )
     *   )
     *  )
     */
    public function rechargeAction()
    {
    }

    /**
     * 购买金币
     * @throws BusinessException
     *
     * @OA\Post(
     *     path="/user/doRecharge",
     *     summary="购买金币",
     *     tags={"用户管理"},
     *
     *     @OA\Parameter(ref="#/components/parameters/HeaderVersion"),
     *     @OA\Parameter(ref="#/components/parameters/HeaderDeviceType"),
     *     @OA\Parameter(ref="#/components/parameters/HeaderTime"),
     *     @OA\Parameter(ref="#/components/parameters/HeaderDebugKey"),
     *     @OA\Parameter(ref="#/components/parameters/HeaderRequestId"),
     *     @OA\Parameter(ref="#/components/parameters/HeaderToken"),
     *     @OA\Parameter(ref="#/components/parameters/HeaderDeviceId"),
     *
     *     @OA\RequestBody(
     *          required=true,
     *          description="JSON格式数据",
     *          @OA\JsonContent(
     *              required={"deviceId", "token", "data"},
     *              @OA\Property(property="deviceId", type="string", description="非调试时，必传设备ID"),
     *              @OA\Property(property="token", type="string", description="登陆后需传"),
     *              @OA\Property(property="data", type="object", description="业务数据",
     *                 required={"group_id", "payment_id"},
     *                 @OA\Property(property="group_id", type="integer", description="产品ID"),
     *                 @OA\Property(property="payment_id", type="string", description="支付方式ID"),
     *              )
     *          )
     *      ),
     *     @OA\Response(
     *      response=200,
     *      description="success",
     *      @OA\JsonContent(
     *          allOf={
     *              @OA\Schema(ref="#/components/schemas/ApiResponseEmpty")
     *          }
     *      )
     *   )
     *  )
     */
    public function doRechargeAction()
    {
    }

    /**
     * 余额日志
     * @return void
     *
     * @OA\Post(
     *     path="/user/accountLog",
     *     summary="余额日志",
     *     tags={"用户管理"},
     *
     *     @OA\Parameter(ref="#/components/parameters/HeaderVersion"),
     *     @OA\Parameter(ref="#/components/parameters/HeaderDeviceType"),
     *     @OA\Parameter(ref="#/components/parameters/HeaderTime"),
     *     @OA\Parameter(ref="#/components/parameters/HeaderDebugKey"),
     *     @OA\Parameter(ref="#/components/parameters/HeaderRequestId"),
     *     @OA\Parameter(ref="#/components/parameters/HeaderToken"),
     *     @OA\Parameter(ref="#/components/parameters/HeaderDeviceId"),
     *
     *     @OA\RequestBody(
     *          required=true,
     *          description="JSON格式数据",
     *          @OA\JsonContent(
     *              required={"deviceId", "token", "data"},
     *              @OA\Property(property="deviceId", type="string", description="非调试时，必传设备ID"),
     *              @OA\Property(property="token", type="string", description="登陆后需传"),
     *              @OA\Property(property="data", type="object", description="业务数据",
     *                 @OA\Property(property="field", type="string", description="余额字段"),
     *                 @OA\Property(property="page", type="integer", default=1, description="页码")
     *              )
     *          )
     *      ),
     *     @OA\Response(
     *      response=200,
     *      description="success",
     *      @OA\JsonContent(
     *          allOf={
     *              @OA\Schema(ref="#/components/schemas/ApiResponseEmpty")
     *          }
     *      )
     *   )
     *  )
     */
    public function accountLogAction()
    {
    }

    /**
     * 兑换码
     * @return void
     * @throws BusinessException
     *
     * @OA\Post(
     *     path="/user/doCode",
     *     summary="兑换码兑换",
     *     tags={"用户管理"},
     *
     *     @OA\Parameter(ref="#/components/parameters/HeaderVersion"),
     *     @OA\Parameter(ref="#/components/parameters/HeaderDeviceType"),
     *     @OA\Parameter(ref="#/components/parameters/HeaderTime"),
     *     @OA\Parameter(ref="#/components/parameters/HeaderDebugKey"),
     *     @OA\Parameter(ref="#/components/parameters/HeaderRequestId"),
     *     @OA\Parameter(ref="#/components/parameters/HeaderToken"),
     *     @OA\Parameter(ref="#/components/parameters/HeaderDeviceId"),
     *
     *     @OA\RequestBody(
     *          required=true,
     *          description="JSON格式数据",
     *          @OA\JsonContent(
     *              required={"deviceId", "token", "data"},
     *              @OA\Property(property="deviceId", type="string", description="非调试时，必传设备ID"),
     *              @OA\Property(property="token", type="string", description="登陆后需传"),
     *              @OA\Property(property="data", type="object", description="业务数据",
     *                 required={"code"},
     *                 @OA\Property(property="code", type="string", description="兑换码")
     *              )
     *          )
     *      ),
     *     @OA\Response(
     *      response=200,
     *      description="success",
     *      @OA\JsonContent(
     *          allOf={
     *              @OA\Schema(ref="#/components/schemas/ApiResponseEmpty")
     *          }
     *      )
     *   )
     *  )
     */
    public function doCodeAction()
    {
    }

    /**
     * 兑换码记录
     * @return void
     *
     * @OA\Post(
     *     path="/user/codeLog",
     *     summary="兑换码记录",
     *     tags={"用户管理"},
     *
     *     @OA\Parameter(ref="#/components/parameters/HeaderVersion"),
     *     @OA\Parameter(ref="#/components/parameters/HeaderDeviceType"),
     *     @OA\Parameter(ref="#/components/parameters/HeaderTime"),
     *     @OA\Parameter(ref="#/components/parameters/HeaderDebugKey"),
     *     @OA\Parameter(ref="#/components/parameters/HeaderRequestId"),
     *     @OA\Parameter(ref="#/components/parameters/HeaderToken"),
     *     @OA\Parameter(ref="#/components/parameters/HeaderDeviceId"),
     *
     *     @OA\RequestBody(
     *          required=true,
     *          description="JSON格式数据",
     *          @OA\JsonContent(
     *              required={"deviceId", "token", "data"},
     *              @OA\Property(property="deviceId", type="string", description="非调试时，必传设备ID"),
     *              @OA\Property(property="token", type="string", description="登陆后需传"),
     *              @OA\Property(property="data", type="object", description="业务数据",
     *                 @OA\Property(property="page", type="integer", default=1, description="页码")
     *              )
     *          )
     *      ),
     *     @OA\Response(
     *      response=200,
     *      description="success",
     *      @OA\JsonContent(
     *          allOf={
     *              @OA\Schema(ref="#/components/schemas/ApiResponseEmpty")
     *          }
     *      )
     *   )
     *  )
     */
    public function codeLogAction()
    {
    }

    /**
     * 订单记录
     * @return void
     *
     * @OA\Post(
     *     path="/user/orderLog",
     *     summary="订单记录",
     *     tags={"用户管理"},
     *
     *     @OA\Parameter(ref="#/components/parameters/HeaderVersion"),
     *     @OA\Parameter(ref="#/components/parameters/HeaderDeviceType"),
     *     @OA\Parameter(ref="#/components/parameters/HeaderTime"),
     *     @OA\Parameter(ref="#/components/parameters/HeaderDebugKey"),
     *     @OA\Parameter(ref="#/components/parameters/HeaderRequestId"),
     *     @OA\Parameter(ref="#/components/parameters/HeaderToken"),
     *     @OA\Parameter(ref="#/components/parameters/HeaderDeviceId"),
     *
     *     @OA\RequestBody(
     *          required=true,
     *          description="JSON格式数据",
     *          @OA\JsonContent(
     *              required={"deviceId", "token", "data"},
     *              @OA\Property(property="deviceId", type="string", description="非调试时，必传设备ID"),
     *              @OA\Property(property="token", type="string", description="登陆后需传"),
     *              @OA\Property(property="data", type="object", description="业务数据",
     *                 required={"type"},
     *                 @OA\Property(property="type", type="string", description="订单类型:vip,recharge"),
     *                 @OA\Property(property="page", type="integer", default=1, description="页码")
     *              )
     *          )
     *      ),
     *     @OA\Response(
     *      response=200,
     *      description="success",
     *      @OA\JsonContent(
     *          allOf={
     *              @OA\Schema(ref="#/components/schemas/ApiResponseEmpty")
     *          }
     *      )
     *   )
     *  )
     */
    public function orderLogAction()
    {
    }

    /**
     * 分享信息
     * @return void
     *
     * @OA\Post(
     *     path="/user/shareInfo",
     *     summary="分享信息",
     *     tags={"用户管理"},
     *
     *     @OA\Parameter(ref="#/components/parameters/HeaderVersion"),
     *     @OA\Parameter(ref="#/components/parameters/HeaderDeviceType"),
     *     @OA\Parameter(ref="#/components/parameters/HeaderTime"),
     *     @OA\Parameter(ref="#/components/parameters/HeaderDebugKey"),
     *     @OA\Parameter(ref="#/components/parameters/HeaderRequestId"),
     *     @OA\Parameter(ref="#/components/parameters/HeaderToken"),
     *     @OA\Parameter(ref="#/components/parameters/HeaderDeviceId"),
     *
     *     @OA\RequestBody(
     *          required=true,
     *          description="JSON格式数据",
     *          @OA\JsonContent(
     *              required={"deviceId", "token"},
     *              @OA\Property(property="deviceId", type="string", description="非调试时，必传设备ID"),
     *              @OA\Property(property="token", type="string", description="登陆后需传"),
     *          )
     *      ),
     *     @OA\Response(
     *      response=200,
     *      description="success",
     *      @OA\JsonContent(
     *          allOf={
     *              @OA\Schema(ref="#/components/schemas/ApiResponseEmpty")
     *          }
     *      )
     *   )
     *  )
     */
    public function shareInfoAction()
    {
    }

    /**
     * 邀请列表
     * @return void
     *
     * @OA\Post(
     *     path="/user/shareLog",
     *     summary="邀请列表",
     *     tags={"用户管理"},
     *
     *     @OA\Parameter(ref="#/components/parameters/HeaderVersion"),
     *     @OA\Parameter(ref="#/components/parameters/HeaderDeviceType"),
     *     @OA\Parameter(ref="#/components/parameters/HeaderTime"),
     *     @OA\Parameter(ref="#/components/parameters/HeaderDebugKey"),
     *     @OA\Parameter(ref="#/components/parameters/HeaderRequestId"),
     *     @OA\Parameter(ref="#/components/parameters/HeaderToken"),
     *     @OA\Parameter(ref="#/components/parameters/HeaderDeviceId"),
     *
     *     @OA\RequestBody(
     *          required=true,
     *          description="JSON格式数据",
     *          @OA\JsonContent(
     *              required={"deviceId", "token", "data"},
     *              @OA\Property(property="deviceId", type="string", description="非调试时，必传设备ID"),
     *              @OA\Property(property="token", type="string", description="登陆后需传"),
     *              @OA\Property(property="data", type="object", description="业务数据",
     *                 @OA\Property(property="page", type="integer", default=1, description="页码")
     *              )
     *          )
     *      ),
     *     @OA\Response(
     *      response=200,
     *      description="success",
     *      @OA\JsonContent(
     *          allOf={
     *              @OA\Schema(ref="#/components/schemas/ApiResponseEmpty")
     *          }
     *      )
     *   )
     *  )
     */
    public function shareLogAction()
    {
    }

    /**
     * 客户端心跳-活跃
     * 实现当前在线人数,xx人在看
     * 客户端定时心跳,30s一次
     * 无需响应
     * @return void
     *
     * @OA\Post(
     *     path="/user/doActive",
     *     summary="客户端心跳-活跃",
     *     tags={"用户管理"},
     *     description="客户端定时心跳,30s一次，无需响应",
     *
     *     @OA\Parameter(ref="#/components/parameters/HeaderVersion"),
     *     @OA\Parameter(ref="#/components/parameters/HeaderDeviceType"),
     *     @OA\Parameter(ref="#/components/parameters/HeaderTime"),
     *     @OA\Parameter(ref="#/components/parameters/HeaderDebugKey"),
     *     @OA\Parameter(ref="#/components/parameters/HeaderRequestId"),
     *     @OA\Parameter(ref="#/components/parameters/HeaderToken"),
     *     @OA\Parameter(ref="#/components/parameters/HeaderDeviceId"),
     *
     *     @OA\RequestBody(
     *          required=true,
     *          description="JSON格式数据",
     *          @OA\JsonContent(
     *              required={"deviceId", "token", "data"},
     *              @OA\Property(property="deviceId", type="string", description="非调试时，必传设备ID"),
     *              @OA\Property(property="token", type="string", description="登陆后需传"),
     *              @OA\Property(property="data", type="object", description="业务数据",
     *                 @OA\Property(property="route", type="string", description="路由"),
     *                 @OA\Property(property="params", type="string", description="参数")
     *              )
     *          )
     *      ),
     *     @OA\Response(
     *      response=200,
     *      description="success",
     *      @OA\JsonContent(
     *          allOf={
     *              @OA\Schema(ref="#/components/schemas/ApiResponseEmpty")
     *          }
     *      )
     *   )
     *  )
     */
    public function doActiveAction()
    {
    }

    /**
     * 客户端心跳-获取活跃人数
     * @return void
     *
     * @OA\Post(
     *     path="/user/getActive",
     *     summary="客户端心跳-获取活跃人数",
     *     tags={"用户管理"},
     *
     *     @OA\Parameter(ref="#/components/parameters/HeaderVersion"),
     *     @OA\Parameter(ref="#/components/parameters/HeaderDeviceType"),
     *     @OA\Parameter(ref="#/components/parameters/HeaderTime"),
     *     @OA\Parameter(ref="#/components/parameters/HeaderDebugKey"),
     *     @OA\Parameter(ref="#/components/parameters/HeaderRequestId"),
     *     @OA\Parameter(ref="#/components/parameters/HeaderToken"),
     *     @OA\Parameter(ref="#/components/parameters/HeaderDeviceId"),
     *
     *     @OA\RequestBody(
     *          required=true,
     *          description="JSON格式数据",
     *          @OA\JsonContent(
     *              required={"deviceId", "token", "data"},
     *              @OA\Property(property="deviceId", type="string", description="非调试时，必传设备ID"),
     *              @OA\Property(property="token", type="string", description="登陆后需传"),
     *              @OA\Property(property="data", type="object", description="业务数据",
     *                 @OA\Property(property="route", type="string", description="路由"),
     *                 @OA\Property(property="params", type="string", description="参数")
     *              )
     *          )
     *      ),
     *     @OA\Response(
     *      response=200,
     *      description="success",
     *      @OA\JsonContent(
     *          allOf={
     *              @OA\Schema(ref="#/components/schemas/ApiResponseEmpty")
     *          }
     *      )
     *   )
     *  )
     */
    public function getActiveAction()
    {
    }

    /**
     * 去提现
     * @return void
     * @throws BusinessException
     *
     * @OA\Post(
     *     path="/user/doWithdraw",
     *     summary="提现申请",
     *     tags={"用户管理"},
     *
     *     @OA\Parameter(ref="#/components/parameters/HeaderVersion"),
     *     @OA\Parameter(ref="#/components/parameters/HeaderDeviceType"),
     *     @OA\Parameter(ref="#/components/parameters/HeaderTime"),
     *     @OA\Parameter(ref="#/components/parameters/HeaderDebugKey"),
     *     @OA\Parameter(ref="#/components/parameters/HeaderRequestId"),
     *     @OA\Parameter(ref="#/components/parameters/HeaderToken"),
     *     @OA\Parameter(ref="#/components/parameters/HeaderDeviceId"),
     *
     *     @OA\RequestBody(
     *          required=true,
     *          description="JSON格式数据",
     *          @OA\JsonContent(
     *              required={"deviceId", "token", "data"},
     *              @OA\Property(property="deviceId", type="string", description="非调试时，必传设备ID"),
     *              @OA\Property(property="token", type="string", description="登陆后需传"),
     *              @OA\Property(property="data", type="object", description="业务数据",
     *                 @OA\Property(property="method", type="string", description="提现方式"),
     *                 @OA\Property(property="bank_name", type="string", description="银行名称"),
     *                 @OA\Property(property="account_name", type="string", description="账号名称"),
     *                 @OA\Property(property="account", type="string", description="账号"),
     *                 @OA\Property(property="num", type="integer", description="提现数量"),
     *                 @OA\Property(property="field", type="string", default="balance", description="余额字段")
     *              )
     *          )
     *      ),
     *     @OA\Response(
     *      response=200,
     *      description="success",
     *      @OA\JsonContent(
     *          allOf={
     *              @OA\Schema(ref="#/components/schemas/ApiResponseEmpty")
     *          }
     *      )
     *   )
     *  )
     */
    public function doWithdrawAction()
    {
    }

    /**
     * 提现记录
     * @return void
     *
     * @OA\Post(
     *     path="/user/withdrawLog",
     *     summary="提现记录",
     *     tags={"用户管理"},
     *
     *     @OA\Parameter(ref="#/components/parameters/HeaderVersion"),
     *     @OA\Parameter(ref="#/components/parameters/HeaderDeviceType"),
     *     @OA\Parameter(ref="#/components/parameters/HeaderTime"),
     *     @OA\Parameter(ref="#/components/parameters/HeaderDebugKey"),
     *     @OA\Parameter(ref="#/components/parameters/HeaderRequestId"),
     *     @OA\Parameter(ref="#/components/parameters/HeaderToken"),
     *     @OA\Parameter(ref="#/components/parameters/HeaderDeviceId"),
     *
     *     @OA\RequestBody(
     *          required=true,
     *          description="JSON格式数据",
     *          @OA\JsonContent(
     *              required={"deviceId", "token", "data"},
     *              @OA\Property(property="deviceId", type="string", description="非调试时，必传设备ID"),
     *              @OA\Property(property="token", type="string", description="登陆后需传"),
     *              @OA\Property(property="data", type="object", description="业务数据",
     *                 @OA\Property(property="field", type="string", description="余额字段"),
     *                 @OA\Property(property="page", type="integer", default=1, description="页码")
     *              )
     *          )
     *      ),
     *     @OA\Response(
     *      response=200,
     *      description="success",
     *      @OA\JsonContent(
     *          allOf={
     *              @OA\Schema(ref="#/components/schemas/ApiResponseEmpty")
     *          }
     *      )
     *   )
     *  )
     */
    public function withdrawLogAction()
    {
    }

    /**
     * 获取客服链接
     * @return void
     * @throws \Exception
     *
     * @OA\Post(
     *     path="/user/getCustomerUrl",
     *     summary="获取客服链接",
     *     tags={"用户管理"},
     *
     *     @OA\Parameter(ref="#/components/parameters/HeaderVersion"),
     *     @OA\Parameter(ref="#/components/parameters/HeaderDeviceType"),
     *     @OA\Parameter(ref="#/components/parameters/HeaderTime"),
     *     @OA\Parameter(ref="#/components/parameters/HeaderDebugKey"),
     *     @OA\Parameter(ref="#/components/parameters/HeaderRequestId"),
     *     @OA\Parameter(ref="#/components/parameters/HeaderToken"),
     *     @OA\Parameter(ref="#/components/parameters/HeaderDeviceId"),
     *
     *     @OA\RequestBody(
     *          required=true,
     *          description="JSON格式数据",
     *          @OA\JsonContent(
     *              required={"deviceId", "token"},
     *              @OA\Property(property="deviceId", type="string", description="非调试时，必传设备ID"),
     *              @OA\Property(property="token", type="string", description="登陆后需传"),
     *          )
     *      ),
     *
     *      @OA\Response(
     *          response=200,
     *          description="请求响应成功",
     *          @OA\JsonContent(
     *              @OA\Property(property="status", type="string", default="y", enum={"y", "n"}),
     *              @OA\Property(property="data", type="object", nullable=true,
     *                  @OA\Property(property="url", type="string", description="客服链接")
     *              ),
     *              @OA\Property(property="error", type="string", nullable=true),
     *              @OA\Property(property="errorCode", type="integer", nullable=true),
     *              @OA\Property(property="time", type="string", nullable=true)
     *          )
     *      )
     *  )
     */
    public function getCustomerUrlAction()
    {
    }
}
