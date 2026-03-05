<?php

namespace App\Controller\Swagger;

use App\Controller\BaseApiController;
use App\Exception\BusinessException;
use Phalcon\Storage\Exception;

/**
 * @OA\Tag(name="AI管理", description="AI相关接口")
 */
class AiSwagger extends BaseApiController
{
    /**
     * @return void
     *
     * @OA\Post(
     *     path="/ai/config",
     *     summary="获取AI配置",
     *     tags={"AI管理"},
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
     *
     *  )
     */
    public function configAction()
    {
    }

    /**
     * 获取顶部菜单,根据type
     * @return void
     * @throws Exception
     *
     * @OA\Post(
     *    path="/ai/navFilter",
     *    summary="获取顶部菜单,根据type",
     *    tags={"AI管理"},
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
     *                required={"type"},
     *                @OA\Property(property="type", type="string", default="text_to_image", description="类型：text_to_image,..."),
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
    }

    /**
     * 获取模板
     * @return void
     *
     * @OA\Post(
     *     path="/ai/tpl",
     *     summary="获取模板",
     *     tags={"AI管理"},
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
     *                 required={"type"},
     *                 @OA\Property(property="type", type="string", default="text_to_image", description="类型：text_to_image,..."),
     *                 @OA\Property(property="page", type="integer", default="1", description="分页数"),
     *                 @OA\Property(property="page_size", type="integer", default="20", description="每页数据"),
     *                 @OA\Property(property="order", type="string", default="", description="排序: hot,new"),
     *                 @OA\Property(property="tag_id", type="string", default="", description="标签ID"),
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
    public function tplAction()
    {
    }

    /**
     * 提示词
     * @return void
     *
     * @OA\Post(
     *    path="/ai/tips",
     *    summary="提示词",
     *    tags={"AI管理"},
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
     *                required={"type"},
     *                @OA\Property(property="type", type="string", default="text_to_image", description="类型：text_to_image,..."),
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
    public function tipsAction()
    {
    }

    /**
     * 图生视频
     * @return void
     * @throws BusinessException
     *
     * @OA\Post(
     *      path="/ai/doImageToVideo",
     *      summary="图生视频",
     *      tags={"AI管理"},
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
     *                  required={"source_path", "tpl_id"},
     *                  @OA\Property(property="source_path", type="string", description="源路径"),
     *                  @OA\Property(property="tpl_id", type="string", description="模版id"),
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
    public function doImageToVideoAction()
    {
    }

    /**
     * 文转语音
     * @return void
     * @throws BusinessException
     *
     * @OA\Post(
     *       path="/ai/doTextToVoice",
     *       summary="文转语音",
     *       tags={"AI管理"},
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
     *                   required={"content", "tpl_id"},
     *                   @OA\Property(property="content", type="string", description="需要转化的文本，文字限制200字以内"),
     *                   @OA\Property(property="tpl_id", type="string", description="模版id"),
     *                   @OA\Property(property="source_path", type="string", description="语音源可以是模板 也可以是用户自己的 可以是m3u8"),
     *                )
     *            )
     *        ),
     *
     *        @OA\Response(
     *            response=200,
     *            description="请求响应成功",
     *            @OA\JsonContent(
     *                @OA\Property(property="status", type="string", default="y", enum={"y", "n"}),
     *                @OA\Property(property="data", type="object", nullable=true),
     *                @OA\Property(property="error", type="string", nullable=true),
     *                @OA\Property(property="errorCode", type="integer", nullable=true),
     *                @OA\Property(property="time", type="string", nullable=true)
     *            )
     *        )
     *    )
     */
    public function doTextToVoiceAction()
    {
    }

