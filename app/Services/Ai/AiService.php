<?php

namespace App\Services\Ai;

use App\Constants\StatusCode;
use App\Core\Services\BaseService;
use App\Exception\BusinessException;
use App\Jobs\Event\EventBusJob;
use App\Jobs\Event\Payload\Ai\AIChangeDressBarePayload;
use App\Jobs\Event\Payload\Ai\AIChangeDressPayload;
use App\Jobs\Event\Payload\Ai\AIChangeFaceImagePayload;
use App\Jobs\Event\Payload\Ai\AIChangeFaceVideoPayload;
use App\Jobs\Event\Payload\Ai\AiImageToVideoPayload;
use App\Jobs\Event\Payload\Ai\AiNovelPayload;
use App\Jobs\Event\Payload\Ai\AiTextToImagePayload;
use App\Jobs\Event\Payload\Ai\AiTextToVoicePayload;
use App\Models\Ai\AiOrderModel;
use App\Models\Ai\AiTplModel;
use App\Models\Common\ConfigModel;
use App\Models\User\UserModel;
use App\Services\Common\CommonService;
use App\Services\Common\ConfigService;
use App\Services\Common\JobService;
use App\Services\Common\M3u8Service;
use App\Services\User\AccountService;
use App\Services\User\UserService;
use App\Utils\CommonUtil;
use App\Utils\FileUtil;
use App\Utils\LogUtil;
use Phalcon\Manager\MediaLSJAiService;

/**
 * AI
 */
class AiService extends BaseService
{
    /**
     * 订单信息
     * @param mixed $id
     */
    public static function has($id)
    {
        return AiOrderModel::count(['_id' => $id]) > 0;
    }

    /**
     * 同步配置
     * @return void
     */
    public static function asyncConfig()
    {
        $configs = ConfigService::getConfig('ai_tpl_id');
        $split   = CommonUtil::getSplitChar($configs);
        $configs = explode($split, $configs);

        $face_tpl_id          = '';
        $img_to_video_tpl_id  = '';
        $text_to_voice_tpl_id = '';

        foreach ($configs as $config) {
            $tplConfig = explode('=>', $config);
            switch ($tplConfig[0]) {
                case 'face_tpl_id':
                    $face_tpl_id = $tplConfig[1];
                    break;
                case 'img_to_video_tpl_id':
                    $img_to_video_tpl_id = $tplConfig[1];
                    break;
                case 'text_to_voice_tpl_id':
                    $text_to_voice_tpl_id = $tplConfig[1];
                    break;
            }
        }

        $result = self::getClient()->getConfig($face_tpl_id, $img_to_video_tpl_id, $text_to_voice_tpl_id);
        self::saveTpl($result);
    }

