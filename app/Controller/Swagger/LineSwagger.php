<?php

namespace App\Controller\Swagger;

use App\Controller\BaseApiController;

/**
 * h5k线路检查
 *
 * @OA\Tag(
 *     name="Line",
 *     description="H5线路检测"
 * )
 */
class LineSwagger extends BaseApiController
{
    public function initialize()
    {
    }

    /**
     * 1.获取线路
     *
     * @OA\Post(
     *     path="/line/index",
     *     tags={"Line"},
     *     summary="获取H5线路",
     *     description="获取指定索引的H5线路地址，支持 JSONP",
     *     @OA\RequestBody(
     *         required=false,
     *         @OA\JsonContent(
     *             @OA\Property(
     *                 property="id",
     *                 type="integer",
     *                 default=0,
     *                 description="线路索引"
     *             ),
     *             @OA\Property(
     *                 property="callback",
     *                 type="string",
     *                 description="JSONP 回调函数名"
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="成功",
     *         @OA\JsonContent(
     *             @OA\Property(
     *                 property="url",
     *                 type="string",
     *                 description="线路地址"
     *             )
     *         )
     *     )
     * )
     */
    public function indexAction()
    {
    }

    /**
     * 2.ping
     *
     * @OA\Post(
     *     path="/line/ping",
     *     tags={"Line"},
     *     summary="线路Ping检测",
     *     description="检测线路是否可用，支持 JSONP",
     *     @OA\RequestBody(
     *         required=false,
     *         @OA\JsonContent(
     *             @OA\Property(
     *                 property="callback",
     *                 type="string",
     *                 description="JSONP 回调函数名"
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="成功",
     *         @OA\JsonContent(
     *             @OA\Property(
     *                 property="status",
     *                 type="string",
     *                 example="y",
     *                 description="线路状态"
     *             )
     *         )
     *     )
     * )
     */
    public function pingAction()
    {
    }
}
