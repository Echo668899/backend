<?php

namespace App\Controller\Swagger;

use App\Controller\BaseApiController;
use App\Exception\BusinessException;

/**
 * @OA\Tag(name="小说模块", description="小说模块 API")
 */
class NovelSwagger extends BaseApiController
{
    /**
     * nav下模块,常规模块,带items
     * @OA\Post(
     * path="/novel/navBlock",
     * summary="导航下模块列表",
     * tags={"小说模块"},
     * @OA\Parameter(ref="#/components/parameters/HeaderVersion"),
     * @OA\Parameter(ref="#/components/parameters/HeaderDeviceType"),
     * @OA\Parameter(ref="#/components/parameters/HeaderTime"),
     * @OA\Parameter(ref="#/components/parameters/HeaderDebugKey"),
     * @OA\Parameter(ref="#/components/parameters/HeaderRequestId"),
     * @OA\Parameter(ref="#/components/parameters/HeaderToken"),
     * @OA\Parameter(ref="#/components/parameters/HeaderDeviceId"),
     * @OA\Parameter(name="id", in="query", description="分类ID", required=true, @OA\Schema(type="integer")),
     * @OA\Parameter(name="page", in="query", description="页码", @OA\Schema(type="integer", default=1)),
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
     * @OA\Post(
     * path="/novel/navFilter",
     * summary="导航下过滤器",
     * tags={"小说模块"},
     * @OA\Parameter(ref="#/components/parameters/HeaderVersion"),
     * @OA\Parameter(ref="#/components/parameters/HeaderDeviceType"),
     * @OA\Parameter(ref="#/components/parameters/HeaderTime"),
     * @OA\Parameter(ref="#/components/parameters/HeaderDebugKey"),
     * @OA\Parameter(ref="#/components/parameters/HeaderRequestId"),
     * @OA\Parameter(ref="#/components/parameters/HeaderToken"),
     * @OA\Parameter(ref="#/components/parameters/HeaderDeviceId"),
     * @OA\Parameter(name="id", in="query", description="分类ID", required=true, @OA\Schema(type="integer")),
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
     * @OA\Post(
     * path="/novel/filter",
     * summary="筛选页面配置",
     * tags={"小说模块"},
     * @OA\Parameter(ref="#/components/parameters/HeaderVersion"),
     * @OA\Parameter(ref="#/components/parameters/HeaderDeviceType"),
     * @OA\Parameter(ref="#/components/parameters/HeaderTime"),
     * @OA\Parameter(ref="#/components/parameters/HeaderDebugKey"),
     * @OA\Parameter(ref="#/components/parameters/HeaderRequestId"),
     * @OA\Parameter(ref="#/components/parameters/HeaderToken"),
     * @OA\Parameter(ref="#/components/parameters/HeaderDeviceId"),
     * @OA\Response(
     * response=200,
     * description="成功",
     * @OA\JsonContent(
     * type="object",
     * @OA\Property(property="category", type="object", description="题材分类",
     * @OA\Property(property="field", type="string", example="cat_id"),
     * @OA\Property(property="select", type="string", example="only"),
     * @OA\Property(property="items", type="array", @OA\Items(
     * @OA\Property(property="name", type="string", example="玄幻"),
     * @OA\Property(property="value", type="string", example="玄幻")
     * ))
     * ),
     * @OA\Property(property="tag", type="object", description="标签组(多选)",
     * @OA\Property(property="field", type="string", example="tag_id"),
     * @OA\Property(property="select", type="string", example="multiple"),
     * @OA\Property(property="items", type="array", @OA\Items(
     * @OA\Property(property="name", type="string", description="分组名称", example="角色"),
     * @OA\Property(property="items", type="array", description="标签列表", @OA\Items(
     * @OA\Property(property="name", type="string", example="热血"),
     * @OA\Property(property="value", type="string", example="101")
     * ))
     * ))
     * ),
     * @OA\Property(property="pay_type", type="object", description="付费类型",
     * @OA\Property(property="field", type="string", example="pay_type"),
     * @OA\Property(property="select", type="string", example="only"),
     * @OA\Property(property="items", type="array", @OA\Items(
     * @OA\Property(property="name", type="string", example="免费"),
     * @OA\Property(property="value", type="string", example="free")
     * ))
     * ),
     * @OA\Property(property="update_type", type="object", description="更新状态",
     * @OA\Property(property="field", type="string", example="update_status"),
     * @OA\Property(property="select", type="string", example="only"),
     * @OA\Property(property="items", type="array", @OA\Items(
     * @OA\Property(property="name", type="string", example="连载"),
     * @OA\Property(property="value", type="string", example="n")
     * ))
     * ),
     * @OA\Property(property="sort", type="object", description="排序方式",
     * @OA\Property(property="field", type="string", example="order"),
     * @OA\Property(property="select", type="string", example="only"),
     * @OA\Property(property="items", type="array", @OA\Items(
     * @OA\Property(property="name", type="string", example="人气推荐"),
     * @OA\Property(property="value", type="string", example="favorite")
     * ))
     * )
     * )
     * )
     * )
     * @return void
     */
    public function filterAction()
    {
    }

