<?php

namespace App\Controller\Swagger;

use App\Core\Controller\BaseController;

/**
 * 落地页接口
 *
 * @OA\Tag(
 *     name="Web",
 *     description="落地页与前端公共接口"
 * )
 */
class WebSwagger extends BaseController
{
    /**
     * 获取站点配置
     *
     * 返回落地页所需的基础配置、域名信息及下载地址。
     * 数据经过 base64 编码，用于避免直接暴露真实内容。
     *
     * @throws \Phalcon\Storage\Exception
     *
     * @OA\Get(
     *     path="/web/config",
     *     summary="获取落地页配置",
     *     tags={"Web"},
     *     @OA\Response(
     *         response=200,
     *         description="配置数据（base64 编码后的 JSON）",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="status", type="string", example="y"),
     *             @OA\Property(property="data", type="string", description="base64 编码后的配置内容")
     *         )
     *     )
     * )
     */
    public function configAction()
    {
    }

    /**
     * 上报事件
     *
     * 用于前端或落地页上报用户行为事件。
     *
     * 请求数据示例：
     * {event:"", session_id:"", client_ip:"", channel_code:"", share_code:"", payload:{}}
     *
     * @OA\Post(
     *     path="/web/doEvent",
     *     summary="上报前端事件",
     *     tags={"Web"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="event", type="string", description="事件名称"),
     *             @OA\Property(property="session_id", type="string", description="会话 ID"),
     *             @OA\Property(property="client_ip", type="string", description="客户端 IP"),
     *             @OA\Property(property="channel_code", type="string", description="渠道标识"),
     *             @OA\Property(property="share_code", type="string", description="分享码"),
     *             @OA\Property(property="payload", type="object", description="事件附加数据")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="事件接收成功"
     *     )
     * )
     */
    public function doEventAction()
    {
        # # payload
        // {event:"","session_id":"","client_ip":"","channel_code":"","share_code":"","payload":{}}
    }
}
