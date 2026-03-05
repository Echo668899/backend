<?php

namespace App\Controller\Swagger\ApiSchemas;

/**
 * @OA\Schema(
 *     schema="NavItem",
 *     type="object",
 *     required={"id","name"},
 *
 *     @OA\Property(property="id", type="integer", example=1),
 *     @OA\Property(property="name", type="string", example="首页"),
 *     @OA\Property(property="icon", type="string"),
 *     @OA\Property(property="link", type="string"),
 *     @OA\Property(property="sort", type="integer")
 * )
 */
class NavItemSchema
{
}
