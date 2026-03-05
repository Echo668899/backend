<?php

namespace App\Controller\Swagger;

use App\Controller\BaseApiController;

/**
 * @OA\Tag(
 *     name="有声",
 *     description="有声相关接口"
 * )
 */
class AudioSwagger extends BaseApiController
{
    /**
     * nav下模块,常规模块,带items
     *
     * @OA\Post(
     *     path="/audio/navBlock",
     *     summary="nav下模块",
     *     tags={"有声"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             @OA\Property(property="id", type="integer", description="nav id"),
     *             @OA\Property(property="page", type="integer", default=1, description="页码")
     *         )
     *     ),
     *     @OA\Response(response=200, description="success")
     * )
     */
    public function navBlockAction()
    {
    }

    /**
     * nav下filter
     *
     * @OA\Post(
     *     path="/audio/navFilter",
     *     summary="nav下筛选",
     *     tags={"有声"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             @OA\Property(property="id", type="integer", description="nav id")
     *         )
     *     ),
     *     @OA\Response(response=200, description="success")
     * )
     */
    public function navFilterAction()
    {
    }

    /**
     * 筛选页面
     *
     * @OA\Post(
     *     path="/audio/filter",
     *     summary="筛选页面",
     *     tags={"有声"},
     *     @OA\Response(response=200, description="success")
     * )
     */
    public function filterAction()
    {
    }

    /**
     * 模块详情
     *
     * @OA\Post(
     *     path="/audio/blockDetail",
     *     summary="模块详情",
     *     tags={"有声"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             @OA\Property(property="id", type="integer", description="模块ID")
     *         )
     *     ),
     *     @OA\Response(response=200, description="success")
     * )
     */
    public function blockDetailAction()
    {
    }

    /**
     * 详情
     *
     * @OA\Post(
     *     path="/audio/detail",
     *     summary="有声详情",
     *     tags={"有声"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             @OA\Property(property="id", type="string", description="有声ID"),
     *             @OA\Property(property="lid", type="string", description="线路ID")
     *         )
     *     ),
     *     @OA\Response(response=200, description="success")
     * )
     */
    public function detailAction()
    {
    }

    /**
     * 标签详情
     *
     * @OA\Post(
     *     path="/audio/tagDetail",
     *     summary="标签详情",
     *     tags={"有声"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             @OA\Property(property="id", type="integer", description="标签ID")
     *         )
     *     ),
     *     @OA\Response(response=200, description="success")
     * )
     */
    public function tagDetailAction()
    {
    }

    /**
     * 去点赞
     *
     * @OA\Post(
     *     path="/audio/doLove",
     *     summary="点赞/取消点赞",
     *     tags={"有声"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             @OA\Property(property="id", type="string", description="有声ID")
     *         )
     *     ),
     *     @OA\Response(response=200, description="success")
     * )
     */
    public function doLoveAction()
    {
    }

    /**
     * 点赞列表
     *
     * @OA\Post(
     *     path="/audio/love",
     *     summary="点赞列表",
     *     tags={"有声"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             @OA\Property(property="home_id", type="string", description="home id"),
     *             @OA\Property(property="page", type="integer", default=1),
     *             @OA\Property(property="cursor", type="string")
     *         )
     *     ),
     *     @OA\Response(response=200, description="success")
     * )
     */
    public function loveAction()
    {
    }

    /**
     * 去收藏
     *
     * @OA\Post(
     *     path="/audio/doFavorite",
     *     summary="收藏/取消收藏",
     *     tags={"有声"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             @OA\Property(property="id", type="string", description="有声ID")
     *         )
     *     ),
     *     @OA\Response(response=200, description="success")
     * )
     */
    public function doFavoriteAction()
    {
    }

    /**
     * 收藏列表
     *
     * @OA\Post(
     *     path="/audio/favorite",
     *     summary="收藏列表",
     *     tags={"有声"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             @OA\Property(property="home_id", type="string"),
     *             @OA\Property(property="page", type="integer", default=1),
     *             @OA\Property(property="cursor", type="string")
     *         )
     *     ),
     *     @OA\Response(response=200, description="success")
     * )
     */
    public function favoriteAction()
    {
    }

    /**
     * 添加播放记录
     *
     * @OA\Post(
     *     path="/audio/doHistory",
     *     summary="添加播放记录",
     *     tags={"有声"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             @OA\Property(property="id", type="string"),
     *             @OA\Property(property="lid", type="string"),
     *             @OA\Property(property="code", type="string"),
     *             @OA\Property(property="time", type="integer")
     *         )
     *     ),
     *     @OA\Response(response=200, description="success")
     * )
     */
    public function doHistoryAction()
    {
    }

    /**
     * 删除历史
     *
     * @OA\Post(
     *     path="/audio/delHistory",
     *     summary="删除播放历史",
     *     tags={"有声"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             @OA\Property(property="ids", type="string", description="id列表或all")
     *         )
     *     ),
     *     @OA\Response(response=200, description="success")
     * )
     */
    public function delHistoryAction()
    {
    }

    /**
     * 历史列表
     *
     * @OA\Post(
     *     path="/audio/history",
     *     summary="播放历史列表",
     *     tags={"有声"},
     *     @OA\RequestBody(
     *         @OA\JsonContent(
     *             @OA\Property(property="page", type="integer", default=1),
     *             @OA\Property(property="cursor", type="string")
     *         )
     *     ),
     *     @OA\Response(response=200, description="success")
     * )
     */
    public function historyAction()
    {
    }

    /**
     * 购买有声
     *
     * @OA\Post(
     *     path="/audio/doBuy",
     *     summary="购买有声",
     *     tags={"有声"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             @OA\Property(property="id", type="string", description="有声ID")
     *         )
     *     ),
     *     @OA\Response(response=200, description="success")
     * )
     */
    public function doBuyAction()
    {
    }

    /**
     * 购买记录
     *
     * @OA\Post(
     *     path="/audio/buyLog",
     *     summary="购买记录",
     *     tags={"有声"},
     *     @OA\RequestBody(
     *         @OA\JsonContent(
     *             @OA\Property(property="page", type="integer", default=1),
     *             @OA\Property(property="cursor", type="string")
     *         )
     *     ),
     *     @OA\Response(response=200, description="success")
     * )
     */
    public function buyLogAction()
    {
    }

    /**
     * 站点地图
     *
     * @OA\Post(
     *     path="/audio/sitemap",
     *     summary="站点地图",
     *     tags={"有声"},
     *     @OA\RequestBody(
     *         @OA\JsonContent(
     *             @OA\Property(property="page", type="integer", default=1)
     *         )
     *     ),
     *     @OA\Response(response=200, description="success")
     * )
     */
    public function sitemapAction()
    {
    }
}
