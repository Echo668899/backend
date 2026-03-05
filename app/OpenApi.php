<?php

declare(strict_types=1);

namespace App;

/**
 * @OA\OpenApi(
 *     @OA\Info(
 *         title="YC152-APP-UU视频",
 *         version="1.0.0",
 *         description="YC152-APP-UU视频 API接口文档",
 *     ),
 *
 *     servers={
 *          @OA\Server(
 *              url="https://xxxx.me/api",
 *              description="默认API前缀",
 *              @OA\ServerVariable(
 *                   serverVariable="version",
 *                   default="1.0.0",
 *               ),
 *               @OA\ServerVariable(
 *                   serverVariable="deviceType",
 *                   default="android",
 *               ),
 *               @OA\ServerVariable(
 *                    serverVariable="debugKey",
 *                    default="xxxxxxxxxxxx",
 *               ),
 *               @OA\ServerVariable(
 *                     serverVariable="token",
 *                     default="xxxxxxxxxxxx",
 *               ),
 *               @OA\ServerVariable(
 *                      serverVariable="deviceId",
 *                      default="xxxxxxxxxxxx",
 *               ),
 *               @OA\ServerVariable(
 *                       serverVariable="userAgent",
 *                       default="Dart",
 *               ),
 *          )
 *     },
 *
 *     @OA\Components(
 *        @OA\Parameter(
 *            parameter="HeaderVersion",
 *            name="version",
 *            in="header",
 *            required=true,
 *            description="API版本号，格式：x.x.x",
 *            @OA\Schema(type="string", default="{{version}}"),
 *        ),
 *        @OA\Parameter(
 *            parameter="HeaderDeviceType",
 *            name="deviceType",
 *            in="header",
 *            required=true,
 *            description="设备类型: ios,android,web",
 *            @OA\Schema(type="string", default="{{deviceType}}"),
 *        ),
 *        @OA\Parameter(
 *             parameter="HeaderTime",
 *             name="time",
 *             in="header",
 *             required=true,
 *             description="时间戳",
 *             @OA\Schema(type="string", default="{{$timestamp}}"),
 *        ),
 *        @OA\Parameter(
 *              parameter="HeaderDebugKey",
 *              name="debugKey",
 *              in="header",
 *              required=false,
 *              description="调试key,传入可以返回非加密json数据",
 *              @OA\Schema(type="string", default="{{debugKey}}"),
 *         ),
 *        @OA\Parameter(
 *                parameter="HeaderRequestId",
 *                name="requestId",
 *                in="header",
 *                required=true,
 *                description="请求ID，动态生成",
 *                @OA\Schema(type="string", default="{{$guid}}"),
 *         ),
 *         @OA\Parameter(
 *               parameter="HeaderToken",
 *               name="token",
 *               in="header",
 *               required=false,
 *               description="调试key添加时，可以传token",
 *               @OA\Schema(type="string", default="{{token}}"),
 *          ),
 *          @OA\Parameter(
 *                parameter="HeaderDeviceId",
 *                name="deviceId",
 *                in="header",
 *                required=false,
 *                description="调试key添加时，可以传deviceId",
 *                @OA\Schema(type="string", default="{{deviceId}}"),
 *           ),
 *           @OA\Parameter(
 *                 parameter="HeaderUserAgent",
 *                 name="User-Agent",
 *                 in="header",
 *                 required=false,
 *                 description="deviceType为ios,android,必传包含Dart",
 *                 @OA\Schema(type="string", default="{{userAgent}}"),
 *           ),
 *     ),
 * )
 */
final class OpenApi
{
}
