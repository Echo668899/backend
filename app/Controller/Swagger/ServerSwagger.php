<?php

namespace App\Controller\Swagger;

use App\Controller\BaseApiController;

/**
 * @OA\Tag(
 *     name="服务器管理",
 *     description="服务器状态检测及分词词库相关接口"
 * )
 */
class ServerSwagger extends BaseApiController
{
    public function initialize()
    {
    }

    /**
     * 系统检测
     *
     * @OA\Post(
     *     path="/server/check",
     *     summary="系统检测",
     *     description="获取服务器运行状态信息",
     *     tags={"服务器管理"},
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
     *         required=false,
     *         description="无需业务参数",
     *         @OA\JsonContent(
     *             @OA\Property(property="deviceId", type="string", description="非调试时，设备ID")
     *         )
     *     ),
     *
     *     @OA\Response(
     *         response=200,
     *         description="服务器状态信息",
     *         @OA\JsonContent(
     *             type="object",
     *             additionalProperties=true
     *         )
     *     )
     * )
     */
    public function checkAction()
    {
    }

    /**
     * 词库
     *
     * @OA\Post(
     *     path="/server/ik",
     *     summary="获取 IK 分词词库",
     *     description="返回服务器当前加载的 IK 分词词库内容（纯文本）",
     *     tags={"服务器管理"},
     *
     *     @OA\Response(
     *         response=200,
     *         description="词库内容",
     *         @OA\MediaType(
     *             mediaType="text/plain",
     *             @OA\Schema(type="string")
     *         )
     *     )
     * )
     */
    public function ikAction()
    {
    }
}
