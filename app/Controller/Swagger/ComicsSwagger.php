<?php

namespace App\Controller\Swagger;

use App\Controller\BaseApiController;
use App\Exception\BusinessException;
use App\Repositories\Api\ComicsRepository;
use Phalcon\Storage\Exception;

/**
 * @OA\Tag(name="漫画管理", description="漫画相关接口")
 */
class ComicsSwagger extends BaseApiController
{
    /**
     * nav下模块,常规模块,带items
     * @return void
     * @throws Exception|BusinessException
     *
     * @OA\Post(
     *     path="/comics/navBlock",
     *     summary="nav模块",
     *     tags={"漫画管理"},
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
     *          required=true,
     *          description="JSON格式数据",
     *          @OA\JsonContent(
     *              required={"deviceId", "data"},
     *              @OA\Property(property="deviceId", type="string", description="非调试时，必传设备ID"),
     *              @OA\Property(property="data", type="object", description="业务数据",
     *                 required={"id"},
     *                 @OA\Property(property="id", type="integer", default="10", description=""),
     *                 @OA\Property(property="page", type="integer", default="1", description="分页数"),
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
     *  )
     */
    public function navBlockAction()
    {
        $navId  = $this->getRequest('id', 'int');
        $page   = $this->getRequest('page', 'int', 1);
        $result = ComicsRepository::navBlock($navId, $page);
        $this->sendSuccessResult($result);
    }

    /**
     * nav下filter
     * @return void
     * @throws BusinessException
     * @throws Exception
     *
     * @OA\Post(
     *    path="/comics/navFilter",
     *    summary="nav下filter",
     *    tags={"漫画管理"},
     *
     *    @OA\Parameter(ref="#/components/parameters/HeaderVersion"),
     *    @OA\Parameter(ref="#/components/parameters/HeaderDeviceType"),
     *    @OA\Parameter(ref="#/components/parameters/HeaderTime"),
     *    @OA\Parameter(ref="#/components/parameters/HeaderDebugKey"),
     *    @OA\Parameter(ref="#/components/parameters/HeaderRequestId"),
     *    @OA\Parameter(ref="#/components/parameters/HeaderToken"),
     *    @OA\Parameter(ref="#/components/parameters/HeaderDeviceId"),
     *
     *    @OA\RequestBody(
     *         required=true,
     *         description="JSON格式数据",
     *         @OA\JsonContent(
     *             required={"deviceId", "data"},
     *             @OA\Property(property="deviceId", type="string", description="非调试时，必传设备ID"),
     *             @OA\Property(property="data", type="object", description="业务数据",
     *                required={"id"},
     *                @OA\Property(property="id", type="integer", default="10", description=""),
     *                @OA\Property(property="page", type="integer", default="1", description="分页数"),
     *             )
     *         )
     *     ),
     *
     *    @OA\Response(
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
    public function navFilterAction()
    {
        $navId  = $this->getRequest('id', 'int');
        $result = ComicsRepository::navFilter($navId);
        $this->sendSuccessResult($result);
    }

    /**
     * 筛选页面
     * @return void
     *
     * @OA\Post(
     *     path="/comics/filter",
     *     summary="筛选页面",
     *     tags={"漫画管理"},
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
     *          required=true,
     *          description="JSON格式数据",
     *          @OA\JsonContent(
     *              required={"deviceId"},
     *              @OA\Property(property="deviceId", type="string", description="非调试时，必传设备ID"),
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
     *  )
     */
    public function filterAction()
    {
        $result = ComicsRepository::filter();
        $this->sendSuccessResult($result);
    }

    /**
     * 模块详情
     * @return void
     * @throws \App\Exception\BusinessException
     *
     * @OA\Post(
     *     path="/comics/blockDetail",
     *     summary="模块详情",
     *     tags={"漫画管理"},
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
     *          required=true,
     *          description="JSON格式数据",
     *          @OA\JsonContent(
     *              required={"deviceId", "data"},
     *              @OA\Property(property="deviceId", type="string", description="非调试时，必传设备ID"),
     *              @OA\Property(property="data", type="object", description="业务数据",
     *                 required={"id"},
     *                 @OA\Property(property="id", type="integer", default="10", description="模块id"),
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
     *  )
     */
    public function blockDetailAction()
    {
        $blockId = $this->getRequest('id', 'int');
        $result  = ComicsRepository::getBlockDetail($blockId);
        $this->sendSuccessResult($result);
    }