    /**
     * 文字生成图片
     * @return void
     * @throws BusinessException
     *
     * @OA\Post(
     *        path="/ai/doTextToImage",
     *        summary="文字生成图片",
     *        tags={"AI管理"},
     *
     *        @OA\Parameter(ref="#/components/parameters/HeaderVersion"),
     *        @OA\Parameter(ref="#/components/parameters/HeaderDeviceType"),
     *        @OA\Parameter(ref="#/components/parameters/HeaderTime"),
     *        @OA\Parameter(ref="#/components/parameters/HeaderDebugKey"),
     *        @OA\Parameter(ref="#/components/parameters/HeaderRequestId"),
     *        @OA\Parameter(ref="#/components/parameters/HeaderToken"),
     *        @OA\Parameter(ref="#/components/parameters/HeaderDeviceId"),
     *
     *        @OA\RequestBody(
     *             required=true,
     *             description="JSON格式数据",
     *             @OA\JsonContent(
     *                 required={"deviceId", "token", "data"},
     *                 @OA\Property(property="deviceId", type="string", description="非调试时，必传设备ID"),
     *                 @OA\Property(property="token", type="string", description="登陆后需传"),
     *                 @OA\Property(property="data", type="object", description="业务数据",
     *                    required={"prompt", "tpl_id"},
     *                    @OA\Property(property="prompt", type="string", default="美丽的大眼睛", description="提示词，文字限制200字以内"),
     *                    @OA\Property(property="tpl_id", type="string", default="12", description="模版id"),
     *                    @OA\Property(property="size", type="string", default="60x30", description="尺寸"),
     *                    @OA\Property(property="source_path", type="string", default="/mxx/xxxxxx.png", description="参考图片"),
     *                    @OA\Property(property="batch_count", type="string", default="2", description="生成组数"),
     *                    @OA\Property(property="batch_size", type="string", default="3", description="每组生成张数"),
     *                 )
     *             )
     *         ),
     *
     *         @OA\Response(
     *             response=200,
     *             description="请求响应成功",
     *             @OA\JsonContent(
     *                 @OA\Property(property="status", type="string", default="y", enum={"y", "n"}),
     *                 @OA\Property(property="data", type="object", nullable=true),
     *                 @OA\Property(property="error", type="string", nullable=true),
     *                 @OA\Property(property="errorCode", type="integer", nullable=true),
     *                 @OA\Property(property="time", type="string", nullable=true)
     *             )
     *         )
     *     )
     */
    public function doTextToImageAction()
    {
    }

    /**
     * 换脸
     * @return void
     * @throws BusinessException
     *
     * @OA\Post(
     *         path="/ai/doChangeFace",
     *         summary="换脸",
     *         tags={"AI管理"},
     *
     *         @OA\Parameter(ref="#/components/parameters/HeaderVersion"),
     *         @OA\Parameter(ref="#/components/parameters/HeaderDeviceType"),
     *         @OA\Parameter(ref="#/components/parameters/HeaderTime"),
     *         @OA\Parameter(ref="#/components/parameters/HeaderDebugKey"),
     *         @OA\Parameter(ref="#/components/parameters/HeaderRequestId"),
     *         @OA\Parameter(ref="#/components/parameters/HeaderToken"),
     *         @OA\Parameter(ref="#/components/parameters/HeaderDeviceId"),
     *
     *         @OA\RequestBody(
     *              required=true,
     *              description="JSON格式数据",
     *              @OA\JsonContent(
     *                  required={"deviceId", "token", "data"},
     *                  @OA\Property(property="deviceId", type="string", description="非调试时，必传设备ID"),
     *                  @OA\Property(property="token", type="string", description="登陆后需传"),
     *                  @OA\Property(property="data", type="object", description="业务数据",
     *                     required={"source_path", "type"},
     *                     @OA\Property(property="type", type="string", default="image", description="类型", enum={"image", "video"}),
     *                     @OA\Property(property="source_path", type="string", default="/mxx/xxxxxx.png", description="脸部源,图片地址"),
     *                     @OA\Property(property="tpl_id", type="string", default="12", description="模版id"),
     *                     @OA\Property(property="target_path", type="string", default="/mxx/xxxxxx.png", description="需要处理的视频或者图片  图片可以支持多个用,分开  视频支持mp4 m3u8"),
     *                  )
     *              )
     *          ),
     *
     *          @OA\Response(
     *              response=200,
     *              description="请求响应成功",
     *              @OA\JsonContent(
     *                  @OA\Property(property="status", type="string", default="y", enum={"y", "n"}),
     *                  @OA\Property(property="data", type="object", nullable=true),
     *                  @OA\Property(property="error", type="string", nullable=true),
     *                  @OA\Property(property="errorCode", type="integer", nullable=true),
     *                  @OA\Property(property="time", type="string", nullable=true)
     *              )
     *          )
     *      )
     */
    public function doChangeFaceAction()
    {
    }