    /**
     * 模块详情
     * @OA\Post(
     * path="/novel/blockDetail",
     * summary="模块详情",
     * tags={"小说模块"},
     * @OA\Parameter(ref="#/components/parameters/HeaderVersion"),
     * @OA\Parameter(ref="#/components/parameters/HeaderDeviceType"),
     * @OA\Parameter(ref="#/components/parameters/HeaderTime"),
     * @OA\Parameter(ref="#/components/parameters/HeaderDebugKey"),
     * @OA\Parameter(ref="#/components/parameters/HeaderRequestId"),
     * @OA\Parameter(ref="#/components/parameters/HeaderToken"),
     * @OA\Parameter(ref="#/components/parameters/HeaderDeviceId"),
     * @OA\Parameter(name="id", in="query", description="模块ID", required=true, @OA\Schema(type="integer")),
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
     * @throws \App\Exception\BusinessException
     */
    public function blockDetailAction()
    {
    }

    /**
     * 小说详情
     * @OA\Post(
     * path="/novel/detail",
     * summary="小说详情",
     * tags={"小说模块"},
     * @OA\Parameter(ref="#/components/parameters/HeaderVersion"),
     * @OA\Parameter(ref="#/components/parameters/HeaderDeviceType"),
     * @OA\Parameter(ref="#/components/parameters/HeaderTime"),
     * @OA\Parameter(ref="#/components/parameters/HeaderDebugKey"),
     * @OA\Parameter(ref="#/components/parameters/HeaderRequestId"),
     * @OA\Parameter(ref="#/components/parameters/HeaderToken"),
     * @OA\Parameter(ref="#/components/parameters/HeaderDeviceId"),
     * @OA\Parameter(name="id", in="query", description="内容ID", required=true, @OA\Schema(type="string")),
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
     * @throws \App\Exception\BusinessException
     */
    public function detailAction()
    {
    }

    /**
     * 章节详情-阅读
     * @OA\Post(
     * path="/novel/chapter",
     * summary="章节详情-阅读",
     * tags={"小说模块"},
     * @OA\Parameter(ref="#/components/parameters/HeaderVersion"),
     * @OA\Parameter(ref="#/components/parameters/HeaderDeviceType"),
     * @OA\Parameter(ref="#/components/parameters/HeaderTime"),
     * @OA\Parameter(ref="#/components/parameters/HeaderDebugKey"),
     * @OA\Parameter(ref="#/components/parameters/HeaderRequestId"),
     * @OA\Parameter(ref="#/components/parameters/HeaderToken"),
     * @OA\Parameter(ref="#/components/parameters/HeaderDeviceId"),
     * @OA\Parameter(name="id", in="query", description="章节ID", required=true, @OA\Schema(type="string")),
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
     * @throws \App\Exception\BusinessException
     * @throws \Phalcon\Storage\Exception
     */
    public function chapterAction()
    {
    }

    /**
     * 标签详情
     * @OA\Post(
     * path="/novel/tagDetail",
     * summary="标签详情列表",
     * tags={"小说模块"},
     * @OA\Parameter(ref="#/components/parameters/HeaderVersion"),
     * @OA\Parameter(ref="#/components/parameters/HeaderDeviceType"),
     * @OA\Parameter(ref="#/components/parameters/HeaderTime"),
     * @OA\Parameter(ref="#/components/parameters/HeaderDebugKey"),
     * @OA\Parameter(ref="#/components/parameters/HeaderRequestId"),
     * @OA\Parameter(ref="#/components/parameters/HeaderToken"),
     * @OA\Parameter(ref="#/components/parameters/HeaderDeviceId"),
     * @OA\Parameter(name="id", in="query", description="标签ID", required=true, @OA\Schema(type="integer")),
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
     * @OA\Post(
     * path="/novel/doLove",
     * summary="去点赞/取消点赞",
     * tags={"小说模块"},
     * @OA\Parameter(ref="#/components/parameters/HeaderVersion"),
     * @OA\Parameter(ref="#/components/parameters/HeaderDeviceType"),
     * @OA\Parameter(ref="#/components/parameters/HeaderTime"),
     * @OA\Parameter(ref="#/components/parameters/HeaderDebugKey"),
     * @OA\Parameter(ref="#/components/parameters/HeaderRequestId"),
     * @OA\Parameter(ref="#/components/parameters/HeaderToken"),
     * @OA\Parameter(ref="#/components/parameters/HeaderDeviceId"),
     * @OA\RequestBody(
     * required=true,
     * @OA\JsonContent(
     * @OA\Property(property="id", type="string", description="内容ID")
     * )
     * ),
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
     * @throws \App\Exception\BusinessException
     */
    public function doLoveAction()
    {
    }

