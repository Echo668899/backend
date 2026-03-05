<?php

namespace App\Services\Movie;

use App\Constants\StatusCode;
use App\Core\Services\BaseService;
use App\Exception\BusinessException;
use App\Jobs\Event\EventBusJob;
use App\Jobs\Event\Payload\Movie\MovieDownloadPayload;
use App\Models\Movie\MovieDownloadModel;
use App\Models\Movie\MovieModel;
use App\Services\Common\CommonService;
use App\Services\Common\IpService;
use App\Services\Common\JobService;
use App\Services\Common\M3u8Service;
use App\Services\User\UserBuyLogService;
use App\Services\User\UserGroupService;
use App\Services\User\UserService;
use App\Utils\CommonUtil;

class MovieDownloadService extends BaseService
{
    /**
     * 缓存列表
     * @param        $userId
     * @param  int   $page
     * @param  int   $pageSize
     * @return array
     */
    public static function getDownloadList($userId, $page = 1, $pageSize = 15)
    {
        $result = [];
        $skip   = ($page - 1) * $pageSize;
        $items  = MovieDownloadModel::find(['user_id' => $userId, 'status' => 1], [], ['_id' => -1], $skip, $pageSize);
        foreach ($items as $item) {
            $result[] = [
                'task_id'  => strval($item['_id']),
                'movie_id' => strval($item['movie_id']),
                'link_ids' => $item['link_ids'],
                'name'     => strval($item['name']),
                'img'      => CommonService::getCdnUrl($item['img']),
                'duration' => strval($item['duration']),
            ];
        }
        return $result;
    }

    /**
     * 下载视频
     * @param                    $userId
     * @param                    $movieId
     * @param  mixed             $linkId
     * @return array
     * @throws BusinessException
     */
    public static function do($userId, $movieId, $linkId)
    {
        $userId  = intval($userId);
        $movieId = strval($movieId);
        $linkId  = strval($linkId);

        $userInfo = UserService::getInfoFromCache($userId);
        UserService::checkDisabled($userInfo);
        $movieRow = MovieModel::findByID($movieId);
        if (empty($movieRow) || $movieRow['status'] != 1) {
            throw new BusinessException(StatusCode::DATA_ERROR, '视频已下架!');
        }
        // /视频上架15日内 不允许下载
        //        if($movieInfo['show_at']+86400*15>time()){
        //            throw new BusinessException(StatusCode::DATA_ERROR, "本视频处于保护期内! 将于".(date('Y-m-d H:i',$movieInfo['show_at']+86400*15))."开放");
        //        }

        $links       = $movieRow['links'];
        $currentLink = null;
        foreach ($links as $link) {
            if ($link['id'] == $linkId) {
                $currentLink = $link;
            }
        }
        if (empty($currentLink)) {
            throw new BusinessException(StatusCode::DATA_ERROR, '视频已下架!');
        }

        $idValue = md5($userId . '_' . $movieId);
        $hasDown = MovieDownloadModel::findByID($idValue);

        if (empty($hasDown) || $userId != $movieRow['user_id']) {
            // 该链接没有下载过
            if (!in_array($linkId, $hasDown['link_ids'])) {
                // 判断视频类型
                if ($movieRow['pay_type'] == 'money') {
                    if ($userInfo['group_rate'] != '0' && !UserBuyLogService::has($userId, $movieId, 'movie', $linkId)) {
                        throw new BusinessException(StatusCode::DATA_ERROR, '请购买后下载!');
                    }
                } else {
                    // 是否有权限
                    if (!in_array('do_download', UserService::getRights($userInfo))) {
                        throw new BusinessException(StatusCode::DATA_ERROR, '您没有下载权限!');
                    }
                }
            }
        }

        $maxNum     = self::getDownloadMaxNum($userInfo['group_id']);
        $maxNumDark = self::getDownloadMaxNum($userInfo['group_dark_id']);
        if ($maxNum < $maxNumDark) {
            $maxNum = $maxNumDark;
        }
        $usedNum = self::getDownloadUsedNum($userId);
        if ($usedNum >= $maxNum) {
            throw new BusinessException(StatusCode::DATA_ERROR, '您本周下载次数已经用完!');
        }
        //        $m3u8Info = M3u8Service::doDownload($currentLink['m3u8_url']);
        //        if (empty($m3u8Info['content'])) {
        //            throw new BusinessException(StatusCode::DATA_ERROR, '系统解析失败,请稍后重试!');
        //        }

        $data = [
            '_id'      => $idValue,
            'name'     => $movieRow['name'],
            'movie_id' => $movieId,
            'user_id'  => intval($userId),
            'link_ids' => value(function () use ($hasDown, $linkId) {
                $ids   = $hasDown['link_ids'] ?? [];
                $ids[] = $linkId;
                return array_values($ids);
            }),
            'img'        => $movieRow['img_x'],
            'duration'   => $currentLink['duration'] * 1,
            'label'      => date('Y-m-d'),
            'status'     => 1,
            'created_at' => time(),
            'updated_at' => time(),
        ];

        MovieDownloadModel::findAndModify(['_id' => $data['_id']], ['$set' => $data], [], true);
        // 修改视频下载数量
        MovieService::handler('download', $movieId);

        JobService::create(new EventBusJob(new MovieDownloadPayload($userId, $movieId, $linkId)));

        return [
            'task_id' => strval($data['_id']),
            'link'    => M3u8Service::encode($currentLink['m3u8_url'], IpService::isChina(CommonUtil::getClientIp())),
            //            'content' => $m3u8Info['content'],
            //            'files' => $m3u8Info['files'],
        ];
    }

    /**
     * 获取可缓存的次数
     * @param      $groupId
     * @return int
     */
    public static function getDownloadMaxNum($groupId)
    {
        if (empty($groupId)) {
            return 0;
        }
        $group = UserGroupService::getInfo($groupId);
        if (empty($group)) {
            return 0;
        }
        return $group['download_num'] * 1;
    }

    /**
     * 获取本周已经下载的次数
     * @param        $userId
     * @return mixed
     */
    public static function getDownloadUsedNum($userId)
    {
        $startTime  = CommonUtil::getWeekFirst();
        $startTime  = intval($startTime);
        $countWhere = [
            'user_id'    => intval($userId),
            'created_at' => ['$gte' => $startTime]
        ];
        return MovieDownloadModel::count($countWhere);
    }

    /**
     * 获取下载记录id
     * @param        $userId
     * @param        $page
     * @param        $pageSize
     * @return array
     */
    public static function getIds($userId, $page = 1, $pageSize = 12)
    {
        $query = ['user_id' => $userId, 'status' => 1];
        $count = MovieDownloadModel::count($query);
        $rows  = MovieDownloadModel::find($query, ['movie_id'], ['updated_at' => -1], ($page - 1) * $pageSize, $pageSize);
        $ids   = array_column($rows, 'movie_id');
        return [
            'ids'          => $ids,
            'total'        => $count,
            'current_page' => $page,
            'page_size'    => $pageSize,
            'last_page'    => strval(ceil($count / $pageSize))
        ];
    }
}
