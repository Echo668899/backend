<?php

declare(strict_types=1);

namespace App\Repositories\Backend\Audio;

use App\Constants\CommonValues;
use App\Constants\StatusCode;
use App\Core\Repositories\BaseRepository;
use App\Exception\BusinessException;
use App\Models\Audio\AudioNavModel;
use App\Services\Audio\AudioNavService;
use App\Utils\CommonUtil;

class AudioNavRepository extends BaseRepository
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
        if ($request['position']) {
            $filter['position'] = self::getRequest($request, 'position', 'string');
            $query['position']  = $filter['position'];
        }

        $skip   = ($page - 1) * $pageSize;
        $fields = [];
        $count  = AudioNavModel::count($query);
        $items  = AudioNavModel::find($query, $fields, [$sort => $order], $skip, $pageSize);
        foreach ($items as $index => $item) {
            $item['is_disabled'] = CommonValues::getIs($item['is_disabled']);
            $item['position']    = CommonValues::getAudioNavPosition($item['position']);
            $item['created_at']  = date('Y-m-d H:i:s', $item['created_at']);
            $item['updated_at']  = date('Y-m-d H:i:s', $item['updated_at']);

            $item['style'] = value(function () use ($item) {
                $style = CommonValues::getAudioNavStyle($item['style']);
                return $item['style'] . ' | ' . (is_array($style) ? '注意:错误的样式' : $style);
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
     * @param                 $data
     * @return bool|int|mixed
     * @throws \Exception
     */
    public static function save($data)
    {
        $row = [
            'name'            => self::getRequest($data, 'name'),
            'code'            => self::getRequest($data, 'code', 'string'),
            'position'        => self::getRequest($data, 'position', 'string'),
            'style'           => self::getRequest($data, 'style', 'string'),
            'sort'            => self::getRequest($data, 'sort', 'int', 0),
            'filter'          => self::getRequest($data, 'filter'),
            'is_disabled'     => self::getRequest($data, 'is_disabled', 'int', 0),
            'seo_title'       => self::getRequest($data, 'seo_title', 'string'),
            'seo_keywords'    => self::getRequest($data, 'seo_keywords', 'string'),
            'seo_description' => self::getRequest($data, 'seo_description', 'string'),
        ];
        if (empty($row['name']) || empty($row['position'])) {
            throw  new BusinessException(StatusCode::PARAMETER_ERROR, '参数错误!');
        }

        if ($row['style'] == 'audio_2') {
            $row['filter'] = stripcslashes($row['filter']);
            if (!json_decode($row['filter'], true)) {
                throw  new BusinessException(StatusCode::PARAMETER_ERROR, CommonValues::getComicsNavStyle($row['style']) . ' 必须输入搜索条件!');
            }
        }

        if ($row['style'] == 'audio_3') {
            $row['filter'] = stripcslashes($row['filter']);
            if (CommonUtil::arrayDepth(json_decode($row['filter'], true)) == 1) {
                throw  new BusinessException(StatusCode::PARAMETER_ERROR, CommonValues::getComicsNavStyle($row['style']) . ' 该样式务必输入数组,格式为: [{"name":"二次元":"filter":{}},{"name":"二次元":"filter":{}}]  其中filter为具体搜索条件,搜索条件参考模块 ');
            }

            $hasChildFilter = false;
            foreach (json_decode($row['filter'], true) as $index => $item) {
                if (empty($item['name'])) {
                    throw  new BusinessException(StatusCode::PARAMETER_ERROR, CommonValues::getComicsBlockStyle($row['style']) . " 特殊模块,搜索条件配置错误\n可寻求技术指导");
                }
                if (empty($item['filter'])) {
                    throw  new BusinessException(StatusCode::PARAMETER_ERROR, CommonValues::getComicsBlockStyle($row['style']) . " 特殊模块,搜索条件配置错误\n可寻求技术指导");
                }
                $hasChildFilter = true;
            }
            if ($hasChildFilter == false) {
                throw  new BusinessException(StatusCode::PARAMETER_ERROR, CommonValues::getComicsBlockStyle($row['style']) . " 特殊模块,搜索条件配置错误\n可寻求技术指导");
            }
        }

        if ($data['_id']) {
            $row['_id'] = self::getRequest($data, '_id', 'int');
        }
        $result = AudioNavModel::save($row);
        if ($result) {
            AudioNavService::deleteCache();
        }
        return $result;
    }

    /**
     * 获取详情
     * @param             $id
     * @return mixed
     * @throws \Exception
     */
    public static function getDetail($id)
    {
        $row = AudioNavModel::findByID(intval($id));
        if (empty($row)) {
            throw  new BusinessException(StatusCode::PARAMETER_ERROR, '数据不存在!');
        }
        return $row;
    }

    /**
     * @param        $id
     * @return mixed
     */
    public static function delete($id)
    {
        return AudioNavModel::deleteById(intval($id));
    }
}