    /**
     * 点赞列表
     * @OA\Post(
     * path="/novel/love",
     * summary="用户点赞列表",
     * tags={"小说模块"},
     * @OA\Parameter(ref="#/components/parameters/HeaderVersion"),
     * @OA\Parameter(ref="#/components/parameters/HeaderDeviceType"),
     * @OA\Parameter(ref="#/components/parameters/HeaderTime"),
     * @OA\Parameter(ref="#/components/parameters/HeaderDebugKey"),
     * @OA\Parameter(ref="#/components/parameters/HeaderRequestId"),
     * @OA\Parameter(ref="#/components/parameters/HeaderToken"),
     * @OA\Parameter(ref="#/components/parameters/HeaderDeviceId"),
     * @OA\Parameter(name="home_id", in="query", description="用户ID", required=true, @OA\Schema(type="string")),
     * @OA\Parameter(name="page", in="query", @OA\Schema(type="integer", default=1)),
     * @OA\Parameter(name="cursor", in="query", description="游标", @OA\Schema(type="string")),
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
     * @OA\Post(
     * path="/novel/doFavorite",
     * summary="去收藏/取消收藏",
     * tags={"小说模块"},
     * @OA\Parameter(ref="#/components/parameters/HeaderVersion"),
     * @OA\Parameter(ref="#/components/parameters/HeaderDeviceType"),
     * @OA\Parameter(ref="#/components/parameters/HeaderTime"),
     * @OA\Parameter(ref="#/components/parameters/HeaderDebugKey"),
     * @OA\Parameter(ref="#/components/parameters/HeaderRequestId"),
     * @OA\Parameter(ref="#/components/parameters/HeaderToken"),
     * @OA\Parameter(ref="#/components/parameters/HeaderDeviceId"),
     * @OA\RequestBody(
     * required=true,
     * @OA\JsonContent(
     * @OA\Property(property="id", type="string", description="内容ID")
     * )
     * ),
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
     * @throws \App\Exception\BusinessException
     */
    public function doFavoriteAction()
    {
    }

