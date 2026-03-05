<?php

namespace App\Controller\Swagger;

use App\Controller\BaseApiController;
use App\Exception\BusinessException;
use Phalcon\Storage\Exception;

/**
 * @OA\Tag(name="影视管理", description="影视相关接口")
 */
class MovieSwagger extends BaseApiController
{
    /**
     * nav下模块,常规模块,带items
     *
     * @OA\Post(
     *       path="/movie/navBlock",
     *       summary="视频nav下模块",
     *       tags={"影视管理"},
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
     *         @OA\JsonContent(
     *             @OA\Property(property="id", type="integer", example="1"),
     *             @OA\Property(property="page", type="integer", example="1")
     *         )
     *     ),
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
     *       path="/movie/navFilter",
     *       summary="视频nav下filter",
     *       tags={"影视管理"},
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
     *         @OA\JsonContent(
     *             @OA\Property(property="id", type="integer", example="1")
     *         )
     *     ),
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
     * )
     * @return void
     */
    public function navFilterAction()
    {
    }

    /**
     * 筛选页面
     *
     * @OA\Post(
     *      path="/movie/filter",
     *      summary="搜索条件接口",
     *      tags={"影视管理"},
     *
     *     @OA\Parameter(ref="#/components/parameters/HeaderVersion"),
     *     @OA\Parameter(ref="#/components/parameters/HeaderDeviceType"),
     *     @OA\Parameter(ref="#/components/parameters/HeaderTime"),
     *     @OA\Parameter(ref="#/components/parameters/HeaderDebugKey"),
     *     @OA\Parameter(ref="#/components/parameters/HeaderRequestId"),
     *     @OA\Parameter(ref="#/components/parameters/HeaderToken"),
     *     @OA\Parameter(ref="#/components/parameters/HeaderDeviceId"),
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
     * )
     * @return void
     */
    public function filterAction()
    {
    }

    /**
     * 模块详情
     *
     * @OA\Post(
     *       path="/movie/blockDetail",
     *       summary="模块详情",
     *       tags={"影视管理"},
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
     *         @OA\JsonContent(
     *             @OA\Property(property="id", type="integer", example="1")
     *         )
     *     ),
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
     * )
     * @return void
     * @throws BusinessException
     */
    public function blockDetailAction()
    {
    }

    /**
     * 详情
     *
     * @OA\Post(
     *       path="/movie/detail",
     *       summary="详情",
     *       tags={"影视管理"},
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
     *         @OA\JsonContent(
     *             @OA\Property(property="id", type="string"),
     *             @OA\Property(property="lid", type="string")
     *         )
     *     ),
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
     * )
     * @return void
     * @throws BusinessException|Exception
     */
    public function detailAction()
    {
    }

    /**
     * 标签详情
     *
     * @OA\Post(
     *       path="/movie/tagDetail",
     *       summary="标签详情",
     *       tags={"影视管理"},
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
     *         @OA\JsonContent(
     *             @OA\Property(property="id", type="integer", example="1")
     *         )
     *     ),
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
     * )
     *
     * @return void
     * @throws BusinessException
     */
    public function tagDetailAction()
    {
    }

    /**
     * 去点赞
     *
     * @OA\Post(
     *       path="/movie/doLove",
     *       summary="去点赞",
     *       tags={"影视管理"},
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
     *         @OA\JsonContent(
     *             required={"id"},
     *             @OA\Property(property="id", type="string")
     *         )
     *     ),
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
     * )
     * @throws BusinessException
     */
    public function doLoveAction()
    {
    }
    /**
     * 去点踩
     *
     * @OA\Post(
     *       path="/movie/doDisLove",
     *       summary="去点踩",
     *       tags={"影视管理"},
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
     *         @OA\JsonContent(
     *             required={"id"},
     *             @OA\Property(property="id", type="string")
     *         )
     *     ),
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
     * )
     *
     * @throws BusinessException
     */
    public function doDisLoveAction()
    {
    }

