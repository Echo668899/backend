<?php

namespace App\Controller\Swagger;

use App\Controller\BaseApiController;

/**
 * 弹幕
 *
 * @OA\Tag(name="弹幕管理", description="弹幕相关接口")
 */
class DanmakuSwagger extends BaseApiController
{
    /**
     * 彈幕留言
     * @throws \App\Exception\BusinessException
     *
     * @OA\Post(
     *       path="/danmaku/do",
     *       summary="彈幕留言",
     *       tags={"弹幕管理"},
     *
     *       @OA\Parameter(ref="#/components/parameters/HeaderVersion"),
     *       @OA\Parameter(ref="#/components/parameters/HeaderDeviceType"),
     *       @OA\Parameter(ref="#/components/parameters/HeaderTime"),
     *       @OA\Parameter(ref="#/components/parameters/HeaderDebugKey"),
     *       @OA\Parameter(ref="#/components/parameters/HeaderRequestId"),
     *       @OA\Parameter(ref="#/components/parameters/HeaderToken"),
     *       @OA\Parameter(ref="#/components/parameters/HeaderDeviceId"),
     *
     *       @OA\RequestBody(
     *            required=true,
     *            description="JSON格式数据",
     *            @OA\JsonContent(
     *                required={"deviceId", "token", "data"},
     *                @OA\Property(property="deviceId", type="string", description="非调试时，必传设备ID"),
     *                @OA\Property(property="token", type="string", description="登陆后需传"),
     *                @OA\Property(property="data", type="object", description="业务数据",
     *                      required={"pos", "object_id", "object_type", "content"},
     *                      @OA\Property(property="object_id", type="string", description=""),
     *                      @OA\Property(property="sub_id", type="string", description=""),
     *                      @OA\Property(property="object_type", type="string", description=""),
     *                      @OA\Property(property="pos", type="string", description=""),
     *                      @OA\Property(property="size", type="string", default="12", description=""),
     *                      @OA\Property(property="color", type="string", default="16777215", description=""),
     *                      @OA\Property(property="pool", type="string", default="0", description=""),
     *                      @OA\Property(property="content", type="string", description=""),
     *                )
     *            )
     *        ),
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
     *    )
     */
    public function doAction()
    {
    }

    /**
     * 弹幕列表
     * @return void
     * @throws \App\Exception\BusinessException
     *
     * @OA\Post(
     *        path="/danmaku/list",
     *        summary="弹幕列表",
     *        tags={"弹幕管理"},
     *
     *        @OA\Parameter(ref="#/components/parameters/HeaderVersion"),
     *        @OA\Parameter(ref="#/components/parameters/HeaderDeviceType"),
     *        @OA\Parameter(ref="#/components/parameters/HeaderTime"),
     *        @OA\Parameter(ref="#/components/parameters/HeaderDebugKey"),
     *        @OA\Parameter(ref="#/components/parameters/HeaderRequestId"),
     *        @OA\Parameter(ref="#/components/parameters/HeaderToken"),
     *        @OA\Parameter(ref="#/components/parameters/HeaderDeviceId"),
     *
     *        @OA\RequestBody(
     *             required=true,
     *             description="JSON格式数据",
     *             @OA\JsonContent(
     *                 required={"deviceId", "data"},
     *                 @OA\Property(property="deviceId", type="string", description="非调试时，必传设备ID"),
     *                 @OA\Property(property="token", type="string", description="登陆后需传"),
     *                 @OA\Property(property="data", type="object", description="业务数据",
     *                       required={"object_id", "object_type"},
     *                       @OA\Property(property="object_id", type="string", description=""),
     *                       @OA\Property(property="sub_id", type="string", description=""),
     *                       @OA\Property(property="object_type", type="string", description=""),
     *                       @OA\Property(property="start_pos", type="integer", description=""),
     *                       @OA\Property(property="end_pos", type="integer", default="12", description=""),
     *                 )
     *             )
     *         ),
     *
     *        @OA\Response(
     *             response=200,
     *             description="请求响应成功",
     *             @OA\JsonContent(
     *                 @OA\Property(property="status", type="string", default="y", enum={"y", "n"}),
     *                 @OA\Property(property="data", type="object", nullable=true),
     *                 @OA\Property(property="error", type="string", nullable=true),
     *                 @OA\Property(property="errorCode", type="integer", nullable=true),
     *                 @OA\Property(property="time", type="string", nullable=true)
     *             )
     *         )
     *     )
     */
    public function listAction()
    {
    }
}
