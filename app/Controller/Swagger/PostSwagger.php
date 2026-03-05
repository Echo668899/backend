<?php

namespace App\Controller\Swagger;

use App\Controller\BaseApiController;
use App\Exception\BusinessException;

/**
 * @OA\Tag(name="帖子模块", description="帖子相关接口")
 */
class PostSwagger extends BaseApiController
{
    /**
     * nav下模块,常规模块,带items
     *
     * @OA\Post(
     *     path="/post/navBlock",
     *     summary="获取Nav下的常规模块(带items)",
     *      tags={"帖子模块"},
     *     @OA\Parameter(ref="#/components/parameters/HeaderVersion"),
     *     @OA\Parameter(ref="#/components/parameters/HeaderDeviceType"),
     *     @OA\Parameter(ref="#/components/parameters/HeaderTime"),
     *     @OA\Parameter(ref="#/components/parameters/HeaderDebugKey"),
     *     @OA\Parameter(ref="#/components/parameters/HeaderRequestId"),
     *     @OA\Parameter(ref="#/components/parameters/HeaderToken"),
     *     @OA\Parameter(ref="#/components/parameters/HeaderDeviceId"),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"id"},
     *             @OA\Property(property="id", type="integer", description="Nav ID"),
     *             @OA\Property(property="page", type="integer", description="页码", default=1)
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
     * @return void
     * @throws \Phalcon\Storage\Exception
     */
    public function navBlockAction()
    {
    }

    /**
     * nav下filter
     *
     * @OA\Post(
     *     path="/post/navFilter",
     *     summary="获取Nav下的筛选器",
     *      tags={"帖子模块"},
     *     @OA\Parameter(ref="#/components/parameters/HeaderVersion"),
     *     @OA\Parameter(ref="#/components/parameters/HeaderDeviceType"),
     *     @OA\Parameter(ref="#/components/parameters/HeaderTime"),
     *     @OA\Parameter(ref="#/components/parameters/HeaderDebugKey"),
     *     @OA\Parameter(ref="#/components/parameters/HeaderRequestId"),
     *     @OA\Parameter(ref="#/components/parameters/HeaderToken"),
     *     @OA\Parameter(ref="#/components/parameters/HeaderDeviceId"),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"id"},
     *             @OA\Property(property="id", type="integer", description="Nav ID")
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
     * @return void
     */
    public function navFilterAction()
    {
    }

    /**
     * 模块详情
     *
     * @OA\Post(
     *     path="/post/blockDetail",
     *     summary="获取模块详情",
     *      tags={"帖子模块"},
     *     @OA\Parameter(ref="#/components/parameters/HeaderVersion"),
     *     @OA\Parameter(ref="#/components/parameters/HeaderDeviceType"),
     *     @OA\Parameter(ref="#/components/parameters/HeaderTime"),
     *     @OA\Parameter(ref="#/components/parameters/HeaderDebugKey"),
     *     @OA\Parameter(ref="#/components/parameters/HeaderRequestId"),
     *     @OA\Parameter(ref="#/components/parameters/HeaderToken"),
     *     @OA\Parameter(ref="#/components/parameters/HeaderDeviceId"),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"id"},
     *             @OA\Property(property="id", type="integer", description="模块 ID")
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
     *
     * @return void
     * @throws BusinessException
     */
    public function blockDetailAction()
    {
    }

    /**
     * 标签详情
     *
     * @OA\Post(
     *     path="/post/tagDetail",
     *     summary="获取标签详情",
     *      tags={"帖子模块"},
     *     @OA\Parameter(ref="#/components/parameters/HeaderVersion"),
     *     @OA\Parameter(ref="#/components/parameters/HeaderDeviceType"),
     *     @OA\Parameter(ref="#/components/parameters/HeaderTime"),
     *     @OA\Parameter(ref="#/components/parameters/HeaderDebugKey"),
     *     @OA\Parameter(ref="#/components/parameters/HeaderRequestId"),
     *     @OA\Parameter(ref="#/components/parameters/HeaderToken"),
     *     @OA\Parameter(ref="#/components/parameters/HeaderDeviceId"),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"id"},
     *             @OA\Property(property="id", type="integer", description="标签 ID")
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
     * @return void
     */
    public function tagDetailAction()
    {
    }