    /**
     * 小说
     * @return void
     * @throws BusinessException
     *
     * @OA\Post(
     *    path="/ai/doNovel",
     *    summary="小说",
     *    tags={"AI管理"},
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
     *                required={"description", "story", "tpl_id"},
     *                @OA\Property(property="description", type="string", default="", description="细节说明"),
     *                @OA\Property(property="story", type="string", default="", description="故事情节"),
     *                @OA\Property(property="tpl_id", type="string", default="12", description="模版id"),
     *                @OA\Property(property="background", type="string", default="", description="人物设定"),
     *                @OA\Property(property="scene", type="string", default="", description="场景地点"),
     *             )
     *         )
     *     ),
     *
     *     @OA\Response(
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
    public function doNovelAction()
    {
    }

    /**
     * 换装
     * @return void
     * @throws BusinessException
     *
     * @OA\Post(
     *     path="/ai/doChangeDress",
     *     summary="换装",
     *     tags={"AI管理"},
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
     *                 required={"source_path", "tpl_id"},
     *                 @OA\Property(property="source_path", type="string", default="", description="图片地址"),
     *                 @OA\Property(property="tpl_id", type="string", default="12", description="模版id"),
     *              )
     *          )
     *      ),
     *
     *      @OA\Response(
     *          response=200,
     *          description="请求响应成功",
     *          @OA\JsonContent(
     *              @OA\Property(property="status", type="string", default="y", enum={"y", "n"}),
     *              @OA\Property(property="data", type="object", nullable=true),
     *              @OA\Property(property="error", type="string", nullable=true),
     *              @OA\Property(property="errorCode", type="integer", nullable=true),
     *              @OA\Property(property="time", type="string", nullable=true)
     *          )
     *      )
     *  )
     */
    public function doChangeDressAction()
    {
    }

    /**
     * 去衣
     * @return void
     * @throws BusinessException
     *
     * @OA\Post(
     *      path="/ai/doChangeDressBare",
     *      summary="去衣",
     *      tags={"AI管理"},
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
     *                  required={"source_path", "tpl_id"},
     *                  @OA\Property(property="source_path", type="string", default="", description="图片地址"),
     *                  @OA\Property(property="tpl_id", type="string", default="12", description="模版id"),
     *               )
     *           )
     *       ),
     *
     *       @OA\Response(
     *           response=200,
     *           description="请求响应成功",
     *           @OA\JsonContent(
     *               @OA\Property(property="status", type="string", default="y", enum={"y", "n"}),
     *               @OA\Property(property="data", type="object", nullable=true),
     *               @OA\Property(property="error", type="string", nullable=true),
     *               @OA\Property(property="errorCode", type="integer", nullable=true),
     *               @OA\Property(property="time", type="string", nullable=true)
     *           )
     *       )
     *   )
     */
    public function doChangeDressBareAction()
    {
    }

    /**
     * 任务列表
     * @return void
     *
     * @OA\Post(
     *    path="/ai/task",
     *    summary="任务列表",
     *    tags={"AI管理"},
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
     *                @OA\Property(property="home_id", type="integer", default="56433", description="不传则为用户自己ID"),
     *                @OA\Property(property="page", type="integer", default="1", description="分页数"),
     *                @OA\Property(property="type", type="string", default="1", description=""),
     *             )
     *         )
     *     ),
     *
     *     @OA\Response(
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
    public function taskAction()
    {
    }

    /**
     * 查看任务进度
     * @return void
     * @throws BusinessException
     *
     * @OA\Post(
     *     path="/ai/taskInfo",
     *     summary="查看任务进度",
     *     tags={"AI管理"},
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
     *                 @OA\Property(property="id", type="integer", default="xxxx", description="AI订单id值"),
     *              )
     *          )
     *      ),
     *
     *      @OA\Response(
     *          response=200,
     *          description="请求响应成功",
     *          @OA\JsonContent(
     *              @OA\Property(property="status", type="string", default="y", enum={"y", "n"}),
     *              @OA\Property(property="data", type="object", nullable=true),
     *              @OA\Property(property="error", type="string", nullable=true),
     *              @OA\Property(property="errorCode", type="integer", nullable=true),
     *              @OA\Property(property="time", type="string", nullable=true)
     *          )
     *      )
     *  )
     */
    public function taskInfoAction()
    {
    }