    /**
     * 漫画详情
     * @return void
     * @throws \App\Exception\BusinessException
     *
     * @OA\Post(
     *     path="/comics/detail",
     *     summary="漫画详情",
     *     tags={"漫画管理"},
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
     *          required=true,
     *          description="JSON格式数据",
     *          @OA\JsonContent(
     *              required={"deviceId", "token", "data"},
     *              @OA\Property(property="deviceId", type="string", description="非调试时，必传设备ID"),
     *              @OA\Property(property="token", type="string", description="登陆后需传"),
     *              @OA\Property(property="data", type="object", description="业务数据",
     *                 required={"id"},
     *                 @OA\Property(property="id", type="string", default="12321", description="漫画id"),
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
     *  )
     */
    public function detailAction()
    {
        $userId = $this->getUserId(false);
        $id     = $this->getRequest('id', 'string');
        $result = ComicsRepository::getDetail($userId, $id);
        $this->sendSuccessResult($result);
    }

    /**
     * 章节详情-阅读
     * @return void
     * @throws \App\Exception\BusinessException
     *
     * @OA\Post(
     *      path="/comics/chapter",
     *      summary="章节详情-阅读",
     *      tags={"漫画管理"},
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
     *               @OA\Property(property="data", type="object", description="业务数据",
     *                  required={"id"},
     *                  @OA\Property(property="id", type="string", default="12321", description="章节id"),
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
    public function chapterAction()
    {
        $userId = $this->getUserId(false);
        $id     = $this->getRequest('id', 'string');
        $result = ComicsRepository::getChapterDetail($userId, $id);
        $this->sendSuccessResult($result);
    }

    /**
     * 标签详情
     * @return void
     * @throws BusinessException
     *
     * @OA\Post(
     *       path="/comics/tagDetail",
     *       summary="标签详情",
     *       tags={"漫画管理"},
     *
     *       @OA\Parameter(ref="#/components/parameters/HeaderVersion"),
     *       @OA\Parameter(ref="#/components/parameters/HeaderDeviceType"),
     *       @OA\Parameter(ref="#/components/parameters/HeaderTime"),
     *       @OA\Parameter(ref="#/components/parameters/HeaderDebugKey"),
     *       @OA\Parameter(ref="#/components/parameters/HeaderRequestId"),
     *       @OA\Parameter(ref="#/components/parameters/HeaderToken"),
     *       @OA\Parameter(ref="#/components/parameters/HeaderDeviceId"),
     *
     *       @OA\RequestBody(
     *            required=true,
     *            description="JSON格式数据",
     *            @OA\JsonContent(
     *                required={"deviceId", "data"},
     *                @OA\Property(property="deviceId", type="string", description="非调试时，必传设备ID"),
     *                @OA\Property(property="data", type="object", description="业务数据",
     *                   required={"id"},
     *                   @OA\Property(property="id", type="string", default="12321", description="标签id"),
     *                )
     *            )
     *        ),
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
     *    )
     */
    public function tagDetailAction()
    {
        $tagId  = $this->getRequest('id', 'int');
        $result = ComicsRepository::getTagDetail($tagId);
        $this->sendSuccessResult($result);
    }

    /**
     * 去点赞
     * @return void
     * @throws \App\Exception\BusinessException
     *
     * @OA\Post(
     *    path="/comics/doLove",
     *    summary="去点赞",
     *    tags={"漫画管理"},
     *
     *    @OA\Parameter(ref="#/components/parameters/HeaderVersion"),
     *    @OA\Parameter(ref="#/components/parameters/HeaderDeviceType"),
     *    @OA\Parameter(ref="#/components/parameters/HeaderTime"),
     *    @OA\Parameter(ref="#/components/parameters/HeaderDebugKey"),
     *    @OA\Parameter(ref="#/components/parameters/HeaderRequestId"),
     *    @OA\Parameter(ref="#/components/parameters/HeaderToken"),
     *    @OA\Parameter(ref="#/components/parameters/HeaderDeviceId"),
     *
     *    @OA\RequestBody(
     *         required=true,
     *         description="JSON格式数据",
     *         @OA\JsonContent(
     *             required={"deviceId", "token", "data"},
     *             @OA\Property(property="deviceId", type="string", description="非调试时，必传设备ID"),
     *             @OA\Property(property="token", type="string", description="登陆后需传"),
     *             @OA\Property(property="data", type="object", description="业务数据",
     *                required={"id"},
     *                @OA\Property(property="id", type="string", default="12321", description="漫画id"),
     *             )
     *         )
     *     ),
     *
     *    @OA\Response(
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
    public function doLoveAction()
    {
        $userId   = $this->getUserId();
        $comicsId = $this->getRequest('id', 'string');
        if (empty($comicsId)) {
            $this->sendErrorResult('参数错误');
        }
        $result = ComicsRepository::doLove($userId, $comicsId);
        $this->sendSuccessResult(['status' => $result ? 'y' : 'n']);
    }

    /**
     * 点赞列表
     * @return void
     *
     * @OA\Post(
     *     path="/comics/love",
     *     summary="点赞列表",
     *     tags={"漫画管理"},
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
     *          required=true,
     *          description="JSON格式数据",
     *          @OA\JsonContent(
     *              required={"deviceId", "token", "data"},
     *              @OA\Property(property="deviceId", type="string", description="非调试时，必传设备ID"),
     *              @OA\Property(property="token", type="string", description="登陆后需传"),
     *              @OA\Property(property="data", type="object", description="业务数据",
     *                 required={"home_id"},
     *                 @OA\Property(property="home_id", type="string", default="12321", description="用户id"),
     *                 @OA\Property(property="page", type="integer", default="1", description="分页数"),
     *                 @OA\Property(property="cursor", type="string", default="2025-06", description="更新时间"),
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
     *  )
     */
    public function loveAction()
    {
        $userId = $this->getUserId();
        $page   = $this->getRequest('page', 'int', 1);
        $homeId = $this->getRequest('home_id', 'string');
        $cursor = $this->getRequest('cursor', 'string');
        if (empty($homeId) || $homeId < 1) {
            $this->sendErrorResult('请检查参数!');
        }
        $result = ComicsRepository::getLoveList($homeId, $page, 12, $cursor);
        $this->sendSuccessResult($result);
    }