    /**
     * 点赞列表
     *
     *  @OA\Post(
     *       path="/movie/love",
     *       summary="点赞列表",
     *       tags={"影视管理"},
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
     *         @OA\JsonContent(
     *             required={"home_id"},
     *             @OA\Property(property="page", type="integer", example="1"),
     *             @OA\Property(property="home_id", type="string"),
     *             @OA\Property(property="cursor", type="string")
     *         )
     *     ),
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
     * )
     *
     * @return void
     */
    public function loveAction()
    {
    }

    /**
     * 去收藏
     *
     * @OA\Post(
     *       path="/movie/doFavorite",
     *       summary="去收藏",
     *       tags={"影视管理"},
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
     *         @OA\JsonContent(
     *             required={"id"},
     *             @OA\Property(property="id", type="string", example="1")
     *         )
     *     ),
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
     * )
     * @throws BusinessException
     */
    public function doFavoriteAction()
    {
    }

    /**
     * 收藏列表
     *
     * @OA\Post(
     *       path="/movie/favorite",
     *       summary="收藏列表",
     *       tags={"影视管理"},
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
     *         @OA\JsonContent(
     *             required={"home_id"},
     *             @OA\Property(property="page", type="integer", example="1"),
     *             @OA\Property(property="home_id", type="string"),
     *             @OA\Property(property="cursor", type="string")
     *         )
     *     ),
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
     * )
     * @return void
     */
    public function favoriteAction()
    {
    }

    /**
     * 添加播放记录
     *
     * @OA\Post(
     *       path="/movie/doHistory",
     *       summary="添加播放记录",
     *       tags={"影视管理"},
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
     *         @OA\JsonContent(
     *             @OA\Property(property="id", type="string"),
     *             @OA\Property(property="lid", type="string"),
     *             @OA\Property(property="code", type="string"),
     *             @OA\Property(property="time", type="integer"),
     *             @OA\Property(property="view_time", type="integer"),
     *             @OA\Property(property="event", type="string")
     *         )
     *     ),
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
     * )
     */
    public function doHistoryAction()
    {
    }

    /**
     * 删除历史
     *
     * @OA\Post(
     *       path="/movie/delHistory",
     *       summary="删除历史",
     *       tags={"影视管理"},
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
     *         @OA\JsonContent(
     *             @OA\Property(property="ids", type="string", example="1,2,3")
     *         )
     *     ),
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
     *       path="/movie/history",
     *       summary="历史列表",
     *       tags={"影视管理"},
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
     *         @OA\JsonContent(
     *             @OA\Property(property="page", type="integer", example="1"),
     *             @OA\Property(property="cursor", type="string")
     *         )
     *     ),
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
     *
     * )
     * @return void
     */
    public function historyAction()
    {
    }

    /**
     * 购买视频
     *
     * @OA\Post(
     *       path="/movie/doBuy",
     *       summary="购买视频",
     *       tags={"影视管理"},
     *
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
     *         @OA\JsonContent(
     *             @OA\Property(property="id", type="string"),
     *             @OA\Property(property="lid", type="string")
     *         )
     *     ),
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
     *
     * )
     * @return void
     * @throws BusinessException
     */
    public function doBuyAction()
    {
    }

    /**
     * 购买记录
     *
     * @OA\Post(
     *       path="/movie/buyLog",
     *       summary="购买记录",
     *       tags={"影视管理"},
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
     *         @OA\JsonContent(
     *             @OA\Property(property="page", type="integer"),
     *             @OA\Property(property="cursor", type="string")
     *         )
     *     ),
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
     * )
     * @return void
     */
    public function buyLogAction()
    {
    }

    /**
     * 播放地址
     *
     * @OA\Post(
     *       path="/movie/play",
     *       summary="播放地址",
     *       tags={"影视管理"},
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
     *         @OA\JsonContent(
     *             required={"id"},
     *             @OA\Property(property="id", type="integer")
     *         )
     *     ),
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
     * )
     *
     * @return void
     * @throws BusinessException
     */
    public function playAction()
    {
    }

