<?php

namespace App\Controller\Swagger;

use App\Controller\BaseApiController;
use App\Repositories\Api\LoginRepository;

/**
 * 登录
 *
 * @OA\Tag(name="登录模块", description="登录模块 API")
 */
class LoginSwagger extends BaseApiController
{
    /**
     * 账号登录
     *
     * @OA\Post(
     *     path="/login/username",
     *     summary="账号密码登录",
     *     tags={"登录模块"},
     *     @OA\Parameter(ref="#/components/parameters/HeaderVersion"),
     *     @OA\Parameter(ref="#/components/parameters/HeaderDeviceType"),
     *     @OA\Parameter(ref="#/components/parameters/HeaderTime"),
     *     @OA\Parameter(ref="#/components/parameters/HeaderDebugKey"),
     *     @OA\Parameter(ref="#/components/parameters/HeaderRequestId"),
     *     @OA\Parameter(ref="#/components/parameters/HeaderToken"),
     *     @OA\Parameter(ref="#/components/parameters/HeaderDeviceId"),
     *     @OA\RequestBody(
     *          required=true,
     *          description="JSON格式数据",
     *          @OA\JsonContent(
     *             required={"username", "password"},
     *             @OA\Property(property="username", type="string", description="账号", example="user123"),
     *             @OA\Property(property="password", type="string", description="密码", example="123456"),
     *             @OA\Property(property="clipboard_text", type="string", description="剪贴板内容(用于获取渠道/邀请码)")
     *         )
     *     ),
     *     @OA\Response(
     *      response=200,
     *      description="success",
     *      @OA\JsonContent(
     *          allOf={
     *              @OA\Schema(ref="#/components/schemas/ApiResponseEmpty")
     *          }
     *      )
     *   )
     * )
     */
    public function usernameAction()
    {
    }

    /**
     * 凭证登录
     *
     * @OA\Post(
     *     path="/login/qrcode",
     *     summary="凭证/二维码登录",
     *     tags={"登录模块"},
     *     @OA\Parameter(ref="#/components/parameters/HeaderVersion"),
     *     @OA\Parameter(ref="#/components/parameters/HeaderDeviceType"),
     *     @OA\Parameter(ref="#/components/parameters/HeaderTime"),
     *     @OA\Parameter(ref="#/components/parameters/HeaderDebugKey"),
     *     @OA\Parameter(ref="#/components/parameters/HeaderRequestId"),
     *     @OA\Parameter(ref="#/components/parameters/HeaderToken"),
     *     @OA\Parameter(ref="#/components/parameters/HeaderDeviceId"),
     *     @OA\RequestBody(
     *          required=true,
     *          description="JSON格式数据",
     *          @OA\JsonContent(
     *             required={"code"},
     *             @OA\Property(property="code", type="string", description="凭证代码/二维码内容")
     *         )
     *     ),
     *     @OA\Response(
     *      response=200,
     *      description="success",
     *      @OA\JsonContent(
     *          allOf={
     *              @OA\Schema(ref="#/components/schemas/ApiResponseEmpty")
     *          }
     *      )
     *   )
     * )
     */
    public function qrcodeAction()
    {
    }

    /**
     * 设备号登录
     *
     * @OA\Post(
     *     path="/login/device",
     *     summary="设备号登录(游客登录)",
     *     tags={"登录模块"},
     *     @OA\Parameter(ref="#/components/parameters/HeaderVersion"),
     *     @OA\Parameter(ref="#/components/parameters/HeaderDeviceType"),
     *     @OA\Parameter(ref="#/components/parameters/HeaderTime"),
     *     @OA\Parameter(ref="#/components/parameters/HeaderDebugKey"),
     *     @OA\Parameter(ref="#/components/parameters/HeaderRequestId"),
     *     @OA\Parameter(ref="#/components/parameters/HeaderToken"),
     *     @OA\Parameter(ref="#/components/parameters/HeaderDeviceId"),
     *     @OA\RequestBody(
     *          required=true,
     *          description="JSON格式数据",
     *          @OA\JsonContent(
     *             required={"clipboard_text"},
     *             @OA\Property(property="clipboard_text", type="string", description="剪贴板内容(用于获取渠道/邀请码)")
     *         )
     *     ),
     *     @OA\Response(
     *      response=200,
     *      description="success",
     *      @OA\JsonContent(
     *          allOf={
     *              @OA\Schema(ref="#/components/schemas/ApiResponseEmpty")
     *          }
     *      )
     *   )
     * )
     */
    public function deviceAction()
    {
        $result = LoginRepository::device($_REQUEST);
        $this->sendSuccessResult($result);
    }

    /**
     * 退出登录
     *
     * @OA\Post(
     *     path="/login/logout",
     *     summary="退出登录",
     *     tags={"登录模块"},
     *     @OA\Parameter(ref="#/components/parameters/HeaderVersion"),
     *     @OA\Parameter(ref="#/components/parameters/HeaderDeviceType"),
     *     @OA\Parameter(ref="#/components/parameters/HeaderTime"),
     *     @OA\Parameter(ref="#/components/parameters/HeaderDebugKey"),
     *     @OA\Parameter(ref="#/components/parameters/HeaderRequestId"),
     *     @OA\Parameter(ref="#/components/parameters/HeaderToken"),
     *     @OA\Parameter(ref="#/components/parameters/HeaderDeviceId"),
     *     @OA\Response(
     *      response=200,
     *      description="success",
     *      @OA\JsonContent(
     *          allOf={
     *              @OA\Schema(ref="#/components/schemas/ApiResponseEmpty")
     *          }
     *      )
     *   )
     * )
     */
    public function logoutAction()
    {
    }
}
