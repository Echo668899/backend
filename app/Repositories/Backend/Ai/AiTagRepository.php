<?php

declare(strict_types=1);

namespace App\Repositories\Backend\Ai;

use App\Constants\CommonValues;
use App\Constants\StatusCode;
use App\Core\Repositories\BaseRepository;
use App\Exception\BusinessException;
use App\Models\Ai\AiTagModel;

/**
 * 标签
 * @package App\Repositories\Backend
 */
class AiTagRepository extends BaseRepository
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
        if ($request['is_hot'] !== '' && $request['is_hot'] !== null) {
            $filter['is_hot'] = self::getRequest($request, 'is_hot', 'int');
            $query['is_hot']  = $filter['is_hot'];
        }
        if ($request['type'] !== '' && $request['type'] !== null) {
            $filter['type'] = self::getRequest($request, 'type', 'string');
            $query['type']  = $filter['type'];
        }

        $skip   = ($page - 1) * $pageSize;
        $fields = [];
        $count  = AiTagModel::count($query);
        $items  = AiTagModel::find($query, $fields, [$sort => $order], $skip, $pageSize);
        foreach ($items as $index => $item) {
            $item['created_at'] = date('Y-m-d H:i', $item['created_at']);
            $item['updated_at'] = date('Y-m-d H:i', $item['updated_at']);
            $item['type']       = CommonValues::getAiTplType($item['type']);
            $item['is_hot']     = CommonValues::getIs($item['is_hot']);
            $item['parent_id']  = $item['parent_id'] ?: '-';
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
            'name'   => self::getRequest($data, 'name'),
            'is_hot' => self::getRequest($data, 'is_hot', 'int'),
            'type'   => self::getRequest($data, 'type', 'string'),
        ];
        if (empty($row['name']) || empty($row['type'])) {
            throw  new BusinessException(StatusCode::PARAMETER_ERROR, '参数错误!');
        }
        $row['name'] = strtolower($row['name']);

        $where = ['name' => $row['name'], 'type' => $row['type']];
        if ($data['_id'] > 0) {
            $row['_id']   = self::getRequest($data, '_id', 'int');
            $where['_id'] = ['$ne' => $row['_id']];
        }
        if (AiTagModel::count($where)) {
            throw  new BusinessException(StatusCode::PARAMETER_ERROR, '标签名称不能重复!');
        }
        return AiTagModel::save($row);
    }

    /**
     * @param             $data
     * @return bool|mixed
     */
    public static function update($data)
    {
        return AiTagModel::updateById($data, $data['_id']);
    }

    /**
     * 获取详情
     * @param                    $id
     * @return mixed
     * @throws BusinessException
     */
    public static function getDetail($id)
    {
        $row = AiTagModel::findByID(intval($id));
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
        return AiTagModel::deleteById(intval($id));
    }
}