    /**
     * 下载
     *
     * @OA\Post(
     *       path="/movie/doDownload",
     *       summary="下载",
     *       tags={"影视管理"},
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
     *         @OA\JsonContent(
     *             required={"id", "lid"},
     *             @OA\Property(property="id", type="string"),
     *             @OA\Property(property="lid", type="string")
     *         )
     *     ),
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
     *
     * )
     * @return void
     * @throws BusinessException
     */
    public function doDownloadAction()
    {
    }

    /**
     * 发布
     *
     * @OA\Post(
     *       path="/movie/create",
     *       summary="发布",
     *       tags={"影视管理"},
     *
     *     @OA\Parameter(ref="#/components/parameters/HeaderVersion"),
     *     @OA\Parameter(ref="#/components/parameters/HeaderDeviceType"),
     *     @OA\Parameter(ref="#/components/parameters/HeaderTime"),
     *     @OA\Parameter(ref="#/components/parameters/HeaderDebugKey"),
     *     @OA\Parameter(ref="#/components/parameters/HeaderRequestId"),
     *     @OA\Parameter(ref="#/components/parameters/HeaderToken"),
     *     @OA\Parameter(ref="#/components/parameters/HeaderDeviceId"),
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
     *
     * )
     * @return void
     */
    public function createAction()
    {
    }

    /**
     * 保存视频
     *
     * @OA\Post(
     *       path="/movie/doCreate",
     *       summary="保存视频",
     *       tags={"影视管理"},
     *
     *     @OA\Parameter(ref="#/components/parameters/HeaderVersion"),
     *     @OA\Parameter(ref="#/components/parameters/HeaderDeviceType"),
     *     @OA\Parameter(ref="#/components/parameters/HeaderTime"),
     *     @OA\Parameter(ref="#/components/parameters/HeaderDebugKey"),
     *     @OA\Parameter(ref="#/components/parameters/HeaderRequestId"),
     *     @OA\Parameter(ref="#/components/parameters/HeaderToken"),
     *     @OA\Parameter(ref="#/components/parameters/HeaderDeviceId"),
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
     *
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
     *       path="/movie/sitemap",
     *       summary="站点地图",
     *       tags={"影视管理"},
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
     *         @OA\JsonContent(
     *             @OA\Property(property="page", type="integer")
     *         )
     *     ),
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
     *
     * )
     * @return void
     */
    public function sitemapAction()
    {
    }