    /**
     *  储存订单至job
     *  因为会调用第三方接口,用异步提交
     * @param                    $userId
     * @param  array             $data
     * @return true
     * @throws BusinessException
     */
    public static function doSave($userId, array $data)
    {
        if (empty($data['type']) || empty($data['num']) || empty($data['extra'])) {
            throw  new BusinessException(StatusCode::PARAMETER_ERROR, '参数错误!');
        }

        $userRow = UserModel::findByID(intval($userId));
        UserService::checkDisabled($userRow);

        $tplId = $data['tpl_id'];
        if (kProdMode) {
            $money = $data['num'] * $data['money'];
        } else {
            // TODO 测试时,务必金额为0
            $money = 0;
        }
        if ($money > $userRow['balance']) {
            throw  new BusinessException(StatusCode::PARAMETER_ERROR, sprintf('当前可用金币%s个,数量不足!', $userRow['balance']));
        }

        $saveData = [
            'order_sn'      => CommonUtil::createOrderNo('AI'),
            'order_type'    => $data['type'],
            'user_id'       => $userId,
            'tpl_id'        => $data['tpl_id'],
            'device_type'   => $userRow['device_type'],
            'username'      => $userRow['username'],
            'channel_name'  => $userRow['channel_name'],
            'extra'         => $data['extra'],
            'out_data'      => [],
            'status'        => 0,
            'amount'        => $money,
            'real_amount'   => 0,
            'label'         => date('Y-m-d'),
            'register_ip'   => $userRow['register_ip'],
            'register_at'   => $userRow['register_at'],
            'register_date' => $userRow['register_date'],
            'created_ip'    => CommonUtil::getClientIp(),
            'is_delete'     => 0,
        ];

        // /生成订单
        $orderId = AiOrderModel::insert($saveData);
        if (empty($orderId)) {
            throw  new BusinessException(StatusCode::PARAMETER_ERROR, '购买失败!');
        }

        $remark = sprintf('%s消耗:%s金币', $data['remark'], $money);
        $result = AccountService::reduceBalance($userRow, $saveData['order_sn'], $money, 3, 'balance', $remark, json_encode(['order_id' => $orderId]));
        if (empty($result)) {
            throw  new BusinessException(StatusCode::PARAMETER_ERROR, '购买失败!');
        }
        AiOrderModel::updateRaw(['$set' => ['real_money' => $money]], ['_id' => $orderId]);

        if ($money > 0) {
            switch ($data['type']) {
                case 'change_dress':
                    JobService::create(new EventBusJob(new AIChangeDressPayload($userId, $orderId, $data['type'], $money, $userRow['balance'], $userRow['balance'] - $money)));
                    break;
                case 'change_dress_bare':
                    JobService::create(new EventBusJob(new AIChangeDressBarePayload($userId, $orderId, $data['type'], $money, $userRow['balance'], $userRow['balance'] - $money)));
                    break;
                case 'change_face_image':
                    JobService::create(new EventBusJob(new AIChangeFaceImagePayload($userId, $orderId, $data['type'], $money, $userRow['balance'], $userRow['balance'] - $money)));
                    break;
                case 'change_face_video':
                    JobService::create(new EventBusJob(new AIChangeFaceVideoPayload($userId, $orderId, $data['type'], $money, $userRow['balance'], $userRow['balance'] - $money)));
                    break;
                case 'text_to_image':
                    JobService::create(new EventBusJob(new AiTextToImagePayload($userId, $orderId, $data['type'], $money, $userRow['balance'], $userRow['balance'] - $money)));
                    break;
                case 'image_to_video':
                    JobService::create(new EventBusJob(new AiImageToVideoPayload($userId, $orderId, $data['type'], $money, $userRow['balance'], $userRow['balance'] - $money)));
                    break;
                case 'text_to_voice':
                    JobService::create(new EventBusJob(new AiTextToVoicePayload($userId, $orderId, $data['type'], $money, $userRow['balance'], $userRow['balance'] - $money)));
                    break;
                case 'novel':
                    JobService::create(new EventBusJob(new AiNovelPayload($userId, $orderId, $data['type'], $money, $userRow['balance'], $userRow['balance'] - $money)));
                    break;
            }
        }

        // 不使用jobService,因为业务代码随时会变,会导致无法加载最新代码
        return $orderId;
    }

    /**
     * 退款
     * @param       $orderRow
     * @return bool
     */
    public static function doRefund($orderRow)
    {
        if (empty($orderRow) || empty($orderRow['real_amount'])) {
            return false;
        }
        $amount  = $orderRow['real_amount'];
        $userRow = UserModel::findByID($orderRow['user_id']);
        if ($userRow) {
            $remark = sprintf('AI退款%s金币', $amount);
            AccountService::addBalance($userRow, $orderRow['order_sn'], $amount, 4, 'balance', $remark, json_encode(['order_id' => $orderRow['_id']]));
            LogUtil::error("id:{$orderRow['_id']} remark:{$remark}");
        }
        return true;
    }