    /**
     * 去点赞
     *
     * @OA\Post(
     *     path="/post/doLove",
     *     summary="点赞/取消点赞",
     *      tags={"帖子模块"},
     *     @OA\Parameter(ref="#/components/parameters/HeaderVersion"),
     *     @OA\Parameter(ref="#/components/parameters/HeaderDeviceType"),
     *     @OA\Parameter(ref="#/components/parameters/HeaderTime"),
     *     @OA\Parameter(ref="#/components/parameters/HeaderDebugKey"),
     *     @OA\Parameter(ref="#/components/parameters/HeaderRequestId"),
     *     @OA\Parameter(ref="#/components/parameters/HeaderToken"),
     *     @OA\Parameter(ref="#/components/parameters/HeaderDeviceId"),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"id"},
     *             @OA\Property(property="id", type="string", description="帖子 ID")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="请求响应成功",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="status", type="string", example="y")
     *         )
     *     )
     * )
     *
     * @return void
     * @throws \App\Exception\BusinessException
     */
    public function doLoveAction()
    {
    }

    /**
     * 点赞列表
     *
     * @OA\Post(
     *     path="/post/love",
     *     summary="获取点赞列表",
     *      tags={"帖子模块"},
     *     @OA\Parameter(ref="#/components/parameters/HeaderVersion"),
     *     @OA\Parameter(ref="#/components/parameters/HeaderDeviceType"),
     *     @OA\Parameter(ref="#/components/parameters/HeaderTime"),
     *     @OA\Parameter(ref="#/components/parameters/HeaderDebugKey"),
     *     @OA\Parameter(ref="#/components/parameters/HeaderRequestId"),
     *     @OA\Parameter(ref="#/components/parameters/HeaderToken"),
     *     @OA\Parameter(ref="#/components/parameters/HeaderDeviceId"),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"home_id", "page", "cursor"},
     *             @OA\Property(property="home_id", type="string", description="用户 ID (查看谁的点赞列表)"),
     *             @OA\Property(property="page", type="integer", description="页码", default=1),
     *             @OA\Property(property="cursor", type="string", description="游标")
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
     * @return void
     */
    public function loveAction()
    {
    }

    /**
     * 去收藏
     *
     * @OA\Post(
     *     path="/post/doFavorite",
     *     summary="收藏/取消收藏",
     *      tags={"帖子模块"},
     *     @OA\Parameter(ref="#/components/parameters/HeaderVersion"),
     *     @OA\Parameter(ref="#/components/parameters/HeaderDeviceType"),
     *     @OA\Parameter(ref="#/components/parameters/HeaderTime"),
     *     @OA\Parameter(ref="#/components/parameters/HeaderDebugKey"),
     *     @OA\Parameter(ref="#/components/parameters/HeaderRequestId"),
     *     @OA\Parameter(ref="#/components/parameters/HeaderToken"),
     *     @OA\Parameter(ref="#/components/parameters/HeaderDeviceId"),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"id"},
     *             @OA\Property(property="id", type="string", description="帖子 ID")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="请求响应成功",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="status", type="string", example="y")
     *         )
     *     )
     * )
     * @return void
     * @throws \App\Exception\BusinessException
     */
    public function doFavoriteAction()
    {
    }

