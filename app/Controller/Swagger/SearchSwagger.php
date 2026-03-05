<?php

namespace App\Controller\Swagger;

use App\Controller\BaseApiController;

/**
 * @OA\Tag(name="搜索管理", description="影片，漫画，小说，音频，帖子搜索相关接口")
 */
class SearchSwagger extends BaseApiController
{
    /**
     * 搜索页面
     *
     * @OA\Post(
     *      path="/search/home",
     *      summary="搜索页面",
     *      tags={"搜索管理"},
     *
     *      @OA\Parameter(ref="#/components/parameters/HeaderVersion"),
     *      @OA\Parameter(ref="#/components/parameters/HeaderDeviceType"),
     *      @OA\Parameter(ref="#/components/parameters/HeaderTime"),
     *      @OA\Parameter(ref="#/components/parameters/HeaderDebugKey"),
     *      @OA\Parameter(ref="#/components/parameters/HeaderRequestId"),
     *      @OA\Parameter(ref="#/components/parameters/HeaderToken"),
     *      @OA\Parameter(ref="#/components/parameters/HeaderDeviceId"),
     *
     *      @OA\RequestBody(
     *           required=true,
     *           description="JSON格式数据",
     *           @OA\JsonContent(
     *               required={"deviceId", "data"},
     *               @OA\Property(property="deviceId", type="string", description="非调试时，必传设备ID"),
     *               @OA\Property(property="data", type="object", description="业务数据",
     *                   required={"position"},
     *                   @OA\Property(property="position", type="string", default="movie", description="类型：movie,comics,novel,audio,post"),
     *              )
     *           )
     *       ),
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
     *   )
     */
    public function homeAction()
    {
    }

    /**
     * 影片搜索
     *
     * @OA\Post(
     *      path="/search/movie",
     *      summary="影片搜索",
     *      tags={"搜索管理"},
     *
     *      @OA\Parameter(ref="#/components/parameters/HeaderVersion"),
     *      @OA\Parameter(ref="#/components/parameters/HeaderDeviceType"),
     *      @OA\Parameter(ref="#/components/parameters/HeaderTime"),
     *      @OA\Parameter(ref="#/components/parameters/HeaderDebugKey"),
     *      @OA\Parameter(ref="#/components/parameters/HeaderRequestId"),
     *      @OA\Parameter(ref="#/components/parameters/HeaderToken"),
     *      @OA\Parameter(ref="#/components/parameters/HeaderDeviceId"),
     *
     *      @OA\RequestBody(
     *          required=true,
     *          description="JSON格式数据",
     *          @OA\JsonContent(
     *              required={"deviceId", "token", "data"},
     *              @OA\Property(property="deviceId", type="string", description="非调试时，必传设备ID"),
     *              @OA\Property(property="token", type="string", description="登陆后需传"),
     *              @OA\Property(property="data", type="object", description="搜索参数",
     *                  @OA\Property(property="keywords", type="string", description="搜索关键词"),
     *                  @OA\Property(property="page", type="integer", default=1, description="页码"),
     *                  @OA\Property(property="page_size", type="integer", default=12, description="每页数量，默认12"),
     *                  @OA\Property(property="x_filter", type="string", description="扩展筛选条件"),
     *                  @OA\Property(property="icon", type="string", description="图标标识"),
     *                  @OA\Property(property="position", type="string", description="位置标识"),
     *                  @OA\Property(property="pay_type", type="string", description="付费类型"),
     *                  @OA\Property(property="cat_id", type="string", description="分类ID"),
     *                  @OA\Property(property="tag_id", type="string", description="标签ID"),
     *                  @OA\Property(property="home_id", type="string", description="首页ID"),
     *                  @OA\Property(property="home_ids", type="string", description="首页ID列表（多个用逗号分隔）"),
     *                  @OA\Property(property="canvas", type="string", description="画布标识"),
     *                  @OA\Property(property="ids", type="string", description="指定ID列表（多个用逗号分隔）"),
     *                  @OA\Property(property="not_ids", type="string", description="排除ID列表（多个用逗号分隔）"),
     *                  @OA\Property(property="order", type="string", description="排序字段"),
     *                  @OA\Property(property="status", type="string", description="状态筛选"),
     *                  @OA\Property(property="ad_code", type="string", description="广告代码"),
     *                  @OA\Property(property="language", type="string", description="语言，默认为系统语言"),
     *                  @OA\Property(property="duration", type="string", description="时长筛选")
     *              )
     *          )
     *      ),
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
     *   )
     */
    public function movieAction()
    {
    }