    /**
     * 搜索
     * @param  array      $filter
     * @param  null|mixed $userId
     * @return array
     */
    public static function doSearch(array $filter = [], $userId = null)
    {
        $page      = $filter['page'] ?: 1;
        $pageSize  = $filter['page_size'] ?: 16;
        $keywords  = strval($filter['keywords']);
        $order     = $filter['order'] ?: '';
        $homeId    = $filter['home_id'];
        $orderType = $filter['order_type'];
        $status    = $filter['status'] ?: 2;
        $statusAll = $filter['status_all'] ? 1 : 0;  // 是否获取全部status 状况
        $ids       = strval($filter['ids']);
        $from      = ($page - 1) * $pageSize;

        $query = [];

        $sort = [];
        switch ($order) {
            case 'new':
                $sort['created_at'] = -1;
                break;
                // case 'follow': //关注数
                //     $sort['follow_total'] = -1;
                //     break;
            default:
                $sort['created_at'] = -1;
                break;
        }
        if ($keywords) {
            // $query['name'] = array('$regex' => $keywords, '$options' => 'i');
            $regex        = ['$regex' => $keywords, '$options' => 'i'];
            $query['$or'] = [
                ['name' => $regex],
                ['extra.prompt'      => $regex],
                ['extra.content'     => $regex],
                ['extra.description' => $regex],
                ['extra.story'       => $regex],
                ['extra.background'  => $regex],
                ['tpl_id' => ['$in' => value(function () use ($keywords) {
                    $tpls = AiTplModel::find([
                        'name' => ['$regex' => $keywords, '$options' => 'i']
                    ], ['_id']);
                    $tplIds = [];
                    foreach ($tpls as $tpl) {
                        $tplIds[] = $tpl['_id'];
                    }
                    return $tplIds;
                })]]
            ];
            AiKeywordsService::do($keywords);
        }
        if (!$statusAll) {
            $query['status'] = $status;
            unset($statusAll);
        }
        if (!empty($homeId)) {
            $query['user_id'] = intval($homeId);
            unset($homeId);
        }
        if (!empty($orderType)) {
            $query['order_type'] = strval($orderType);
            unset($orderType);
        }

        if (!empty($ids)) {
            $idArr = explode(',', $ids);
            foreach ($idArr as &$id) {
                $id = intval($id);
            }
            $query['_id'] = [
                '$in' => $idArr
            ];
            unset($ids, $idArr);
        }
        $query['is_delete'] = 0;

        $count = AiOrderModel::count($query);
        $items = AiOrderModel::find($query, [], $sort, $from, $pageSize);

        foreach ($items as &$item) {
            $item = [
                'id'         => strval($item['_id']),
                'order_sn'   => strval($item['order_sn']),
                'order_type' => strval($item['order_type']),
                'user'       => value(function () use ($item) {
                    $result = UserService::getInfoFromCache($item['user_id']);
                    return [
                        'id'       => strval($result['id']),
                        'username' => strval($result['username']),
                        'nickname' => strval($result['nickname']),
                        'headico'  => CommonService::getCdnUrl($result['headico']),
                    ];
                }),
                'status' => strval($item['status']),
                'amount' => value(function () use ($item, $userId) {
                    return $userId ? strval($item['amount']) : '0';
                }),

                'extra' => value(function () use ($item) {
                    $extra = $item['extra'];
                    $ext   = FileUtil::getFileExt($extra['source_path']);
                    if ($ext == 'png' || $ext == 'jpg') {
                        $extra['source_path'] = CommonService::getCdnUrl($extra['source_path']);
                    }

                    $targetExt = FileUtil::getFileExt($extra['target_path']);
                    if (in_array($targetExt, ['png', 'jpg', 'm3u8'])) {
                        $extra['target_path'] = CommonService::getCdnUrl($extra['target_path']);
                    }
                    return $extra;
                }),

                'out_data'   => self::parseOutData($item),
                'created_at' => date('Y-m-d H:i:s', $item['created_at']),
            ];
            // 添加动态实时数据
            $item = self::getRealTimeData($item, $userId);

            unset($item);
        }
        $result = [
            'data'         => $items,
            'total'        => strval($count),
            'current_page' => strval($page),
            'page_size'    => strval($pageSize),
        ];
        $result['last_page'] = strval(ceil($count / $pageSize));
        return $result;
    }

    /**
     * 添加实时数据
     * @param        $item
     * @param        $userId
     * @return array
     */
    public static function getRealTimeData($item, $userId = null)
    {
        /**
         * 如果存在虚拟数据字段 unset 掉虚拟字段，
         * 虚拟字段尽量和返回字段尽量不要一致
         */
        $itemId = $item['id'];

        // 实时获取点击、点赞、收藏数据
        $item['click'] = value(function () use ($itemId, $item) {
            $keyName = 'ai_order_click_' . $itemId;
            $real    = CommonService::getRedisCounter($keyName);
            return strval(CommonUtil::formatNum(intval($item['click'] + $real)));
        });

        $item['love'] = value(function () use ($itemId, $item) {
            $keyName = 'ai_order_love_' . $itemId;
            $real    = CommonService::getRedisCounter($keyName);
            return strval(CommonUtil::formatNum(intval($item['love'] + $real)));
        });

        $item['favorite'] = value(function () use ($itemId, $item) {
            $keyName = 'ai_order_favorite_' . $itemId;
            $real    = CommonService::getRedisCounter($keyName);
            return strval(CommonUtil::formatNum(intval($item['favorite'] + $real)));
        });
        // 实时获取用户状态
        if ($userId) {
            $item['has_love']     = AiLoveService::has($userId, $itemId) ? 'y' : 'n';
            $item['has_favorite'] = AiFavoriteService::has($userId, $itemId) ? 'y' : 'n';
        } else {
            $item['has_love']     = 'n';
            $item['has_favorite'] = 'n';
        }

        return $item;
    }

