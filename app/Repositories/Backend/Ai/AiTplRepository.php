<?php

declare(strict_types=1);

namespace App\Repositories\Backend\Ai;

use App\Constants\CommonValues;
use App\Constants\StatusCode;
use App\Core\Repositories\BaseRepository;
use App\Exception\BusinessException;
use App\Models\Ai\AiTplModel;
use App\Services\Admin\AdminLogService;
use App\Services\Ai\AiTagService;

class AiTplRepository extends BaseRepository
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
        if ($request['type']) {
            $filter['type'] = self::getRequest($request, 'type');
            $query['type']  = $filter['type'];
        }
        if ($request['tags']) {
            $filter['tags'] = self::getRequest($request, 'tags', 'int');
            $query['tags']  = ['$in' => [$filter['tags']]];
        }
        $skip   = ($page - 1) * $pageSize;
        $fields = [];
        $count  = AiTplModel::count($query);
        $items  = AiTplModel::find($query, $fields, [$sort => $order], $skip, $pageSize);

        foreach ($items as $index => $item) {
            $item['created_at']  = date('Y-m-d H:i', $item['created_at']);
            $item['updated_at']  = date('Y-m-d H:i', $item['updated_at']);
            $item['is_disabled'] = CommonValues::getIs($item['is_disabled']);
            $item['adult']       = CommonValues::getIs($item['adult']);
            $item['type']        = CommonValues::getAiTplType($item['type']);

            $item['tags'] = value(function () use ($item) {
                $tags = AiTagService::getByIds($item['tags']);
                return $tags ? join(',', array_column($tags, 'name')) : '-';
            });
            $items[$index] = $item;
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
     * @param                      $data
     * @return bool|int|mixed|null
     * @throws BusinessException
     */
    public static function save($data)
    {
        $row = [
            'name'        => self::getRequest($data, 'name'),
            'description' => self::getRequest($data, 'description', 'string', ''),
            'type'        => self::getRequest($data, 'type', 'string'),
            'img'         => self::getRequest($data, 'img', 'string', ''),
            'money'       => self::getRequest($data, 'money', 'int', 0),
            'sort'        => self::getRequest($data, 'sort', 'int', 0),
            'adult'       => self::getRequest($data, 'adult', 'int', 0),
            'is_disabled' => self::getRequest($data, 'is_disabled', 'int', 0),

            'tags' => value(function () use ($data) {
                $tagIds = $_REQUEST['tags'];
                $result = [];
                foreach ($tagIds as $tagId) {
                    if (empty($tagId)) {
                        continue;
                    }
                    $result[] = intval($tagId);
                }
                return $result;
            }),
        ];

        // 处理config字段
        $config = $data['config'];
        if (!empty($config)) {
            $configArray = json_decode($config, true);
            if (json_last_error() !== JSON_ERROR_NONE) {
                throw new BusinessException(StatusCode::PARAMETER_ERROR, 'config格式错误: ' . json_last_error_msg());
            }
            $row['config'] = $configArray;
        } else {
            $configArray = [];
            // 如果是新增,图片换脸自动生成配置
            if (empty($data['_id']) && $row['type'] == 'change_face_image') {
                $configArray = [
                    'img' => $row['img']
                ];
            }
            $row['config'] = $configArray;
        }

        if (empty($row['name'])) {
            throw new BusinessException(StatusCode::PARAMETER_ERROR, '名称不能为空!');
        }

        if (!empty($data['_id'])) {
            $row['_id'] = self::getRequest($data, '_id', 'string');
            $result     = AiTplModel::update($row, ['_id' => $row['_id']]);
        } else {
            $row['created_at'] = time();
            $row['updated_at'] = time();
            $row['_id']        = $row['type'] . '_' . uniqid();
            $result            = AiTplModel::insert($row, false);
        }

        AdminLogService::do(sprintf('操作AI模板,名称:%s,ID:%s', $row['name'], empty($row['_id']) ? $result : $row['_id']));
        return $result;
    }

    /**
     * 获取详情
     * @param                    $id
     * @return array|mixed
     * @throws BusinessException
     */
    public static function getDetail($id)
    {
        $row = AiTplModel::findByID($id);
        if (empty($row)) {
            throw new BusinessException(StatusCode::DATA_ERROR, '数据不存在!');
        }
        // 将config转为JSON字符串
        if (isset($row['config']) && is_array($row['config'])) {
            $row['config'] = json_encode($row['config'], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT);
        } else {
            $row['config'] = '';
        }

        return $row;
    }

    /**
     * 删除
     * @param           $id
     * @return bool|int
     */
    public static function delete($id)
    {
        AdminLogService::do(sprintf('删除AI模板,ID:%s', $id));
        return AiTplModel::delete(['_id' => $id]);
    }
}
