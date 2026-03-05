<?php

namespace App\Controller\Swagger\ApiSchemas;

/**
 * @OA\Schema(
 *     schema="SystemInfoData",
 *     type="object",
 *
 *     @OA\Property(property="version", type="string", example="1.0.0"),
 *     @OA\Property(property="min_version", type="string", example="1.0.0"),
 *     @OA\Property(property="version_description", type="string", example="升级公告"),
 *     @OA\Property(property="download_url", type="string"),
 *     @OA\Property(property="site_url", type="string"),
 *     @OA\Property(property="img_key", type="string"),
 *     @OA\Property(property="cdn_header", type="string"),
 *
 *     @OA\Property(property="upload_image_url", type="string"),
 *     @OA\Property(property="upload_file_url", type="string"),
 *     @OA\Property(property="upload_file_query_url", type="string"),
 *     @OA\Property(property="upload_file_max_length", type="string"),
 *     @OA\Property(property="upload_image_max_length", type="string"),
 *
 *     @OA\Property(
 *         property="domain_statistic",
 *         type="array",
 *         @OA\Items(
 *             type="object",
 *             @OA\Property(property="domain", type="string"),
 *             @OA\Property(property="tracking_code", type="string")
 *         )
 *     ),
 *
 *     @OA\Property(
 *         property="layers",
 *         type="array",
 *         @OA\Items(
 *             type="object",
 *             @OA\Property(property="type", type="string"),
 *             @OA\Property(property="data", type="string")
 *         )
 *     ),
 *
 *     @OA\Property(
 *         property="ads",
 *         type="object",
 *         @OA\Property(property="app_float_left", nullable=true),
 *         @OA\Property(property="app_float_right", nullable=true),
 *         @OA\Property(property="app_float_bottom", type="array", @OA\Items(type="object"))
 *     ),
 *
 *     @OA\Property(property="movie_nav", type="array", @OA\Items(ref="#/components/schemas/NavItem")),
 *     @OA\Property(property="comics_nav", type="array", @OA\Items(ref="#/components/schemas/NavItem")),
 *     @OA\Property(property="novel_nav", type="array", @OA\Items(ref="#/components/schemas/NavItem")),
 *     @OA\Property(property="audio_nav", type="array", @OA\Items(ref="#/components/schemas/NavItem"))
 * )
 */
class SystemInfoSchema
{
}
