<?php

namespace App\Controller\Swagger;

use App\Controller\BaseApiController;

/**
 * @OA\Tag(
 *     name="系统管理",
 *     description="系统信息、数据跟踪、应用中心、域名相关接口"
 * )
 */
class SystemSwagger extends BaseApiController
{
    /**
     * 系统信息
     *
     * @OA\Post(
     *     path="/system/info",
     *     summary="获取系统信息",
     *     tags={"系统管理"},
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
     *         required=true,
     *         description="JSON格式数据",
     *         @OA\JsonContent(
     *             required={"deviceId"},
     *             @OA\Property(property="deviceId", type="string", description="非调试时，必传设备ID"),
     *             @OA\Property(property="data", type="object", description="业务参数")
     *         )
     *     ),
     *     @OA\Response(
     *      response=200,
     *      description="success",
     *      @OA\JsonContent(
     *          allOf={
     *              @OA\Schema(ref="#/components/schemas/ApiResponse"),
     *             @OA\Schema(
     *                  type="object",
     *                 @OA\Property(
     *                      property="data",
     *                      ref="#/components/schemas/SystemInfoData"
     *                  )
     *              )
     *          }
     *      )
     *   )
     * )
     */
    public function infoAction()
    {
    }

    /**
     * 数据跟踪
     *
     * @OA\Post(
     *     path="/system/track",
     *     summary="数据跟踪",
     *     tags={"系统管理"},
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
     *         required=true,
     *         description="JSON格式数据",
     *         @OA\JsonContent(
     *             required={"deviceId", "data"},
     *             @OA\Property(property="deviceId", type="string", description="非调试时，必传设备ID"),
     *             @OA\Property(property="data", type="object", description="跟踪参数",
     *                 @OA\Property(property="object_type", type="string", description="对象类型"),
     *                 @OA\Property(property="object_id", type="string", description="对象ID"),
     *                 @OA\Property(property="object_name", type="string", description="对象名称")
     *             )
     *         )
     *     ),
     *
     *     @OA\Response(
     *         response=200,
     *         description="请求响应成功",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", default="y", enum={"y", "n"}),
     *             @OA\Property(property="data", type="object", nullable=true),
     *             @OA\Property(property="error", type="string", nullable=true),
     *             @OA\Property(property="errorCode", type="integer", nullable=true),
     *             @OA\Property(property="time", type="string", nullable=true)
     *         )
     *     )
     * )
     */
    public function trackAction()
    {
    }

    /**
     * 应用中心
     *
     * @OA\Post(
     *     path="/system/app-store",
     *     summary="应用中心",
     *     tags={"系统管理"},
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
     *         required=true,
     *         description="JSON格式数据",
     *         @OA\JsonContent(
     *             required={"deviceId"},
     *             @OA\Property(property="deviceId", type="string", description="非调试时，必传设备ID")
     *         )
     *     ),
     *
     *     @OA\Response(
     *         response=200,
     *         description="请求响应成功",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", default="y", enum={"y", "n"}),
     *             @OA\Property(property="data", type="array",
     *                 @OA\Items(
     *                     @OA\Property(property="code", type="string"),
     *                     @OA\Property(property="name", type="string"),
     *                     @OA\Property(property="items", type="array", @OA\Items(type="object"))
     *                 )
     *             ),
     *             @OA\Property(property="error", type="string", nullable=true),
     *             @OA\Property(property="errorCode", type="integer", nullable=true),
     *             @OA\Property(property="time", type="string", nullable=true)
     *         )
     *     )
     * )
     */
    public function appStoreAction()
    {
    }

    /**
     * 获取最新地址
     *
     * @OA\Post(
     *     path="/system/domains",
     *     summary="获取最新地址",
     *     tags={"系统管理"},
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
     *         required=true,
     *         description="JSON格式数据",
     *         @OA\JsonContent(
     *             required={"deviceId"},
     *             @OA\Property(property="deviceId", type="string", description="非调试时，必传设备ID")
     *         )
     *     ),
     *
     *     @OA\Response(
     *         response=200,
     *         description="请求响应成功",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", default="y", enum={"y", "n"}),
     *             @OA\Property(property="data", type="object", nullable=true),
     *             @OA\Property(property="error", type="string", nullable=true),
     *             @OA\Property(property="errorCode", type="integer", nullable=true),
     *             @OA\Property(property="time", type="string", nullable=true)
     *         )
     *     )
     * )
     */
    public function domainsAction()
    {
    }
}
