<?php

namespace App\Controller\Swagger\Schemas;

/**
 * 通用接口返回结构
 *
 * @OA\Schema(
 *     schema="ApiResponseEmpty",
 *     type="object",
 *     required={"status"},
 *     @OA\Property(
 *         property="status",
 *         type="string",
 *         enum={"y","n"},
 *         example="y",
 *         description="请求是否成功"
 *     ),
 *     @OA\Property(
 *         property="data",
 *         nullable=true,
 *         description="业务数据[预设值]"
 *     ),
 *     @OA\Property(
 *         property="error",
 *         type="string",
 *         nullable=true,
 *         description="错误信息"
 *     ),
 *     @OA\Property(
 *         property="errorCode",
 *         type="integer",
 *         nullable=true,
 *         description="错误码"
 *     ),
 *     @OA\Property(
 *         property="time",
 *         type="string",
 *         example="2026-01-19 14:42:13",
 *         description="服务器时间"
 *     )
 * )
 */
class ApiResponseEmpty
{
}