    /**
     * @param       $action
     * @param       $orderId
     * @param       $money
     * @return void
     */
    public static function handler($action, $orderId, $money = null)
    {
        switch ($action) {
            case 'click':
                CommonService::updateRedisCounter("ai_order_click_{$orderId}", 1);
                AiOrderModel::updateRaw(['$inc' => ['real_click' => 1]], ['_id' => $orderId]);
                break;
            case 'favorite':
                CommonService::updateRedisCounter("ai_order_favorite_{$orderId}", 1);
                AiOrderModel::updateRaw(['$inc' => ['real_favorite' => 1]], ['_id' => $orderId]);
                break;
            case 'unFavorite':
                CommonService::updateRedisCounter("ai_order_favorite_{$orderId}", -1);
                AiOrderModel::updateRaw(['$inc' => ['real_favorite' => -1]], ['_id' => $orderId]);
                break;
            case 'love':
                CommonService::updateRedisCounter("ai_order_love_{$orderId}", 1);
                AiOrderModel::updateRaw(['$inc' => ['real_love' => 1]], ['_id' => $orderId]);
                break;
            case 'unLove':
                CommonService::updateRedisCounter("ai_order_love_{$orderId}", -1);
                AiOrderModel::updateRaw(['$inc' => ['real_love' => -1]], ['_id' => $orderId]);
                break;
        }
    }

    /**
     * 处理响应
     * @param        $orderRow
     * @return array
     */
    public static function parseOutData($orderRow)
    {
        if ($orderRow['status'] != 2) {
            return [
                'content' => '',
                'video'   => [],
                'images'  => [],
                'zip'     => [],
            ];
        }
        $video = [];
        $zip   = [];
        $image = [];
        foreach ($orderRow['out_data']['files'] as $file) {
            $ext = FileUtil::getFileExt($file);
            if ($ext == 'm3u8') {
                $video = [
                    'img'   => CommonService::getCdnUrl($orderRow['extra']['source_path']),
                    'video' => [
                        [
                            'id'       => '',
                            'lid'      => '',
                            'code'     => 'line1',
                            'name'     => '线路1',
                            'm3u8_url' => M3u8Service::encode($file, 'tencent')
                        ],
                        [
                            'id'       => '',
                            'lid'      => '',
                            'code'     => 'line2',
                            'name'     => '线路2',
                            'm3u8_url' => M3u8Service::encode($file, 'aws')
                        ],
                        [
                            'id'       => '',
                            'lid'      => '',
                            'code'     => 'line3',
                            'name'     => '线路3',
                            'm3u8_url' => M3u8Service::encode($file, 'free')
                        ],
                    ],
                    'zip'          => '',
                    'zip_pwd'      => '',
                    'can_download' => 'n',
                ];
            } elseif ($ext == 'zip') {
                $zip = [
                    'img'          => CommonService::getCdnUrl($orderRow['extra']['source_path']),
                    'video'        => [],
                    'zip'          => '资源下载地址: ' . CommonService::getCdnUrl($file, 'video', 'free'),
                    'zip_pwd'      => $orderRow['out_data']['zip_pwd'],
                    'can_download' => 'y',
                ];
            } elseif ($ext == 'png' || $ext == 'jpg') {
                $image[] = [
                    'img'          => CommonService::getCdnUrl($file),
                    'video'        => [],
                    'zip'          => '',
                    'zip_pwd'      => '',
                    'can_download' => 'y',
                ];
            }
        }
        $data = [
            'content' => $orderRow['out_data']['text'] ?? '',
            'video'   => $video,
            'images'  => $image,
            'zip'     => $zip,
        ];
        return $data;
    }

    /**
     * @return MediaLSJAiService
     */
    public static function getClient()
    {
        $mediaUrl   = ConfigService::getConfig('media_api');
        $mediaAppid = ConfigService::getConfig('media_appid');
        $mediaKey   = ConfigService::getConfig('media_key');
        return new MediaLSJAiService($mediaUrl, $mediaKey, $mediaAppid);
    }