    /**
     * 漫画搜索
     *
     * @OA\Post(
     *      path="/search/comics",
     *      summary="漫画搜索",
     *      tags={"搜索管理"},
     *
     *      @OA\Parameter(ref="#/components/parameters/HeaderVersion"),
     *      @OA\Parameter(ref="#/components/parameters/HeaderDeviceType"),
     *      @OA\Parameter(ref="#/components/parameters/HeaderTime"),
     *      @OA\Parameter(ref="#/components/parameters/HeaderDebugKey"),
     *      @OA\Parameter(ref="#/components/parameters/HeaderRequestId"),
     *      @OA\Parameter(ref="#/components/parameters/HeaderToken"),
     *      @OA\Parameter(ref="#/components/parameters/HeaderDeviceId"),
     *
     *      @OA\RequestBody(
     *          required=true,
     *          description="JSON格式数据",
     *          @OA\JsonContent(
     *              required={"deviceId", "token", "data"},
     *              @OA\Property(property="deviceId", type="string", description="非调试时，必传设备ID"),
     *              @OA\Property(property="token", type="string", description="登陆后需传"),
     *              @OA\Property(property="data", type="object", description="漫画搜索参数",
     *                  @OA\Property(property="keywords", type="string", description="搜索关键词"),
     *                  @OA\Property(property="page", type="integer", default=1, description="页码"),
     *                  @OA\Property(property="page_size", type="integer", default=12, description="每页数量，默认12"),
     *                  @OA\Property(property="icon", type="string", description="图标标识"),
     *                  @OA\Property(property="pay_type", type="string", description="付费类型"),
     *                  @OA\Property(property="cat_id", type="string", description="分类ID"),
     *                  @OA\Property(property="tag_id", type="string", description="标签ID"),
     *                  @OA\Property(property="is_end", type="string", description="是否完结（0:连载中, 1:已完结）"),
     *                  @OA\Property(property="ids", type="string", description="指定ID列表（多个用逗号分隔）"),
     *                  @OA\Property(property="not_ids", type="string", description="排除ID列表（多个用逗号分隔）"),
     *                  @OA\Property(property="order", type="string", description="排序字段"),
     *                  @OA\Property(property="update_date", type="string", description="更新日期筛选"),
     *                  @OA\Property(property="update_status", type="string", description="更新状态"),
     *                  @OA\Property(property="ad_code", type="string", description="广告代码"),
     *                  @OA\Property(property="language", type="string", description="语言，默认为系统语言")
     *              )
     *          )
     *      ),
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
     *   )
     */
    public function comicsAction()
    {
    }

    /**
     * 小说搜索
     *
     * @OA\Post(
     *      path="/search/novel",
     *      summary="小说搜索",
     *      tags={"搜索管理"},
     *
     *      @OA\Parameter(ref="#/components/parameters/HeaderVersion"),
     *      @OA\Parameter(ref="#/components/parameters/HeaderDeviceType"),
     *      @OA\Parameter(ref="#/components/parameters/HeaderTime"),
     *      @OA\Parameter(ref="#/components/parameters/HeaderDebugKey"),
     *      @OA\Parameter(ref="#/components/parameters/HeaderRequestId"),
     *      @OA\Parameter(ref="#/components/parameters/HeaderToken"),
     *      @OA\Parameter(ref="#/components/parameters/HeaderDeviceId"),
     *
     *      @OA\RequestBody(
     *           required=true,
     *           description="JSON格式数据",
     *           @OA\JsonContent(
     *               required={"deviceId", "token", "data"},
     *               @OA\Property(property="deviceId", type="string", description="非调试时，必传设备ID"),
     *               @OA\Property(property="token", type="string", description="登陆后需传"),
     *               @OA\Property(property="data", type="object", description="小说搜索参数",
     *                   @OA\Property(property="keywords", type="string", description="搜索关键词"),
     *                   @OA\Property(property="page", type="integer", default=1, description="页码"),
     *                   @OA\Property(property="page_size", type="integer", default=12, description="每页数量，默认12"),
     *                   @OA\Property(property="icon", type="string", description="图标标识"),
     *                   @OA\Property(property="pay_type", type="string", description="付费类型"),
     *                   @OA\Property(property="cat_id", type="string", description="分类ID"),
     *                   @OA\Property(property="tag_id", type="string", description="标签ID"),
     *                   @OA\Property(property="is_hot", type="string", description="是否热门（0:否, 1:是）"),
     *                   @OA\Property(property="is_new", type="string", description="是否最新（0:否, 1:是）"),
     *                   @OA\Property(property="is_end", type="string", description="是否完结（0:连载中, 1:已完结）"),
     *                   @OA\Property(property="ids", type="string", description="指定ID列表（多个用逗号分隔）"),
     *                   @OA\Property(property="not_ids", type="string", description="排除ID列表（多个用逗号分隔）"),
     *                   @OA\Property(property="order", type="string", description="排序字段"),
     *                   @OA\Property(property="update_date", type="string", description="更新日期筛选"),
     *                   @OA\Property(property="update_status", type="string", description="更新状态"),
     *                   @OA\Property(property="ad_code", type="string", description="广告代码"),
     *                   @OA\Property(property="language", type="string", description="语言，默认为系统语言")
     *              )
     *           )
     *       ),
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
     *   )
     */
    public function novelAction()
    {
    }

