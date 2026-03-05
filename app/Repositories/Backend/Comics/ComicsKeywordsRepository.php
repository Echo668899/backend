<?php

declare(strict_types=1);

namespace App\Repositories\Backend\Comics;

use App\Constants\StatusCode;
use App\Core\Repositories\BaseRepository;
use App\Exception\BusinessException;
use App\Models\Comics\ComicsKeywordsModel;

/**
 * 视频关键字管理
 * @package App\Repositories\Admin
 */
class ComicsKeywordsRepository extends BaseRepository
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

        if ($request['is_hot'] !== null && $request['is_hot'] !== '') {
            $filter['is_hot'] = self::getRequest($request, 'is_hot', 'int');
            $query['is_hot']  = $filter['is_hot'];
        }
        if ($request['position'] !== null && $request['position'] !== '') {
            $filter['position'] = self::getRequest($request, 'position', 'string');
            $query['position']  = $filter['position'];
        }

        $skip   = ($page - 1) * $pageSize;
        $fields = [];
        $count  = ComicsKeywordsModel::count($query);
        $items  = ComicsKeywordsModel::find($query, $fields, [$sort => $order], $skip, $pageSize);
        foreach ($items as $index => $item) {
            $item['created_at'] = date('Y-m-d H:i', $item['created_at']);
            $item['updated_at'] = date('Y-m-d H:i', $item['updated_at']);
            $item['is_hot']     = $item['is_hot'] ? '是' : '否';
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
     * 获取详情
     * @param                    $id
     * @return mixed
     * @throws BusinessException
     */
    public static function getDetail($id)
    {
        $result = ComicsKeywordsModel::findByID($id);
        if (empty($result)) {
            throw  new BusinessException(StatusCode::DATA_ERROR, '数据不存在!');
        }
        return $result;
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
            'name'   => self::getRequest($data, 'name', 'string'),
            'is_hot' => self::getRequest($data, 'is_hot', 'int', 0),
            'num'    => self::getRequest($data, 'num', 'int', 0),
            'sort'   => self::getRequest($data, 'sort', 'int', 0),
        ];
        if (empty($row['name'])) {
            throw  new BusinessException(StatusCode::PARAMETER_ERROR, '参数错误!');
        }
        $row['_id'] = md5($row['name']);

        if (ComicsKeywordsModel::count(['_id' => $row['_id']]) > 0) {
            return ComicsKeywordsModel::updateById($row, $row['_id']);
        }
        return ComicsKeywordsModel::insert($row);
    }

    /**
     * 删除
     * @param        $id
     * @return mixed
     */
    public static function delete($id)
    {
        return ComicsKeywordsModel::deleteByID($id);
    }
}