    /**
     * 刷新模板
     * @param       $result
     * @return void
     */
    private static function saveTpl($result)
    {
        // 'novel' => '小说',
        // 'change_face' => '换脸',
        // 'change_dress' => '换装',
        // 'change_dress_bare' => '去衣',
        // 'text_to_image' => '文生图片',
        // 'image_to_video' => '图生视频',
        // 'text_to_voice' => '文生语音',

        // 视频换脸模板
        $change_face_video = $result['face_videos_tpl'];
        // 图生视频
        $image_to_video = $result['image_to_video_tpl'];
        // 文生语音
        $text_to_voice = $result['text_to_voice_tpl'];
        // 文生图片
        $text_to_image = $result['text_to_image_generate_models'];
        // 文生图片允许尺寸
        $text_to_image_size = $result['text_to_image_generate_size'];

        // 小说模式
        $novel = $result['novel_method'];
        // 换装模式
        $change_dress = $result['change_method'];
        // 去衣模式
        $change_dress_bare = $result['undress_method'];

        /**
         * "id": "01",
         * "img": "/md-204/common/78/7831965eaa21422651732258fc878c9e.jpg",
         * "m3u8_url": "/md-204/m3u8-download/718/71843ecd7b629dc0e434df50cb26553b/121a24-m.m3u8"
         */
        foreach ($change_face_video as $item) {
            $_id = "change_face_video_{$item['id']}";
            AiTplModel::findAndModify([
                '_id' => $_id,
            ], [
                '$set' => [
                    'config' => [
                        'code'     => $item['id'],
                        'img'      => $item['img'] ?? '',
                        'm3u8_url' => $item['m3u8_url'],
                    ],
                    'updated_at' => time(),
                ],
                '$setOnInsert' => [
                    'name'        => $item['name'] ?? $item['id'],
                    'description' => $item['tips'] ?? '',
                    'type'        => 'change_face_video',
                    'tags'        => [],
                    'money'       => 0,
                    'adult'       => 1,
                    'img'         => $item['img'] ?? '',
                    'sort'        => 0,
                    'is_disabled' => 0,
                    'created_at'  => time(),
                ]
            ], [], true);
        }

        /**
         * "code": "tittysuck",
         * "name": "xx",
         * "tips": "图片需上传漏奶照片，不要遮挡胸部，近景照，画面里只有一个人",
         * "img_num": "1",
         * "img": "/md-204/common/65/65722d943a6e864c4cddfd1032e7574f.jpg",
         * "m3u8_url": "/md-204/m3u8-download/abc/abc8e8cf9fb1e1853814c4fefcc1ee05/5a8ee9-m.m3u8"
         */
        foreach ($image_to_video as $item) {
            $_id = "image_to_video_{$item['code']}";
            AiTplModel::findAndModify([
                '_id' => $_id,
            ], [
                '$set' => [
                    'config' => [
                        'code'     => $item['code'],
                        'm3u8_url' => $item['m3u8_url'],
                        'img_num'  => $item['img_num'],
                    ],
                    'updated_at' => time(),
                ],
                '$setOnInsert' => [
                    'name'        => $item['name'] ?? $item['id'],
                    'description' => $item['tips'] ?? '',
                    'type'        => 'image_to_video',
                    'tags'        => [],
                    'money'       => 0,
                    'adult'       => 1,
                    'img'         => $item['img'] ?? '',
                    'sort'        => 0,
                    'is_disabled' => 0,
                    'created_at'  => time(),
                ]
            ], [], true);
        }

        /**
         * "id": "Angelababy",
         * "img": "/md-204/common/dd/dd11d0491e6d3767c168d3534daee773.jpg",
         * "m3u8_url": "/md-204/m3u8-download/b91/b914afedbead35ae6182cba0617eed41/9f24ec-m.m3u8"
         */
        foreach ($text_to_voice as $item) {
            $_id = "text_to_voice_{$item['id']}";
            AiTplModel::findAndModify([
                '_id' => $_id,
            ], [
                '$set' => [
                    'config' => [
                        'code'     => $item['id'],
                        'm3u8_url' => $item['m3u8_url'],
                    ],
                    'updated_at' => time(),
                ],
                '$setOnInsert' => [
                    'name'        => $item['name'] ?? $item['id'],
                    'description' => $item['tips'] ?? '',
                    'type'        => 'text_to_voice',
                    'tags'        => [],
                    'money'       => 0,
                    'adult'       => 1,
                    'img'         => $item['img'] ?? '',
                    'sort'        => 0,
                    'is_disabled' => 0,
                    'created_at'  => time(),
                ]
            ], [], true);
        }

        /**
         * "name": "真人风1",
         * "is_pron": "y",
         * "type": "real_person",
         * "is_hot": "y",
         * "id": "1"
         */
        foreach ($text_to_image as $item) {
            $_id = "text_to_image_{$item['id']}";
            AiTplModel::findAndModify([
                '_id' => $_id,
            ], [
                '$set' => [
                    'config' => [
                        'code' => $item['id'],
                        'type' => $item['type'],
                        'size' => $text_to_image_size,
                    ],
                    'updated_at' => time(),
                ],
                '$setOnInsert' => [
                    'name'        => $item['name'] ?? $item['id'],
                    'description' => $item['tips'] ?? '',
                    'type'        => 'text_to_image',
                    'tags'        => [],
                    'money'       => 0,
                    'adult'       => $item['is_pron'] == 'y' ? 1 : 0,
                    'img'         => $item['img'] ?? '',
                    'sort'        => 0,
                    'is_disabled' => 0,
                    'created_at'  => time(),
                ]
            ], [], true);
        }

        /**
         * "id": "method_1",
         * "name": "小二"
         */
        foreach ($novel as $item) {
            $_id = "novel_{$item['id']}";
            AiTplModel::findAndModify([
                '_id' => $_id,
            ], [
                '$set' => [
                    'config' => [
                        'code' => $item['id'],
                    ],
                    'updated_at' => time(),
                ],
                '$setOnInsert' => [
                    'name'        => $item['name'] ?? $item['id'],
                    'description' => $item['tips'] ?? '',
                    'type'        => 'novel',
                    'tags'        => [],
                    'money'       => 0,
                    'adult'       => $item['is_pron'] == 'y' ? 1 : 0,
                    'img'         => $item['img'] ?? '',
                    'sort'        => 0,
                    'is_disabled' => 0,
                    'created_at'  => time(),
                ]
            ], [], true);
        }

        /**
         * "id": "bikini",
         * "name": "比基尼"
         */
        foreach ($change_dress as $item) {
            $_id = "change_dress_{$item['id']}";
            AiTplModel::findAndModify([
                '_id' => $_id,
            ], [
                '$set' => [
                    'config' => [
                        'code' => $item['id'],
                    ],
                    'updated_at' => time(),
                ],
                '$setOnInsert' => [
                    'name'        => $item['name'] ?? $item['id'],
                    'description' => $item['tips'] ?? '',
                    'type'        => 'change_dress',
                    'tags'        => [],
                    'money'       => 0,
                    'adult'       => $item['is_pron'] == 'y' ? 1 : 0,
                    'img'         => $item['img'] ?? '',
                    'sort'        => 0,
                    'is_disabled' => 0,
                    'created_at'  => time(),
                ]
            ], [], true);
        }

        /**
         * "id": "method_1",
         * "name": "模式一"
         */
        foreach ($change_dress_bare as $item) {
            $_id = "change_dress_bare_{$item['id']}";
            AiTplModel::findAndModify([
                '_id' => $_id,
            ], [
                '$set' => [
                    'config' => [
                        'code' => $item['id'],
                    ],
                    'updated_at' => time(),
                ],
                '$setOnInsert' => [
                    'name'        => $item['name'] ?? $item['id'],
                    'description' => $item['tips'] ?? '',
                    'type'        => 'change_dress_bare',
                    'tags'        => [],
                    'money'       => 0,
                    'adult'       => $item['is_pron'] == 'y' ? 1 : 0,
                    'img'         => $item['img'] ?? '',
                    'sort'        => 0,
                    'is_disabled' => 0,
                    'created_at'  => time(),
                ]
            ], [], true);
        }
        // 其他配置写入config
        ConfigModel::update(['value' => $result['face_status']], ['code' => 'ai_change_face_status']);
        ConfigModel::update(['value' => $result['image_to_video_status']], ['code' => 'ai_image_to_video_status']);
        ConfigModel::update(['value' => $result['text_to_voice_status']], ['code' => 'ai_text_to_voice_status']);

        LogUtil::info(__CLASS__ . ' AI模板刷新成功');
    }
}
