<?php

namespace App\Services\Common;

use App\Core\Services\BaseService;
use App\Models\Common\QuickReplyModel;

class QuickReplyService extends BaseService
{
    /**
     * 所有
     * @return array
     */
    public static function getAll()
    {
        return QuickReplyModel::find([], [], ['sort' => -1], 0, 1000);
    }
}
