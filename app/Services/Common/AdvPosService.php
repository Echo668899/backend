<?php

namespace App\Services\Common;

use App\Controller\BaseBackendController;
use App\Models\Common\AdvPosModel;

/**
 * 广告位
 */
class AdvPosService extends BaseBackendController
{
    /**
     * 所有广告位
     * @return array
     */
    public static function getAll()
    {
        $items  = AdvPosModel::find([], [], ['sort' => 1], 0, 1000);
        $result = [];
        foreach ($items as $item) {
            $result[$item['code']] = [
                'code'   => $item['code'],
                'name'   => $item['name'],
                'width'  => $item['width'],
                'height' => $item['height'],
            ];
        }
        return $result;
    }
}