    /**
     * 收藏列表
     *
     * @OA\Post(
     *     path="/post/favorite",
     *     summary="获取收藏列表",
     *      tags={"帖子模块"},
     *     @OA\Parameter(ref="#/components/parameters/HeaderVersion"),
     *     @OA\Parameter(ref="#/components/parameters/HeaderDeviceType"),
     *     @OA\Parameter(ref="#/components/parameters/HeaderTime"),
     *     @OA\Parameter(ref="#/components/parameters/HeaderDebugKey"),
     *     @OA\Parameter(ref="#/components/parameters/HeaderRequestId"),
     *     @OA\Parameter(ref="#/components/parameters/HeaderToken"),
     *     @OA\Parameter(ref="#/components/parameters/HeaderDeviceId"),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"home_id", "page", "cursor"},
     *             @OA\Property(property="home_id", type="string", description="用户 ID (查看谁的收藏列表)"),
     *             @OA\Property(property="page", type="integer", description="页码", default=1),
     *             @OA\Property(property="cursor", type="string", description="游标")
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
     * @return void
     */
    public function favoriteAction()
    {
    }

    /**
     * 删除历史
     *
     * @OA\Post(
     *     path="/post/delHistory",
     *     summary="删除浏览历史",
     *      tags={"帖子模块"},
     *     @OA\Parameter(ref="#/components/parameters/HeaderVersion"),
     *     @OA\Parameter(ref="#/components/parameters/HeaderDeviceType"),
     *     @OA\Parameter(ref="#/components/parameters/HeaderTime"),
     *     @OA\Parameter(ref="#/components/parameters/HeaderDebugKey"),
     *     @OA\Parameter(ref="#/components/parameters/HeaderRequestId"),
     *     @OA\Parameter(ref="#/components/parameters/HeaderToken"),
     *     @OA\Parameter(ref="#/components/parameters/HeaderDeviceId"),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"ids"},
     *             @OA\Property(property="ids", type="string", description="逗号分割的帖子ID，或者传 'all' 删除所有")
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
     * @return void
     */
    public function delHistoryAction()
    {
    }

    /**
     * 历史列表
     *
     * @OA\Post(
     *     path="/post/history",
     *     summary="获取浏览历史列表",
     *      tags={"帖子模块"},
     *     @OA\Parameter(ref="#/components/parameters/HeaderVersion"),
     *     @OA\Parameter(ref="#/components/parameters/HeaderDeviceType"),
     *     @OA\Parameter(ref="#/components/parameters/HeaderTime"),
     *     @OA\Parameter(ref="#/components/parameters/HeaderDebugKey"),
     *     @OA\Parameter(ref="#/components/parameters/HeaderRequestId"),
     *     @OA\Parameter(ref="#/components/parameters/HeaderToken"),
     *     @OA\Parameter(ref="#/components/parameters/HeaderDeviceId"),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"page", "cursor"},
     *             @OA\Property(property="page", type="integer", description="页码", default=1),
     *             @OA\Property(property="cursor", type="string", description="游标")
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
     * @return void
     */
    public function historyAction()
    {
    }

    /**
     * 帖子详情
     *
     * @OA\Post(
     *     path="/post/detail",
     *     summary="获取帖子详情",
     *      tags={"帖子模块"},
     *     @OA\Parameter(ref="#/components/parameters/HeaderVersion"),
     *     @OA\Parameter(ref="#/components/parameters/HeaderDeviceType"),
     *     @OA\Parameter(ref="#/components/parameters/HeaderTime"),
     *     @OA\Parameter(ref="#/components/parameters/HeaderDebugKey"),
     *     @OA\Parameter(ref="#/components/parameters/HeaderRequestId"),
     *     @OA\Parameter(ref="#/components/parameters/HeaderToken"),
     *     @OA\Parameter(ref="#/components/parameters/HeaderDeviceId"),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"id"},
     *             @OA\Property(property="id", type="string", description="帖子 ID")
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
     * @return void
     */
    public function detailAction()
    {
    }

    /**
     * 购买帖子
     *
     * @OA\Post(
     *     path="/post/doBuy",
     *     summary="购买帖子",
     *      tags={"帖子模块"},
     *     @OA\Parameter(ref="#/components/parameters/HeaderVersion"),
     *     @OA\Parameter(ref="#/components/parameters/HeaderDeviceType"),
     *     @OA\Parameter(ref="#/components/parameters/HeaderTime"),
     *     @OA\Parameter(ref="#/components/parameters/HeaderDebugKey"),
     *     @OA\Parameter(ref="#/components/parameters/HeaderRequestId"),
     *     @OA\Parameter(ref="#/components/parameters/HeaderToken"),
     *     @OA\Parameter(ref="#/components/parameters/HeaderDeviceId"),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"id"},
     *             @OA\Property(property="id", type="string", description="帖子 ID")
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
     * @return void
     * @throws BusinessException
     * @throws \Phalcon\Storage\Exception
     */
    public function doBuyAction()
    {
    }

