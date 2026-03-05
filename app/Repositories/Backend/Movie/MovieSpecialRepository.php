<?php

namespace App\Repositories\Backend\Movie;

use App\Constants\StatusCode;
use App\Core\Repositories\BaseRepository;
use App\Exception\BusinessException;
use App\Models\Movie\MovieSpecialModel;
use App\Services\Movie\MovieSpecialService;

class MovieSpecialRepository extends BaseRepository
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

        if ($request['position']) {
            $filter['position'] = self::getRequest($request, 'position', 'string');
            $query['position']  = $filter['position'];
        }
        $skip   = ($page - 1) * $pageSize;
        $fields = [];
        $count  = MovieSpecialModel::count($query);
        $items  = MovieSpecialModel::find($query, $fields, [$sort => $order], $skip, $pageSize);
        foreach ($items as $index => $item) {
            $item['created_at'] = date('Y-m-d H:i', $item['created_at']);
            $item['updated_at'] = date('Y-m-d H:i', $item['updated_at']);
            $item['position']   = MovieSpecialService::$position[$item['position']];
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
            'name'        => self::getRequest($data, 'name'),
            'sort'        => self::getRequest($data, 'sort', 'int', 0),
            'filter'      => self::getRequest($data, 'filter'),
            'img'         => self::getRequest($data, 'img', 'string', ''),
            'bg_img'      => self::getRequest($data, 'bg_img', 'string', ''),
            'position'    => self::getRequest($data, 'position', 'string'),
            'is_disabled' => self::getRequest($data, 'is_disabled', 'int', 0),
            'description' => self::getRequest($data, 'description', 'string', ''),
        ];
        $row['filter'] = stripcslashes($row['filter']);
        if (empty($row['name']) || empty($row['filter'])) {
            throw  new BusinessException(StatusCode::PARAMETER_ERROR, '参数错误!');
        }

        if (!json_decode($row['filter'], true)) {
            throw  new BusinessException(StatusCode::PARAMETER_ERROR, 'json格式错误!');
        }

        if ($data['_id'] > 0) {
            $row['_id'] = self::getRequest($data, '_id', 'int');
        }
        return MovieSpecialModel::save($row);
    }

    /**
     * 获取详情
     * @param                    $id
     * @return mixed
     * @throws BusinessException
     */
    public static function getDetail($id)
    {
        $row = MovieSpecialModel::findByID(intval($id));
        if (empty($row)) {
            throw  new BusinessException(StatusCode::DATA_ERROR, '数据不存在!');
        }
        $row['filter'] = stripcslashes($row['filter']);
        return $row;
    }

    /**
     * 删除
     * @param        $id
     * @return mixed
     */
    public static function delete($id)
    {
        return MovieSpecialModel::deleteById(intval($id));
    }
}
