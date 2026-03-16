<?php

namespace App\Jobs\Common;

use App\Jobs\BaseJob;
use App\Models\Common\ChatMessageModel;
use App\Models\Common\ChatModel;
use App\Models\Common\ChatReadModel;
use App\Models\Movie\MovieModel;
use App\Models\Post\PostModel;
use App\Services\Common\Chat\MessageType\ChatMessage\VideoHandler;
use App\Services\Common\CommonService;
use App\Services\Common\ConfigService;
use App\Services\Common\M3u8Service;
use App\Services\Im\Entity\ImMessageType;
use App\Services\Im\Entity\ImPayloadMessage;
use App\Services\Im\ImService;
use App\Services\Im\Payload\ChatMessageData;
use App\Services\Movie\MovieService;
use App\Services\Post\PostService;
use App\Utils\LogUtil;
use Phalcon\Manager\MediaLSJService;
use Phalcon\Manager\MediaTangXinService;

/**
 * 上传查询
 */
class UploadFindJob extends BaseJob
{
    public function handler($_id)
    {
        try {
            $this->post();
        }catch (\Exception $e){
            LogUtil::error(sprintf('%s in %s line %s', $e->getMessage(), $e->getFile(), $e->getLine()));
        }
        try {
            $this->movie();
        }catch (\Exception $e){
            LogUtil::error(sprintf('%s in %s line %s', $e->getMessage(), $e->getFile(), $e->getLine()));
        }

        try {
            $this->chat();
        }catch (\Exception $e){
            LogUtil::error(sprintf('%s in %s line %s', $e->getMessage(), $e->getFile(), $e->getLine()));
        }

    }

    public function post()
    {
        $where = ['status' => -1];
        $pageSize = 1000;
        $lastId = null;
        while (true) {
            if ($lastId !== null) {
                $where['_id'] = ['$lt' => $lastId];
            }
            $items = PostModel::find($where, ['_id','videos'], ['_id' => -1], 0, $pageSize);
            if (empty($items)) {
                break;
            }
            foreach ($items as $item) {
                if($item['videos']){
                    $update=false;
                    $success=0;
                    foreach ($item['videos'] as &$video) {
                        if(!empty($video['video']['url'])){
                            $success++;
                            continue;
                        }
                        $info = $this->findVideoInfoLSJ(str_replace('upload_','',$video['upload_id']));
                        if(!empty($info)){
                            $video['img'] = $info['img'];
                            $video['url'] = $info['m3u8_url'];
                            $success++;
                            $update=true;
                        }
                        unset($video);
                    }
                    if($update){
                        PostModel::updateRaw(
                            [
                                '$set'=>[
                                    'status'=>$success==count($item['videos'])?0:-1,
                                    'videos'=>$item['videos'],
                                ]
                            ],
                            [
                                '_id' => $item['_id']
                            ]
                        );
                        PostService::asyncEs($item['_id']);
                    }
                }else{
                    //没有视频,直接更新状态
                    PostModel::updateRaw(
                        [
                            '$set'=>[
                                'status'=>0,
                            ]
                        ],
                        [
                            '_id' => $item['_id']
                        ]
                    );
                    PostService::asyncEs($item['_id']);
                }
                $lastId = $item['_id'];
            }
        }
    }

    public function movie()
    {
        $where = ['status' => -2];
        $pageSize = 1000;
        $lastId = null;
        while (true) {
            if ($lastId !== null) {
                $where['_id'] = ['$lt' => $lastId];
            }
            $items = MovieModel::find($where, ['_id','mid'], ['_id' => -1], 0, $pageSize);
            if (empty($items)) {
                break;
            }
            foreach ($items as $item) {
                $info = $this->findVideoInfoLSJ(str_replace('upload_','',$item['mid']));
                if(!empty($info)){
                    MovieModel::updateRaw(
                        [
                            '$set'=>[
                                'status'=>2,
                                'width'     =>intval($info['width']),
                                'height'    =>intval($info['height']),
                                'canvas'    => $info['width']>$info['height'] ? 'long':'short',
                                "links.0.m3u8_url"=>strval($info['m3u8_url']),
                                "links.0.preview_m3u8_url"=>strval($info['preview_m3u8_url']),
                            ]
                        ],
                        [
                            '_id' => $item['_id']
                        ]
                    );
                    MovieService::asyncEs($item['_id']);
                }
                $lastId = $item['_id'];
            }
        }
    }

