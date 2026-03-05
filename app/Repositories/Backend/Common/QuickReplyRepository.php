<?php

declare(strict_types=1);

namespace App\Repositories\Backend\Common;

use App\Constants\StatusCode;
use App\Core\Repositories\BaseRepository;
use App\Exception\BusinessException;
use App\Models\Common\QuickReplyModel;

/**
 * 快捷回复
 * @package App\Repositories\Backend
 */
class QuickReplyRepository extends BaseRepository
{
    /**
     * 获取列表
     * @param        $request
     * @return array
     */
    public static function getList($request)
    {
        $page     = self::getRequest($request, 'page', 'int', 1);
        $pageSize = self::getRequest($request, 'pageSize', 'int', 15);
        $sort     = self::getRequest($request, 'sort', 'string', '_id');
        $order    = self::getRequest($request, 'order', 'int', -1);
        $query    = [];
        $filter   = [];

        if ($request['name']) {
            $filter['name'] = self::getRequest($request, 'name');
            $query['name']  = ['$regex' => $filter['name'], '$options' => 'i'];
        }

        $skip   = ($page - 1) * $pageSize;
        $fields = [];
        $count  = QuickReplyModel::count($query);
        $items  = QuickReplyModel::find($query, $fields, [$sort => $order], $skip, $pageSize);
        foreach ($items as $index => $item) {
            $item['created_at'] = date('Y-m-d H:i', $item['created_at']);
            $item['updated_at'] = date('Y-m-d H:i', $item['updated_at']);
            $items[$index]      = $item;
        }

        return [
            'filter'   => $filter,
            'items'    => empty($items) ? [] : array_values($items),
            'count'    => $count,
            'page'     => $page,
            'pageSize' => $pageSize
        ];
    }

    /**
     * 保存数据
     * @param                    $data
     * @return bool|int|mixed
     * @throws BusinessException
     */
    public static function save($data)
    {
        $row = [
            'name'    => self::getRequest($data, 'name', 'string'),
            'content' => self::getRequest($data, 'content', 'string'),
            'sort'    => self::getRequest($data, 'sort', 'int', 0),
        ];

        if (empty($row['name']) || empty($row['content'])) {
            throw  new BusinessException(StatusCode::PARAMETER_ERROR, '参数错误!');
        }

        if ($data['_id'] > 0) {
            $row['_id'] = self::getRequest($data, '_id', 'int');
        }
        return QuickReplyModel::save($row);
    }

    /**
     * 获取详情
     * @param                    $id
     * @return mixed
     * @throws BusinessException
     */
    public static function getDetail($id)
    {
        $row = QuickReplyModel::findByID(intval($id));
        if (empty($row)) {
            throw  new BusinessException(StatusCode::DATA_ERROR, '数据不存在!');
        }
        return $row;
    }

    /**
     * 删除
     * @param        $id
     * @return mixed
     */
    public static function delete($id)
    {
        return QuickReplyModel::deleteById(intval($id));
    }
}