    /**
     * 视频详情带推荐列表
     * MovieShortSearchIdFilterView
     * @return void
     * @throws BusinessException
     * @throws Exception
     *
     * @OA\Post(
     *     path="/movie/similarSearch",
     *     summary="视频详情带推荐列表",
     *     tags={"影视管理"},
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
     *             required={"id"},
     *             @OA\Property(property="id", type="string", description="视频ID（必填）", example="123"),
     *             @OA\Property(property="page", type="integer", description="页码", default=1),
     *             @OA\Property(property="page_size", type="integer", description="每页数量", default=12),
     *             @OA\Property(property="keywords", type="string", description="搜索关键词"),
     *             @OA\Property(property="x_filter", type="string", description="扩展过滤"),
     *             @OA\Property(property="icon", type="string", description="图标过滤"),
     *             @OA\Property(property="position", type="string", description="位置过滤", example="all"),
     *             @OA\Property(property="pay_type", type="string", description="付费类型", example="free/money"),
     *             @OA\Property(property="cat_id", type="string", description="分类ID，多个用逗号分隔"),
     *             @OA\Property(property="tag_id", type="string", description="标签ID，多个用逗号分隔（系统会自动设置相似视频的标签）"),
     *             @OA\Property(property="home_id", type="string", description="用户ID"),
     *             @OA\Property(property="home_ids", type="string", description="多个用户ID，用逗号分隔"),
     *             @OA\Property(property="canvas", type="string", description="画布类型", example="short/vertical"),
     *             @OA\Property(property="ids", type="string", description="指定视频ID列表，用逗号分隔"),
     *             @OA\Property(property="not_ids", type="string", description="排除视频ID列表，用逗号分隔（系统会自动排除当前视频）"),
     *             @OA\Property(property="order", type="string", description="排序方式", example="new/hot/click7/click30/love7/love30"),
     *             @OA\Property(property="status", type="string", description="状态过滤"),
     *             @OA\Property(property="ad_code", type="string", description="广告代码"),
     *             @OA\Property(property="language", type="string", description="语言"),
     *             @OA\Property(property="duration", type="string", description="时长过滤，格式：gte300/lte300/gt300/lt300（秒）")
     *         )
     *     ),
     *
     *     @OA\Response(
     *         response=200,
     *         description="请求响应成功",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", default="y", enum={"y", "n"}),
     *             @OA\Property(property="data", type="object", nullable=true,
     *                 @OA\Property(property="data", type="array", items=@OA\Items(type="object"),
     *                     @OA\Property(property="id", type="string", description="视频ID"),
     *                     @OA\Property(property="name", type="string", description="视频标题"),
     *                     @OA\Property(property="user_id", type="array", items=@OA\Items(type="object"), description="用户信息",
     *                         @OA\Property(property="id", type="string", description="用户ID"),
     *                         @OA\Property(property="nickname", type="string", description="用户昵称"),
     *                         @OA\Property(property="username", type="string", description="用户名"),
     *                         @OA\Property(property="headico", type="string", description="用户头像")
     *                     ),
     *                     @OA\Property(property="type", type="string", description="类型", example="video"),
     *                     @OA\Property(property="img", type="string", description="视频封面"),
     *                     @OA\Property(property="pay_type", type="string", description="付费类型"),
     *                     @OA\Property(property="money", type="string", description="价格"),
     *                     @OA\Property(property="category", type="string", description="分类"),
     *                     @OA\Property(property="click", type="string", description="播放次数"),
     *                     @OA\Property(property="love", type="string", description="点赞数"),
     *                     @OA\Property(property="favorite", type="string", description="收藏数"),
     *                     @OA\Property(property="icon", type="string", description="小图标"),
     *                     @OA\Property(property="duration", type="string", description="视频时长或集数"),
     *                     @OA\Property(property="width", type="string", description="视频宽度"),
     *                     @OA\Property(property="height", type="string", description="视频高度"),
     *                     @OA\Property(property="canvas", type="string", description="画布类型"),
     *                     @OA\Property(property="img_width", type="string", description="图片宽度"),
     *                     @OA\Property(property="img_height", type="string", description="图片高度"),
     *                     @OA\Property(property="time_label", type="string", description="时间标签"),
     *                     @OA\Property(property="show_at", type="string", description="发布时间"),
     *                     @OA\Property(property="tags", type="array", items=@OA\Items(type="object"), description="标签列表",
     *                         @OA\Property(property="id", type="string", description="标签ID"),
     *                         @OA\Property(property="name", type="string", description="标签名称")
     *                     ),
     *                     @OA\Property(property="kouling", type="string", description="口令"),
     *                     @OA\Property(property="link", type="string", description="链接")
     *                 ),
     *                 @OA\Property(property="total", type="string", description="总数"),
     *                 @OA\Property(property="current_page", type="string", description="当前页"),
     *                 @OA\Property(property="page_size", type="string", description="每页数量"),
     *                 @OA\Property(property="last_page", type="string", description="最后一页")
     *             ),
     *             @OA\Property(property="error", type="string", nullable=true),
     *             @OA\Property(property="errorCode", type="integer", nullable=true),
     *             @OA\Property(property="time", type="string", nullable=true)
     *         )
     *     )
     * )
     */
    public function similarSearchAction()
    {
    }
}