    /**
     * 作品详情
     * @return void
     * @throws BusinessException|\Exception
     *
     * @OA\Post(
     *    path="/ai/detail",
     *    summary="作品详情",
     *    tags={"AI管理"},
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
     *                @OA\Property(property="id", type="integer", default="xxxx", description="AI订单id值"),
     *             )
     *         )
     *     ),
     *
     *     @OA\Response(
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
    public function detailAction()
    {
    }

    /**
     * 去点赞
     * @throws BusinessException
     *
     * @OA\Post(
     *     path="/ai/doLove",
     *     summary="去点赞",
     *     tags={"AI管理"},
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
     *                 @OA\Property(property="id", type="integer", default="xxxx", description="AI订单id值"),
     *              )
     *          )
     *      ),
     *
     *      @OA\Response(
     *          response=200,
     *          description="请求响应成功",
     *          @OA\JsonContent(
     *              @OA\Property(property="status", type="string", default="y", enum={"y", "n"}),
     *              @OA\Property(property="data", type="object", nullable=true),
     *              @OA\Property(property="error", type="string", nullable=true),
     *              @OA\Property(property="errorCode", type="integer", nullable=true),
     *              @OA\Property(property="time", type="string", nullable=true)
     *          )
     *      )
     *  )
     */
    public function doLoveAction()
    {
    }

    /**
     * 点赞列表
     * @return void
     *
     * @OA\Post(
     *    path="/ai/love",
     *    summary="点赞列表",
     *    tags={"AI管理"},
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
     *                @OA\Property(property="id", type="integer", default="54566", description="用户ID，传入看他人视频"),
     *                @OA\Property(property="page", type="integer", default="1", description="分页数"),
     *             )
     *         )
     *     ),
     *
     *     @OA\Response(
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
    public function loveAction()
    {
    }

    /**
     * 去收藏
     * @throws BusinessException
     *
     * @OA\Post(
     *     path="/ai/doFavorite",
     *     summary="去收藏AI作品",
     *     tags={"AI管理"},
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
     *                 @OA\Property(property="id", type="integer", default="54566", description="AI订单ID"),
     *              )
     *          )
     *      ),
     *
     *      @OA\Response(
     *          response=200,
     *          description="请求响应成功",
     *          @OA\JsonContent(
     *              @OA\Property(property="status", type="string", default="y", enum={"y", "n"}),
     *              @OA\Property(property="data", type="object", nullable=true),
     *              @OA\Property(property="error", type="string", nullable=true),
     *              @OA\Property(property="errorCode", type="integer", nullable=true),
     *              @OA\Property(property="time", type="string", nullable=true)
     *          )
     *      )
     *  )
     */
    public function doFavoriteAction()
    {
    }

    /**
     * 收藏列表
     * @return void
     *
     * @OA\Post(
     *    path="/ai/favorite",
     *    summary="收藏列表",
     *    tags={"AI管理"},
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
     *                @OA\Property(property="id", type="integer", default="54566", description="用户ID，传入看他人视频，默认自己"),
     *                @OA\Property(property="folder_id", type="string", default="10", description="页数据大小"),
     *                @OA\Property(property="page", type="integer", default="1", description="分页数"),
     *             )
     *         )
     *     ),
     *
     *     @OA\Response(
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
    public function favoriteAction()
    {
    }

    /**
     * @return void
     *
     * @OA\Post(
     *     path="/ai/doDel",
     *     summary="删除AI作品",
     *     tags={"AI管理"},
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
     *                 @OA\Property(property="ids", type="integer", default="xxxxxx", description="AI订单ID，逗号分割或是all"),
     *              )
     *          )
     *      ),
     *
     *      @OA\Response(
     *          response=200,
     *          description="请求响应成功",
     *          @OA\JsonContent(
     *              @OA\Property(property="status", type="string", default="y", enum={"y", "n"}),
     *              @OA\Property(property="data", type="object", nullable=true),
     *              @OA\Property(property="error", type="string", nullable=true),
     *              @OA\Property(property="errorCode", type="integer", nullable=true),
     *              @OA\Property(property="time", type="string", nullable=true)
     *          )
     *      )
     *  )
     */
    public function doDelAction()
    {
    }
}
