<?php

namespace App\Jobs\Common;

use App\Jobs\BaseJob;
use App\Models\Ai\AiOrderModel;
use App\Services\Ai\AiService;
use App\Services\Common\CommonService;
use App\Services\Common\ConfigService;
use App\Utils\LogUtil;
use Phalcon\Manager\MediaLSJService;

/**
 * AI订单处理
 */
class AiOrderJob extends BaseJob
{
    public function handler($_id)
    {
        $this->config();
        $this->run();
    }

    public function config()
    {
        AiService::asyncConfig();
    }

    public function run()
    {
        $runTime   = 297;// 可执行时间/秒
        $startTime = time();
        while (true) {
            if (time() - $startTime >= $runTime) {
                break;
            }
            $rows = AiOrderModel::find(['status' => ['$in' => [0, 1]]], [], ['_id' => -1], 0, 1000);
            foreach ($rows as $row) {
                try {
                    if ($row['status'] == 0) {
                        $this->send($row);
                    } else {
                        $this->query($row);
                    }
                } catch (\Exception $e) {
                    AiOrderModel::updateById([
                        'status'    => -2,
                        'error_msg' => $e->getMessage()
                    ], $row['_id']);
                    LogUtil::error("id:{$row['_id']} order_sn:{$row['order_sn']} error:" . $e->getMessage());
                }
            }
            sleep(10);
        }
    }

    public function success($_id)
    {
    }

    public function error($_id, \Exception $e)
    {
    }

    private function send($order)
    {
        $extra = $order['extra'];
        switch ($order['order_type']) {
            case 'novel':
                $result = AiService::getClient()->doNovel(
                    strval($order['order_sn']),
                    strval($extra['method']),
                    strval($extra['description']),
                    strval($order['order_sn']),
                    strval($order['real_money']),
                    strval($extra['background']),
                    strval($extra['scene'] ? $extra['scene'] . ' ' . $extra['story'] : $extra['story'])
                );
                break;
            case 'change_face_image':
                $result = AiService::getClient()->doChangeFace(
                    strval($order['order_sn']),
                    strval(CommonService::getCdnUrl($extra['source_path'], 'image', 'free')),
                    value(function () use ($extra) {
                        if (strpos($extra['target_path'], '.m3u8') !== false) {
                            return $extra['target_path'];
                        }
                        return CommonService::getCdnUrl($extra['target_path'], 'image', 'free');
                    }),
                    strval($order['order_sn']),
                    strval($order['real_money'])
                );
                break;
            case 'change_face_video':
                if (empty($order['tpl_id'])) {
                    $mediaUrl    = ConfigService::getConfig('upload_url');
                    $mediaKey    = ConfigService::getConfig('upload_key');
                    $videoResult = MediaLSJService::findVideoInfo($mediaUrl, $extra['target_path'], $mediaKey);
                    $targetPath  = $videoResult['file_m3u8'];
                } else {
                    $targetPath = $order['extra']['target_path'];
                }

                if (!empty($targetPath)) {
                    $result = AiService::getClient()->doChangeFace(
                        strval($order['order_sn']),
                        strval(CommonService::getCdnUrl($extra['source_path'], 'image', 'free')),
                        strval($targetPath),
                        strval($order['order_sn']),
                        strval($order['real_money'])
                    );
                }
                break;
            case 'change_dress':
                $result = AiService::getClient()->doChangeDress(
                    strval($order['order_sn']),
                    strval($extra['method']),
                    strval(CommonService::getCdnUrl($extra['source_path'], 'image', 'free')),
                    strval($order['order_sn']),
                    strval($order['real_money'])
                );
                break;
            case 'change_dress_bare':
                $result = AiService::getClient()->doChangeDressBare(
                    strval($order['order_sn']),
                    strval($extra['method']),
                    strval(CommonService::getCdnUrl($extra['source_path'], 'image', 'free')),
                    strval($order['order_sn']),
                    strval($order['real_money'])
                );
                break;
            case 'text_to_image':
                $result = AiService::getClient()->doTextToImage(
                    strval($order['order_sn']),
                    strval($extra['method']),
                    strval($extra['prompt']),
                    strval($extra['batch_count']),
                    strval($extra['batch_size']),
                    strval($order['order_sn']),
                    strval($order['real_money']),
                    strval($extra['size']),
                    strval(CommonService::getCdnUrl($extra['source_path'], 'image', 'free'))
                );
                break;
            case 'image_to_video':
                $result = AiService::getClient()->doImageToVideo(
                    strval($order['order_sn']),
                    strval(CommonService::getCdnUrl($extra['source_path'], 'image', 'free')),
                    strval($extra['method']),
                    strval($order['order_sn']),
                    strval($order['real_money'])
                );
                break;
            case 'text_to_voice':
                if (empty($order['tpl_id'])) {
                    $mediaUrl    = ConfigService::getConfig('upload_url');
                    $mediaKey    = ConfigService::getConfig('upload_key');
                    $videoResult = MediaLSJService::findVideoInfo($mediaUrl, $extra['source_path'], $mediaKey);
                    $sourcePath  = $videoResult['file_m3u8'];
                } else {
                    $sourcePath = $order['extra']['source_path'];
                }

                $result = AiService::getClient()->doTextToVoice(
                    strval($order['order_sn']),
                    strval($extra['method']),
                    strval($extra['content']),
                    strval(CommonService::getCdnUrl($sourcePath, 'video', 'free')),
                    strval($order['order_sn']),
                    strval($order['real_money'])
                );
                break;
        }
        // 更新订单状态和任务ID
        if ($result && $result['task_id']) {
            AiOrderModel::updateById([
                'task_id' => $result['task_id'],
                'status'  => 1,
            ], $order['_id']);
        }
    }

    /**
     * 查询任务
     * @param                             $order
     * @param  mixed                      $orderRow
     * @return void
     * @throws \Phalcon\Storage\Exception
     */
    private function query($orderRow)
    {
        $result = AiService::getClient()->doQueryRequest(
            strval($orderRow['order_sn']),
            strval($orderRow['task_id'])
        );
        if (intval($result['status']) == -1) {
            AiOrderModel::updateById([
                'status'    => -1,
                'error_msg' => $result['error'],
            ], $orderRow['_id']);
            AiService::doRefund($orderRow);
        } elseif (intval($result['status']) == 2) {
            AiOrderModel::updateById([
                'status'   => 2,
                'out_data' => $result['out_data'],
            ], $orderRow['_id']);
        }
    }
}