    /**
     * 去收藏
     * @return void
     * @throws \App\Exception\BusinessException
     *
     * @OA\Post(
     *      path="/comics/doFavorite",
     *      summary="去收藏",
     *      tags={"漫画管理"},
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
     *               @OA\Property(property="data", type="object", description="业务数据",
     *                  required={"id"},
     *                  @OA\Property(property="id", type="string", default="12321", description="漫画id"),
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
    public function doFavoriteAction()
    {
        $userId   = $this->getUserId();
        $comicsId = $this->getRequest('id', 'string');
        if (empty($comicsId)) {
            $this->sendErrorResult('参数错误');
        }
        $result = ComicsRepository::doFavorite($userId, $comicsId);
        $this->sendSuccessResult(['status' => $result ? 'y' : 'n']);
    }

    /**
     * 收藏列表
     * @return void
     *
     * @OA\Post(
     *      path="/comics/favorite",
     *      summary="收藏列表",
     *      tags={"漫画管理"},
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
     *               @OA\Property(property="data", type="object", description="业务数据",
     *                  required={"home_id"},
     *                  @OA\Property(property="home_id", type="string", default="12321", description="用户id"),
     *                  @OA\Property(property="page", type="integer", default="1", description="分页数"),
     *                  @OA\Property(property="cursor", type="string", default="2025-06", description="更新时间"),
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
    public function favoriteAction()
    {
        $userId = $this->getUserId();
        $page   = $this->getRequest('page', 'int', 1);
        $homeId = $this->getRequest('home_id', 'string');
        $cursor = $this->getRequest('cursor', 'string');
        if (empty($homeId) || $homeId < 1) {
            $this->sendErrorResult('请检查参数!');
        }
        $result = ComicsRepository::getFavoriteList($homeId, $page, 12, $cursor);
        $this->sendSuccessResult($result);
    }

    /**
     * 添加播放记录
     *
     * @OA\Post(
     *    path="/comics/doHistory",
     *    summary="添加播放记录",
     *    tags={"漫画管理"},
     *
     *    @OA\Parameter(ref="#/components/parameters/HeaderVersion"),
     *    @OA\Parameter(ref="#/components/parameters/HeaderDeviceType"),
     *    @OA\Parameter(ref="#/components/parameters/HeaderTime"),
     *    @OA\Parameter(ref="#/components/parameters/HeaderDebugKey"),
     *    @OA\Parameter(ref="#/components/parameters/HeaderRequestId"),
     *    @OA\Parameter(ref="#/components/parameters/HeaderToken"),
     *    @OA\Parameter(ref="#/components/parameters/HeaderDeviceId"),
     *
     *    @OA\RequestBody(
     *         required=true,
     *         description="JSON格式数据",
     *         @OA\JsonContent(
     *             required={"deviceId", "token", "data"},
     *             @OA\Property(property="deviceId", type="string", description="非调试时，必传设备ID"),
     *             @OA\Property(property="token", type="string", description="登陆后需传"),
     *             @OA\Property(property="data", type="object", description="业务数据",
     *                required={"chapter_id"},
     *                @OA\Property(property="chapter_id", type="string", default="12321", description="章节id"),
     *                @OA\Property(property="id", type="string", default="12321", description="漫画id"),
     *                @OA\Property(property="index", type="integer", default="", description=""),
     *             )
     *         )
     *     ),
     * @OA\Response(
     *            response=200,
     *            description="请求响应成功",
     *     )
     * )
     */
    public function doHistoryAction()
    {
        $userId    = $this->getUserId();
        $comicsId  = $this->getRequest('id', 'string');
        $chapterId = $this->getRequest('chapter_id', 'string');
        $index     = $this->getRequest('index', 'int');
        ComicsRepository::doHistory($userId, $comicsId, $chapterId, $index);
        /* $this->sendSuccessResult();//没有响应的意义,浪费出口带宽 */
    }

