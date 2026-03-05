<?php

namespace App\Repositories\Backend\Common;

use App\Constants\CommonValues;
use App\Constants\StatusCode;
use App\Core\Repositories\BaseRepository;
use App\Exception\BusinessException;
use App\Models\Common\ArticleModel;
use App\Services\Common\ArticleCategoryService;

class ArticleRepository extends BaseRepository
{
    /**
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
        if ($request['category_code']) {
            $filter['category_code'] = self::getRequest($request, 'category_code');
            $query['category_code']  = $filter['category_code'];
        }

        $skip              = ($page - 1) * $pageSize;
        $fields            = [];
        $count             = ArticleModel::count($query);
        $items             = ArticleModel::find($query, $fields, [$sort => $order], $skip, $pageSize);
        $articleCategories = ArticleCategoryService::getAll();
        foreach ($items as $index => $item) {
            $item['created_at']    = date('Y-m-d H:i', $item['created_at']);
            $item['updated_at']    = date('Y-m-d H:i', $item['updated_at']);
            $item['is_recommend']  = CommonValues::getIs($item['is_recommend']);
            $item['category_name'] = $articleCategories[$item['category_code']]['name'];
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
     * @param                      $data
     * @return bool|int|mixed|null
     * @throws BusinessException
     */
    public static function save($data)
    {
        $row = [
            'title'           => self::getRequest($data, 'title'),
            'content'         => self::getRequest($data, 'content'),
            'img'             => self::getRequest($data, 'img'),
            'seo_keywords'    => self::getRequest($data, 'seo_keywords'),
            'seo_description' => self::getRequest($data, 'seo_description'),
            'url'             => self::getRequest($data, 'url'),
            'category_code'   => self::getRequest($data, 'category_code'),
            'is_recommend'    => self::getRequest($data, 'is_recommend', 'int', 0),
            'sort'            => self::getRequest($data, 'sort', 'int', 0),
            'click'           => self::getRequest($data, 'click', 'int', 0),
            'created_at'      => self::getRequest($data, 'created_at', 'string'),
        ];
        $row['created_at'] = $row['created_at'] ? strtotime($row['created_at']) : time();
        if (empty($row['title']) || empty($row['category_code']) || empty($row['content'])) {
            throw  new BusinessException(StatusCode::PARAMETER_ERROR, '参数错误!');
        }
        if ($data['_id'] > 0) {
            $row['_id'] = self::getRequest($data, '_id', 'int');
        }
        return ArticleModel::save($row);
    }

    /**
     * @param                    $id
     * @return array|mixed
     * @throws BusinessException
     */
    public static function getDetail($id)
    {
        $row = ArticleModel::findByID(intval($id));
        if (empty($row)) {
            throw  new BusinessException(StatusCode::DATA_ERROR, '数据不存在!');
        }
        $row['created_at'] = date('Y-m-d H:i:s', $row['created_at']);
        return $row;
    }

    /**
     * @param           $id
     * @return bool|int
     */
    public static function delete($id)
    {
        return ArticleModel::deleteById(intval($id));
    }
}