    /**
     * 收藏列表
     * @OA\Post(
     * path="/novel/favorite",
     * summary="用户收藏列表",
     * tags={"小说模块"},
     * @OA\Parameter(ref="#/components/parameters/HeaderVersion"),
     * @OA\Parameter(ref="#/components/parameters/HeaderDeviceType"),
     * @OA\Parameter(ref="#/components/parameters/HeaderTime"),
     * @OA\Parameter(ref="#/components/parameters/HeaderDebugKey"),
     * @OA\Parameter(ref="#/components/parameters/HeaderRequestId"),
     * @OA\Parameter(ref="#/components/parameters/HeaderToken"),
     * @OA\Parameter(ref="#/components/parameters/HeaderDeviceId"),
     * @OA\Parameter(name="home_id", in="query", required=true, @OA\Schema(type="string")),
     * @OA\Parameter(name="page", in="query", @OA\Schema(type="integer", default=1)),
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
     * @OA\Post(
     * path="/novel/doHistory",
     * summary="添加/更新阅读记录",
     * tags={"小说模块"},
     * @OA\Parameter(ref="#/components/parameters/HeaderVersion"),
     * @OA\Parameter(ref="#/components/parameters/HeaderDeviceType"),
     * @OA\Parameter(ref="#/components/parameters/HeaderTime"),
     * @OA\Parameter(ref="#/components/parameters/HeaderDebugKey"),
     * @OA\Parameter(ref="#/components/parameters/HeaderRequestId"),
     * @OA\Parameter(ref="#/components/parameters/HeaderToken"),
     * @OA\Parameter(ref="#/components/parameters/HeaderDeviceId"),
     * @OA\RequestBody(
     * required=true,
     * @OA\JsonContent(
     * @OA\Property(property="id", type="string", description="小说ID"),
     * @OA\Property(property="chapter_id", type="string", description="章节ID"),
     * @OA\Property(property="index", type="integer", description="章节索引")
     * )
     * ),
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
     * @OA\Post(
     * path="/novel/delHistory",
     * summary="删除阅读记录",
     * tags={"小说模块"},
     * @OA\Parameter(ref="#/components/parameters/HeaderVersion"),
     * @OA\Parameter(ref="#/components/parameters/HeaderDeviceType"),
     * @OA\Parameter(ref="#/components/parameters/HeaderTime"),
     * @OA\Parameter(ref="#/components/parameters/HeaderDebugKey"),
     * @OA\Parameter(ref="#/components/parameters/HeaderRequestId"),
     * @OA\Parameter(ref="#/components/parameters/HeaderToken"),
     * @OA\Parameter(ref="#/components/parameters/HeaderDeviceId"),
     * @OA\RequestBody(
     * required=true,
     * @OA\JsonContent(
     * @OA\Property(property="ids", type="string", description="逗号分割ID或是all")
     * )
     * ),
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
     * @OA\Post(
     * path="/novel/history",
     * summary="阅读历史列表",
     * tags={"小说模块"},
     * @OA\Parameter(ref="#/components/parameters/HeaderVersion"),
     * @OA\Parameter(ref="#/components/parameters/HeaderDeviceType"),
     * @OA\Parameter(ref="#/components/parameters/HeaderTime"),
     * @OA\Parameter(ref="#/components/parameters/HeaderDebugKey"),
     * @OA\Parameter(ref="#/components/parameters/HeaderRequestId"),
     * @OA\Parameter(ref="#/components/parameters/HeaderToken"),
     * @OA\Parameter(ref="#/components/parameters/HeaderDeviceId"),
     * @OA\Parameter(name="page", in="query", @OA\Schema(type="integer", default=1)),
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
     * 购买小说
     * @OA\Post(
     * path="/novel/doBuy",
     * summary="购买小说/章节",
     * tags={"小说模块"},
     * @OA\Parameter(ref="#/components/parameters/HeaderVersion"),
     * @OA\Parameter(ref="#/components/parameters/HeaderDeviceType"),
     * @OA\Parameter(ref="#/components/parameters/HeaderTime"),
     * @OA\Parameter(ref="#/components/parameters/HeaderDebugKey"),
     * @OA\Parameter(ref="#/components/parameters/HeaderRequestId"),
     * @OA\Parameter(ref="#/components/parameters/HeaderToken"),
     * @OA\Parameter(ref="#/components/parameters/HeaderDeviceId"),
     * @OA\RequestBody(
     * required=true,
     * @OA\JsonContent(
     * @OA\Property(property="id", type="string", description="小说ID")
     * )
     * ),
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
    public function doBuyAction()
    {
    }

    /**
     * 购买记录
     * @OA\Post(
     * path="/novel/buyLog",
     * summary="购买记录列表",
     * tags={"小说模块"},
     * @OA\Parameter(ref="#/components/parameters/HeaderVersion"),
     * @OA\Parameter(ref="#/components/parameters/HeaderDeviceType"),
     * @OA\Parameter(ref="#/components/parameters/HeaderTime"),
     * @OA\Parameter(ref="#/components/parameters/HeaderDebugKey"),
     * @OA\Parameter(ref="#/components/parameters/HeaderRequestId"),
     * @OA\Parameter(ref="#/components/parameters/HeaderToken"),
     * @OA\Parameter(ref="#/components/parameters/HeaderDeviceId"),
     * @OA\Parameter(name="page", in="query", @OA\Schema(type="integer", default=1)),
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
     * 站点地图
     * @OA\Post(
     * path="/novel/sitemap",
     * summary="小说站点地图",
     * tags={"小说模块"},
     * @OA\Parameter(ref="#/components/parameters/HeaderVersion"),
     * @OA\Parameter(ref="#/components/parameters/HeaderDeviceType"),
     * @OA\Parameter(ref="#/components/parameters/HeaderTime"),
     * @OA\Parameter(ref="#/components/parameters/HeaderDebugKey"),
     * @OA\Parameter(ref="#/components/parameters/HeaderRequestId"),
     * @OA\Parameter(ref="#/components/parameters/HeaderToken"),
     * @OA\Parameter(ref="#/components/parameters/HeaderDeviceId"),
     * @OA\Parameter(name="page", in="query", @OA\Schema(type="integer", default=1)),
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