    public function chat()
    {
        $where = ['msg_type' => 'video'];
        $pageSize = 1000;
        $lastId = null;
        while (true) {
            if ($lastId !== null) {
                $where['_id'] = ['$lt' => $lastId];
            }
            $items = ChatMessageModel::find($where, [], ['_id' => -1], 0, $pageSize);
            if (empty($items)) {
                break;
            }
            foreach ($items as $item) {
                $info = $this->findVideoInfoLSJ(str_replace('upload_','',$item['msg_body']['video_id']));
                if(!empty($info)){
                    $item['msg_body'] = [
                        'url'=>strval($info['m3u8_url']),
                        'img'=>strval($info['img']),
                        'width'=>strval($info['width']),
                        'height'=>strval($info['height']),
                        'duration'=>strval($info['duration']),
                    ];
                    ChatMessageModel::updateRaw(
                        [
                            '$set'=>[
                                'msg_body'=>$item['msg_body']
                            ],
                        ],
                        [
                            '_id' => $item['_id']
                        ]
                    );

                    $chatToId = $item['chat_id'].'_'.$item['to_id'];
                    $toChat = ChatModel::findByID($chatToId);
                    $toChatRead = ChatReadModel::findByID($chatToId);
                    $toChatReadSeqid = $toChatRead ? $toChatRead['last_read_seqid'] : 0;

                    $item['msg_body']['url']=M3u8Service::encode($item['msg_body']['url'],CommonService::getCdnDrive('video'));

                    ///发给接收方
                    ImService::sendToUser(ImService::ACTION_SEND_TO_USER, $item['from_id'], $item['to_id'], $item['_id'], new ImPayloadMessage(
                        ImMessageType::CHAT_MESSAGE,
                        new ChatMessageData(
                            $item['chat_id'],
                            $item['from_id'],
                            $item['to_id'],
                            $item['_id'],
                            $item['seqid'],
                            $item['msg_type'],
                            $item['msg_body'],
                            VideoHandler::getPreview($item['msg_body']),
                            $toChatRead['last_read_id'],
                            $toChatReadSeqid,
                            $toChat['unread_count'],
                            time()
                        ),
                    ));
                }
                $lastId = $item['_id'];
            }
        }
    }

    /**
     * 老司机库
     * @param $uploadId
     * @return array|null
     */
    protected function findVideoInfoLSJ($uploadId)
    {
        $mediaUrl = ConfigService::getConfig('upload_url');
        $mediaKey = ConfigService::getConfig('upload_key');
        $videoResult = MediaLSJService::findVideoInfo($mediaUrl,$uploadId,$mediaKey);
        if($videoResult&&$videoResult['file_status']==2){
            $size = explode('X',$videoResult['file_quality']);
            return [
                'id'=>strval($uploadId),
                'm3u8_url'=>strval($videoResult['file_m3u8']),
                'preview_m3u8_url'=>strval($videoResult['file_preview_m3u8']),
                'duration'=>strval($videoResult['file_duration']),
                'width'=>strval($size[0]),
                'height'=>strval($size[1]),
                'img'=>'',///老司机库没有预览
            ];
        }
        return null;
    }

    /**
     * 糖心库
     * @param $uploadId
     * @return array|void
     */
    protected function findVideoInfoTx($uploadId)
    {
        $mediaUrl = ConfigService::getConfig('tangxin_media_api');
        $mediaKey = ConfigService::getConfig('tangxin_media_key');
        $videoResult = MediaTangXinService::findVideoInfo($mediaUrl,$uploadId,$mediaKey);
        if($videoResult&&$videoResult['status']==1){
            return [
                'id'=>strval($uploadId),
                'm3u8_url'=>strval($videoResult['original_link']),
                'preview_m3u8_url'=>strval($videoResult['preview_link']),
                'duration'=>strval($videoResult['original_duration']),
                'width'=>strval($videoResult['width']),
                'height'=>strval($videoResult['height']),
                'img'=>strval($videoResult['img_x']),
            ];
        }
    }

    public function success($_id)
    {

    }

    public function error($_id, \Exception $e)
    {

    }

}