    /**
     * 删除历史
     * @return void
     *
     * @OA\Post(
     *       path="/comics/delHistory",
     *       summary="删除历史",
     *       tags={"漫画管理"},
     *
     *       @OA\Parameter(ref="#/components/parameters/HeaderVersion"),
     *       @OA\Parameter(ref="#/components/parameters/HeaderDeviceType"),
     *       @OA\Parameter(ref="#/components/parameters/HeaderTime"),
     *       @OA\Parameter(ref="#/components/parameters/HeaderDebugKey"),
     *       @OA\Parameter(ref="#/components/parameters/HeaderRequestId"),
     *       @OA\Parameter(ref="#/components/parameters/HeaderToken"),
     *       @OA\Parameter(ref="#/components/parameters/HeaderDeviceId"),
     *
     *       @OA\RequestBody(
     *            required=true,
     *            description="JSON格式数据",
     *            @OA\JsonContent(
     *                required={"deviceId", "token", "data"},
     *                @OA\Property(property="deviceId", type="string", description="非调试时，必传设备ID"),
     *                @OA\Property(property="token", type="string", description="登陆后需传"),
     *                @OA\Property(property="data", type="object", description="业务数据",
     *                   required={"ids"},
     *                   @OA\Property(property="ids", type="string", default="漫画id", description="逗号分割或是all"),
     *                )
     *            )
     *        ),
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
     *    )
     */
    public function delHistoryAction()
    {
        $userId   = $this->getUserId();
        $audioIds = $this->getRequest('ids'); // 逗号分割或是all
        ComicsRepository::delHistory($userId, $audioIds);
        $this->sendSuccessResult();
    }

    /**
     * 历史列表
     * @return void
     *
     * @OA\Post(
     *       path="/comics/history",
     *       summary="历史列表",
     *       tags={"漫画管理"},
     *
     *       @OA\Parameter(ref="#/components/parameters/HeaderVersion"),
     *       @OA\Parameter(ref="#/components/parameters/HeaderDeviceType"),
     *       @OA\Parameter(ref="#/components/parameters/HeaderTime"),
     *       @OA\Parameter(ref="#/components/parameters/HeaderDebugKey"),
     *       @OA\Parameter(ref="#/components/parameters/HeaderRequestId"),
     *       @OA\Parameter(ref="#/components/parameters/HeaderToken"),
     *       @OA\Parameter(ref="#/components/parameters/HeaderDeviceId"),
     *
     *       @OA\RequestBody(
     *            required=true,
     *            description="JSON格式数据",
     *            @OA\JsonContent(
     *                required={"deviceId", "token", "data"},
     *                @OA\Property(property="deviceId", type="string", description="非调试时，必传设备ID"),
     *                @OA\Property(property="token", type="string", description="登陆后需传"),
     *                @OA\Property(property="data", type="object", description="业务数据",
     *                   @OA\Property(property="page", type="integer", default="1", description="分页数"),
     *                   @OA\Property(property="cursor", type="string", default="1", description="更新时间"),
     *                )
     *            )
     *        ),
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
     *    )
     */
    public function historyAction()
    {
        $userId = $this->getUserId();
        $page   = $this->getRequest('page', 'int', 1);
        $cursor = $this->getRequest('cursor', 'string', '');
        $result = ComicsRepository::getHistoryList($userId, $page, 12, $cursor);
        $this->sendSuccessResult($result);
    }