    /**
     * 购买记录
     *
     * @OA\Post(
     *     path="/post/buyLog",
     *     summary="获取购买记录",
     *      tags={"帖子模块"},
     *     @OA\Parameter(ref="#/components/parameters/HeaderVersion"),
     *     @OA\Parameter(ref="#/components/parameters/HeaderDeviceType"),
     *     @OA\Parameter(ref="#/components/parameters/HeaderTime"),
     *     @OA\Parameter(ref="#/components/parameters/HeaderDebugKey"),
     *     @OA\Parameter(ref="#/components/parameters/HeaderRequestId"),
     *     @OA\Parameter(ref="#/components/parameters/HeaderToken"),
     *     @OA\Parameter(ref="#/components/parameters/HeaderDeviceId"),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"page", "cursor"},
     *             @OA\Property(property="page", type="integer", description="页码", default=1),
     *             @OA\Property(property="cursor", type="string", description="游标")
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
     * @return void
     */
    public function buyLogAction()
    {
    }

    /**
     * 发布
     *
     * @OA\Post(
     *     path="/post/create",
     *     summary="获取发布帖子所需的配置信息",
     *      tags={"帖子模块"},
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
     * @return void
     */
    public function createAction()
    {
    }

    /**
     * 发帖
     *
     * @OA\Post(
     *     path="/post/doCreate",
     *     summary="发布帖子",
     *      tags={"帖子模块"},
     *     @OA\Parameter(ref="#/components/parameters/HeaderVersion"),
     *     @OA\Parameter(ref="#/components/parameters/HeaderDeviceType"),
     *     @OA\Parameter(ref="#/components/parameters/HeaderTime"),
     *     @OA\Parameter(ref="#/components/parameters/HeaderDebugKey"),
     *     @OA\Parameter(ref="#/components/parameters/HeaderRequestId"),
     *     @OA\Parameter(ref="#/components/parameters/HeaderToken"),
     *     @OA\Parameter(ref="#/components/parameters/HeaderDeviceId"),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"title", "content", "type", "images"},
     *             description="发布帖子所需的参数，具体字段取决于 createAction 返回的配置",
     *             @OA\Property(property="title", type="string", description="标题"),
     *             @OA\Property(property="content", type="string", description="内容"),
     *             @OA\Property(property="type", type="string", description="帖子类型"),
     *             @OA\Property(property="images", type="array", @OA\Items(type="string"), description="图片列表")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="请求响应成功",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="status", type="string", example="y")
     *         )
     *     )
     * )
     * @return void
     * @throws BusinessException
     */
    public function doCreateAction()
    {
    }

    /**
     * 站点地图
     *
     * @OA\Post(
     *     path="/post/sitemap",
     *     summary="获取站点地图",
     *      tags={"帖子模块"},
     *     @OA\Parameter(ref="#/components/parameters/HeaderVersion"),
     *     @OA\Parameter(ref="#/components/parameters/HeaderDeviceType"),
     *     @OA\Parameter(ref="#/components/parameters/HeaderTime"),
     *     @OA\Parameter(ref="#/components/parameters/HeaderDebugKey"),
     *     @OA\Parameter(ref="#/components/parameters/HeaderRequestId"),
     *     @OA\Parameter(ref="#/components/parameters/HeaderToken"),
     *     @OA\Parameter(ref="#/components/parameters/HeaderDeviceId"),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"page"},
     *             @OA\Property(property="page", type="integer", description="页码", default=1)
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
     * @return void
     */
    public function sitemapAction()
    {
    }
}
