<?php

namespace App\Repositories\Backend\Common;

use App\Constants\CommonValues;
use App\Constants\StatusCode;
use App\Core\Repositories\BaseRepository;
use App\Exception\BusinessException;
use App\Models\Common\DomainModel;
use App\Services\Common\DomainService;

class DomainRepository extends BaseRepository
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

        if ($request['type']) {
            $filter['type'] = self::getRequest($request, 'type');
            $query['type']  = $filter['type'];
        }
        if ($request['domain']) {
            $filter['domain'] = self::getRequest($request, 'domain');
            $query['domain']  = ['$regex' => $filter['domain'], '$options' => 'i'];
        }
        if ($request['is_disabled'] !== '') {
            $filter['is_disabled'] = self::getRequest($request, 'is_disabled', 'int');
            $query['is_disabled']  = $filter['is_disabled'] * 1;
        }

        $skip   = ($page - 1) * $pageSize;
        $fields = [];
        $count  = DomainModel::count($query);
        $items  = DomainModel::find($query, $fields, [$sort => $order], $skip, $pageSize);
        foreach ($items as $index => $item) {
            $item['tracking_code'] = $item['tracking_code'] ? '已配置' : '未配置';
            $item['type']          = CommonValues::getDomainType($item['type']);
            $item['is_disabled']   = CommonValues::getIs($item['is_disabled']);
            $item['created_at']    = date('Y-m-d H:i', $item['created_at']);
            $item['updated_at']    = date('Y-m-d H:i', $item['updated_at']);
            $items[$index]         = $item;
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
     * @param  string            $id
     * @return array
     * @throws BusinessException
     */
    public static function getDetail($id)
    {
        $row = DomainModel::findByID(intval($id));
        if (empty($row)) {
            throw new BusinessException(StatusCode::DATA_ERROR, '数据不存在!');
        }
        $row['tracking_code'] = stripslashes($row['tracking_code']);
        return $row;
    }

    /**
     * 保存数据
     * @param  array             $data
     * @return bool|int|mixed
     * @throws BusinessException
     */
    public static function save($data)
    {
        $row = [
            'type'          => self::getRequest($data, 'type', 'string'),
            'domain'        => self::getRequest($data, 'domain', 'string'),
            'tracking_code' => self::getRequest($data, 'tracking_code', 'html', ''),
            'is_disabled'   => self::getRequest($data, 'is_disabled', 'int', 0),
        ];

        if (empty($row['type']) || empty($row['domain'])) {
            throw new BusinessException(StatusCode::PARAMETER_ERROR, '参数错误!');
        }
        if (parse_url($row['domain'], PHP_URL_SCHEME) != null) {
            throw new BusinessException(StatusCode::PARAMETER_ERROR, '域名请不要输入协议 http:// 或 https:// ');
        }

        if ($data['_id'] > 0) {
            $row['_id'] = self::getRequest($data, '_id', 'int');
        } else {
            $row['check'] = [];
        }
        $checkDomain = DomainModel::findFirst(['domain' => $row['domain']]);
        if ($checkDomain && $checkDomain['_id'] != $data['_id']) {
            throw  new BusinessException(StatusCode::PARAMETER_ERROR, '域名已经存在!');
        }
        DomainService::deleteCache();
        return DomainModel::save($row);
    }

    /**
     * 删除
     * @param  string            $id
     * @return mixed
     * @throws BusinessException
     */
    public static function delete($id)
    {
        $row = DomainModel::findByID(intval($id));
        if (empty($row)) {
            throw new BusinessException(StatusCode::DATA_ERROR, '数据不存在!');
        }
        return DomainModel::deleteById(intval($id));
    }

    /**
     * 标记检查
     * @param       $id
     * @return bool
     */
    public static function check($id)
    {
        return DomainModel::updateById(['updated_at' => time()], intval($id));
    }
}