    /**
     * 购买漫画
     * @return void
     * @throws BusinessException
     *
     * @OA\Post(
     *       path="/comics/doBuy",
     *       summary="购买漫画",
     *       tags={"漫画管理"},
     *
     *       @OA\Parameter(ref="#/components/parameters/HeaderVersion"),
     *       @OA\Parameter(ref="#/components/parameters/HeaderDeviceType"),
     *       @OA\Parameter(ref="#/components/parameters/HeaderTime"),
     *       @OA\Parameter(ref="#/components/parameters/HeaderDebugKey"),
     *       @OA\Parameter(ref="#/components/parameters/HeaderRequestId"),
     *       @OA\Parameter(ref="#/components/parameters/HeaderToken"),
     *       @OA\Parameter(ref="#/components/parameters/HeaderDeviceId"),
     *
     *       @OA\RequestBody(
     *            required=true,
     *            description="JSON格式数据",
     *            @OA\JsonContent(
     *                required={"deviceId", "token", "data"},
     *                @OA\Property(property="deviceId", type="string", description="非调试时，必传设备ID"),
     *                @OA\Property(property="token", type="string", description="登陆后需传"),
     *                @OA\Property(property="data", type="object", description="业务数据",
     *                   required={"home_id"},
     *                   @OA\Property(property="id", type="string", default="12321", description="漫画id"),
     *                )
     *            )
     *        ),
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
     *    )
     */
    public function doBuyAction()
    {
        $userId   = $this->getUserId();
        $comicsId = $this->getRequest('id', 'string');
        //        $chapterId = $this->getRequest('chapter_id', 'string');//一般不用单章解锁
        $chapterId = '';
        ComicsRepository::doBuy($userId, $comicsId, $chapterId);
        $this->sendSuccessResult();
    }

    /**
     * 购买记录
     * @return void
     *
     * @OA\Post(
     *       path="/comics/buyLog",
     *       summary="购买记录",
     *       tags={"漫画管理"},
     *
     *       @OA\Parameter(ref="#/components/parameters/HeaderVersion"),
     *       @OA\Parameter(ref="#/components/parameters/HeaderDeviceType"),
     *       @OA\Parameter(ref="#/components/parameters/HeaderTime"),
     *       @OA\Parameter(ref="#/components/parameters/HeaderDebugKey"),
     *       @OA\Parameter(ref="#/components/parameters/HeaderRequestId"),
     *       @OA\Parameter(ref="#/components/parameters/HeaderToken"),
     *       @OA\Parameter(ref="#/components/parameters/HeaderDeviceId"),
     *
     *       @OA\RequestBody(
     *            required=true,
     *            description="JSON格式数据",
     *            @OA\JsonContent(
     *                required={"deviceId", "token", "data"},
     *                @OA\Property(property="deviceId", type="string", description="非调试时，必传设备ID"),
     *                @OA\Property(property="token", type="string", description="登陆后需传"),
     *                @OA\Property(property="data", type="object", description="业务数据",
     *                   @OA\Property(property="page", type="integer", default="1", description="分页数"),
     *                   @OA\Property(property="cursor", type="string", default="2025-06", description="更新时间"),
     *                )
     *            )
     *        ),
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
     *    )
     */
    public function buyLogAction()
    {
        $userId = $this->getUserId();
        $page   = $this->getRequest('page', 'int', 1);
        $cursor = $this->getRequest('cursor', 'string');
        $result = ComicsRepository::getBuyLogList($userId, $page, 20, $cursor);
        $this->sendSuccessResult($result);
    }

    /**
     * 站点地图
     * @return void
     *
     * @OA\Post(
     *       path="/comics/sitemap",
     *       summary="站点地图",
     *       tags={"漫画管理"},
     *
     *       @OA\Parameter(ref="#/components/parameters/HeaderVersion"),
     *       @OA\Parameter(ref="#/components/parameters/HeaderDeviceType"),
     *       @OA\Parameter(ref="#/components/parameters/HeaderTime"),
     *       @OA\Parameter(ref="#/components/parameters/HeaderDebugKey"),
     *       @OA\Parameter(ref="#/components/parameters/HeaderRequestId"),
     *       @OA\Parameter(ref="#/components/parameters/HeaderToken"),
     *       @OA\Parameter(ref="#/components/parameters/HeaderDeviceId"),
     *
     *       @OA\RequestBody(
     *            required=true,
     *            description="JSON格式数据",
     *            @OA\JsonContent(
     *                required={"deviceId", "data"},
     *                @OA\Property(property="deviceId", type="string", description="非调试时，必传设备ID"),
     *                @OA\Property(property="data", type="object", description="业务数据",
     *                   @OA\Property(property="page", type="integer", default="1", description="分页数"),
     *                )
     *            )
     *        ),
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
     *    )
     */
    public function sitemapAction()
    {
        $page   = $this->getRequest('page', 'int', 1);
        $result = ComicsRepository::sitemap($page);
        $this->sendSuccessResult($result);
    }
}
