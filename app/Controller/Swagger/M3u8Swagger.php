<?php

namespace App\Controller\Swagger;

use App\Controller\BaseApiController;

/**
 * m3u8 播放接口
 *
 * @OA\Tag(
 *     name="M3U8",
 *     description="M3U8 播放与分发接口"
 * )
 */
class M3u8Swagger extends BaseApiController
{
    public function initialize()
    {
    }

    /**
     * m3u8 播放地址
     *
     * 根据加密 token 返回 m3u8 播放内容，用于隐藏真实播放地址。
     *
     * @param string $token
     *
     * @OA\Get(
     *     path="/m3u8/p/{token}.m3u8",
     *     summary="获取 m3u8 播放内容",
     *     tags={"M3U8"},
     *     @OA\Parameter(
     *         name="token",
     *         in="path",
     *         required=true,
     *         description="加密后的播放 token（不含 .m3u8 后缀）",
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="返回 m3u8 播放内容",
     *         @OA\MediaType(
     *             mediaType="application/vnd.apple.mpegurl"
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="token 无效或已过期"
     *     )
     * )
     */
    public function pAction($token = '')
    {
    }
}
