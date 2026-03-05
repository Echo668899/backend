<?php

declare(strict_types=1);

namespace App\Repositories\Backend\Movie;

use App\Constants\CommonValues;
use App\Constants\StatusCode;
use App\Core\Repositories\BaseRepository;
use App\Exception\BusinessException;
use App\Models\Movie\MovieTagModel;

/**
 * 标签
 * @package App\Repositories\Backend
 */
class MovieTagRepository extends BaseRepository
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

        $query  = [];
        $filter = [];

        if ($request['name']) {
            $filter['name'] = self::getRequest($request, 'name');
            $query['name']  = ['$regex' => $filter['name'], '$options' => 'i'];
        }
        if ($request['parent_id'] !== '' && $request['parent_id'] !== null) {
            $filter['parent_id'] = self::getRequest($request, 'parent_id', 'int');
            $query['parent_id']  = $filter['parent_id'];
        }
        if ($request['attribute'] !== '' && $request['attribute'] !== null) {
            $filter['attribute'] = self::getRequest($request, 'attribute', 'string');
            $query['attribute']  = $filter['attribute'];
        }

        if ($request['is_hot'] !== '' && $request['is_hot'] !== null) {
            $filter['is_hot'] = self::getRequest($request, 'is_hot', 'int');
            $query['is_hot']  = $filter['is_hot'];
        }

        if ($request['is_show_upload'] !== '' && $request['is_show_upload'] !== null) {
            $filter['is_show_upload'] = self::getRequest($request, 'is_show_upload', 'int');
            $query['is_show_upload']  = $filter['is_show_upload'];
        }

        $skip   = ($page - 1) * $pageSize;
        $fields = [];
        $count  = MovieTagModel::count($query);
        $items  = MovieTagModel::find($query, $fields, [$sort => $order], $skip, $pageSize);
        foreach ($items as $index => $item) {
            $item['created_at']     = date('Y-m-d H:i', $item['created_at']);
            $item['updated_at']     = date('Y-m-d H:i', $item['updated_at']);
            $item['is_hot']         = CommonValues::getIs($item['is_hot']);
            $item['is_show_upload'] = CommonValues::getIs($item['is_show_upload'] ?? 0);
            $item['parent_id']      = $item['parent_id'] ?: '-';
            $items[$index]          = $item;
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
            'name'           => self::getRequest($data, 'name'),
            'is_hot'         => self::getRequest($data, 'is_hot', 'int'),
            'attribute'      => self::getRequest($data, 'attribute', 'string'),
            'description'    => self::getRequest($data, 'description', 'string'),
            'is_show_upload' => self::getRequest($data, 'is_show_upload', 'int'),
        ];
        if (empty($row['name']) || empty($row['attribute'])) {
            throw  new BusinessException(StatusCode::PARAMETER_ERROR, '参数错误!');
        }
        $row['name'] = strtolower($row['name']);

        $where = ['name' => $row['name']];
        if ($data['_id'] > 0) {
            $row['_id']   = self::getRequest($data, '_id', 'int');
            $where['_id'] = ['$ne' => $row['_id']];
        }
        if (MovieTagModel::count($where)) {
            throw  new BusinessException(StatusCode::PARAMETER_ERROR, '标签名称不能重复!');
        }
        return MovieTagModel::save($row);
    }

    /**
     * @param             $data
     * @return bool|mixed
     */
    public static function update($data)
    {
        return MovieTagModel::updateById($data, $data['_id']);
    }

    /**
     * 获取详情
     * @param                    $id
     * @return mixed
     * @throws BusinessException
     */
    public static function getDetail($id)
    {
        $row = MovieTagModel::findByID(intval($id));
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
        return MovieTagModel::deleteById(intval($id));
    }
}