    /**
     * 音频搜索
     *
     * @OA\Post(
     *      path="/search/audio",
     *      summary="音频搜索",
     *      tags={"搜索管理"},
     *
     *      @OA\Parameter(ref="#/components/parameters/HeaderVersion"),
     *      @OA\Parameter(ref="#/components/parameters/HeaderDeviceType"),
     *      @OA\Parameter(ref="#/components/parameters/HeaderTime"),
     *      @OA\Parameter(ref="#/components/parameters/HeaderDebugKey"),
     *      @OA\Parameter(ref="#/components/parameters/HeaderRequestId"),
     *      @OA\Parameter(ref="#/components/parameters/HeaderToken"),
     *      @OA\Parameter(ref="#/components/parameters/HeaderDeviceId"),
     *
     *      @OA\RequestBody(
     *           required=true,
     *           description="JSON格式数据",
     *           @OA\JsonContent(
     *               required={"deviceId", "token", "data"},
     *               @OA\Property(property="deviceId", type="string", description="非调试时，必传设备ID"),
     *               @OA\Property(property="token", type="string", description="登陆后需传"),
     *               @OA\Property(property="data", type="object", description="音频搜索参数",
     *                   @OA\Property(property="keywords", type="string", description="搜索关键词"),
     *                   @OA\Property(property="page", type="integer", default=1, description="页码"),
     *                   @OA\Property(property="page_size", type="integer", default=12, description="每页数量，默认12"),
     *                   @OA\Property(property="icon", type="string", description="图标标识"),
     *                   @OA\Property(property="pay_type", type="string", description="付费类型"),
     *                   @OA\Property(property="cat_id", type="string", description="分类ID"),
     *                   @OA\Property(property="tag_id", type="string", description="标签ID"),
     *                   @OA\Property(property="is_hot", type="string", description="是否热门（0:否, 1:是）"),
     *                   @OA\Property(property="is_new", type="string", description="是否最新（0:否, 1:是）"),
     *                   @OA\Property(property="is_end", type="string", description="是否完结（0:连载中, 1:已完结）"),
     *                   @OA\Property(property="ids", type="string", description="指定ID列表（多个用逗号分隔）"),
     *                   @OA\Property(property="not_ids", type="string", description="排除ID列表（多个用逗号分隔）"),
     *                   @OA\Property(property="order", type="string", description="排序字段"),
     *                   @OA\Property(property="update_date", type="string", description="更新日期筛选"),
     *                   @OA\Property(property="update_status", type="string", description="更新状态"),
     *                   @OA\Property(property="ad_code", type="string", description="广告代码"),
     *                   @OA\Property(property="language", type="string", description="语言，默认为系统语言")
     *              )
     *           )
     *       ),
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
     *   )
     */
    public function audioAction()
    {
    }

    /**
     * 帖子搜索
     *
     * @OA\Post(
     *      path="/search/post",
     *      summary="帖子搜索",
     *      tags={"搜索管理"},
     *
     *      @OA\Parameter(ref="#/components/parameters/HeaderVersion"),
     *      @OA\Parameter(ref="#/components/parameters/HeaderDeviceType"),
     *      @OA\Parameter(ref="#/components/parameters/HeaderTime"),
     *      @OA\Parameter(ref="#/components/parameters/HeaderDebugKey"),
     *      @OA\Parameter(ref="#/components/parameters/HeaderRequestId"),
     *      @OA\Parameter(ref="#/components/parameters/HeaderToken"),
     *      @OA\Parameter(ref="#/components/parameters/HeaderDeviceId"),
     *
     *      @OA\RequestBody(
     *           required=true,
     *           description="JSON格式数据",
     *           @OA\JsonContent(
     *               required={"deviceId", "token", "data"},
     *               @OA\Property(property="deviceId", type="string", description="非调试时，必传设备ID"),
     *               @OA\Property(property="token", type="string", description="登陆后需传"),
     *               @OA\Property(property="data", type="object", description="帖子搜索参数",
     *                   @OA\Property(property="keywords", type="string", description="搜索关键词"),
     *                   @OA\Property(property="page", type="integer", default=1, description="页码"),
     *                   @OA\Property(property="page_size", type="integer", default=12, description="每页数量，默认12"),
     *                   @OA\Property(property="tag_id", type="string", description="标签ID"),
     *                   @OA\Property(property="global_top", type="string", description="全局置顶（0:否, 1:是）"),
     *                   @OA\Property(property="home_top", type="string", description="首页置顶（0:否, 1:是）"),
     *                   @OA\Property(property="ids", type="string", description="指定ID列表（多个用逗号分隔）"),
     *                   @OA\Property(property="not_ids", type="string", description="排除ID列表（多个用逗号分隔）"),
     *                   @OA\Property(property="home_id", type="string", description="首页ID"),
     *                   @OA\Property(property="home_ids", type="string", description="首页ID列表（多个用逗号分隔）"),
     *                   @OA\Property(property="position", type="string", description="位置标识"),
     *                   @OA\Property(property="order", type="string", description="排序字段"),
     *                   @OA\Property(property="status", type="string", description="状态筛选"),
     *                   @OA\Property(property="type", type="string", description="帖子类型"),
     *                   @OA\Property(property="pay_type", type="string", description="付费类型"),
     *                   @OA\Property(property="ad_code", type="string", description="广告代码"),
     *                   @OA\Property(property="language", type="string", description="语言，默认为系统语言")
     *               )
     *           )
     *       ),
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
     *   )
     */
    public function postAction()
    {
    }
}
