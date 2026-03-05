<?php

namespace App\Services\User;

use App\Constants\CommonValues;
use App\Core\Services\BaseService;
use App\Jobs\Stats\StatsUserUpJob;
use App\Models\User\UserUpModel;
use App\Services\Common\CommonService;
use App\Services\Common\JobService;
use App\Utils\CommonUtil;

class UserUpService extends BaseService
{
    /**
     * @param       $userId
     * @param       $nickname
     * @param       $headico
     * @param       $category
     * @param       $status
     * @param       $postFeeRate     //帖子分成
     * @param       $postUploadNum   //每日最大发帖数量
     * @param       $movieFeeRate    //视频分成
     * @param       $movieMoneyLimit //视频价格限制
     * @param       $movieUploadNum  //每日最大视频数量
     * @return true
     */
    public static function do($userId, $nickname, $headico, $category, $status, $postFeeRate = 0, $postUploadNum = 10, $movieFeeRate = 0, $movieMoneyLimit = 100, $movieUploadNum = 10)
    {
        $userId = intval($userId);
        if ($status) {
            $postFeeRate  = max(0, min($postFeeRate, 100));
            $movieFeeRate = max(0, min($movieFeeRate, 100));
            if (self::has($userId) == false) {
                UserUpModel::insert([
                    '_id'          => $userId,
                    'nickname'     => strval($nickname),
                    'headico'      => strval($headico),
                    'categories'   => $category,
                    'is_hot'       => 0,
                    'cup'          => '',
                    'birthday'     => '',
                    'sort'         => 0,
                    'first_letter' => strtoupper(substr(CommonUtil::pinyin($nickname, true), 0, 1)),

                    'post_fee_rate'     => intval($postFeeRate),
                    'post_upload_num'   => intval($postUploadNum),
                    'movie_fee_rate'    => intval($movieFeeRate),
                    'movie_money_limit' => intval($movieMoneyLimit),
                    'movie_upload_num'  => intval($movieUploadNum),
                ]);
                JobService::create(new StatsUserUpJob($userId));
            } else {
                UserUpModel::updateById([
                    'nickname'     => strval($nickname),
                    'first_letter' => strtoupper(substr(CommonUtil::pinyin($nickname, true), 0, 1)),
                    'headico'      => strval($headico),
                    'categories'   => $category,

                    'post_fee_rate'     => intval($postFeeRate),
                    'post_upload_num'   => intval($postUploadNum),
                    'movie_fee_rate'    => intval($movieFeeRate),
                    'movie_money_limit' => intval($movieMoneyLimit),
                    'movie_upload_num'  => intval($movieUploadNum),
                ], $userId);
            }
        } else {
            UserUpModel::deleteById($userId);
        }
        return true;
    }

    /**
     * @param       $userId
     * @return bool
     */
    public static function has($userId)
    {
        return boolval(UserUpModel::count(['_id' => intval($userId)]));
    }

    /**
     * 获取所有
     * @param        $category
     * @return array
     */
    public static function getAll($category = '')
    {
        $query = [];
        if (!empty($category)) {
            $query['category'] = $category;
        }
        $rows = UserUpModel::find($query, [], [], 0, 5000);
        return $rows;
    }

    /**
     * 搜索
     * @param  array $filter
     * @param        $userId
     * @return array
     */
    public static function doSearch(array $filter = [], $userId = null)
    {
        $page     = $filter['page'] ?: 1;
        $pageSize = $filter['page_size'] ?: 24;
        $keywords = strval($filter['keywords']);
        $catId    = strval($filter['cat_id']);
        $order    = $filter['order'] ?: 'favorite';
        $ids      = $filter['ids'];

        $query = [];
        if ($keywords) {
            $query['nickname'] = ['$regex' => $keywords, '$options' => 'i'];
        }
        if ($catId) {
            $query['categories'] = $catId;
        }
        if ($ids) {
            $idArr = explode(',', $ids);
            foreach ($idArr as $key => $id) {
                if ($id) {
                    $idArr[$key] = intval($id);
                } else {
                    unset($idArr[$key]);
                }
            }
            $query['_id'] = ['$in' => $idArr];
        }
        $sort = [];
        switch ($order) {
            case 'fans':
                $sort['fans_total'] = -1;
                break;
            case 'movie_hot':
                $sort['movie_hot'] = -1;
                break;
            case 'new':
                $sort['created_at'] = -1;
                break;
            case 'movie_click':
                $sort['movie_click_total'] = -1;
                break;
            case 'movie_favorite': // 收藏数
                $sort['movie_favorite_total'] = -1;
                break;
            case 'movie_total': // 作品数
                $sort['movie_total'] = -1;
                break;
            case 'letter': // 首字母
                $sort['first_letter'] = 1;
                break;
        }
        $count       = UserUpModel::count($query);
        $rows        = UserUpModel::find($query, [], $sort, ($page - 1) * $pageSize, $pageSize);
        $relationMap = UserFansService::getMultiRelationStatus($userId, array_column($rows, '_id'));
        foreach ($rows as &$row) {
            $row = [
                'id'          => strval($row['_id']),
                'nickname'    => strval($row['nickname']),
                'categories'  => strval(CommonValues::getUpCategories($row['categories'])),
                'headico'     => CommonService::getCdnUrl($row['headico']),
                'post_total'  => strval(CommonUtil::formatNum($row['post_total'])),
                'movie_total' => strval(CommonUtil::formatNum($row['movie_total'])),
                'fans_total'  => strval(CommonUtil::formatNum($row['fans_total'])),

                'relation' => $relationMap[$row['_id']] ?? 'none'
            ];
            unset($row);
        }

        $result = [
            'data'         => $rows,
            'total'        => strval($count),
            'current_page' => strval($page),
            'page_size'    => strval($pageSize),
        ];
        $result['last_page'] = strval(ceil($count / $pageSize));
        return $result;
    }
}
