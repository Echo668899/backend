<?php

namespace App\Jobs\Center;

use App\Models\Common\AdvAppModel;
use App\Models\Common\AdvModel;
use App\Models\Common\AdvPosModel;
use Phalcon\Manager\Center\CenterAdvService;

/**
 * 广告中心
 */
class CenterAdvJob extends CenterBaseJob
{

    /**
     * @var CenterAdvService
     */
    public $centerService;
    public $action;

    public function __construct($action)
    {
        $this->action = $action;
        $configs = self::getCenterConfig('adv');
        $this->centerService =new CenterAdvService(
            $configs['pull_url'],
            $configs['push_url'],
            $configs['merid'],
            $configs['deptid'],
            $configs['appid'],
            $configs['appkey'],
        );
    }

    public function handler($_id)
    {
        switch ($this->action) {
            case 'sync':
                $this->sync();
                break;
            /*
             * 一般用不到,没事别瞎调,广告中心没做去重
             * case 'reportAdv':
                $this->reportAdv();
                break;
            case 'reportAdvApp':
                $this->reportAdvApp();
                break;
            case 'reportAdvPos':
                $this->reportAdvPos();
                break;*/
        }
    }

    /**
     * 同步广告
     * @return void
     * @throws \Exception
     */
    public function sync()
    {
        //TODO 如果项目有inner://这种协议,需要改成true
        $result = $this->centerService->getAll(false);
        $advArr = $result['adv'];
        $advAppArr = $result['adv_app'];
        $advPosArr = $result['adv_pos'];

        //cid=中心id
        /**====================清空历史广告start=======================**/
        if(count($advPosArr)>0){
            ///处理广告中心修改广告位问题
            ///因为业务侧分adv和adv_app,而广告中心是没区分的,如果adv填到adv_app,再改回,则业务端没删除adv_app中数据
            $cidArr=array_column($advPosArr,'cid');
            AdvPosModel::delete(['code'=>['$nin'=>$cidArr]]);
        }

        if(count($advArr)>0){
            ///处理广告中心修改广告位问题
            ///因为业务侧分adv和adv_app,而广告中心是没区分的,如果adv填到adv_app,再改回,则业务端没删除adv_app中数据
            $cidArr=array_column($advArr,'cid');
            AdvModel::delete(['_id'=>['$nin'=>$cidArr]]);
        }

        if(count($advAppArr)>0){
            ///处理广告中心修改广告位问题
            ///因为业务侧分adv和adv_app,而广告中心是没区分的,如果adv填到adv_app,再改回,则业务端没删除adv_app中数据
            $cidArr=array_column($advAppArr,'cid');
            AdvAppModel::delete(['_id'=>['$nin'=>$cidArr]]);
        }
        /**====================清空历史广告end=======================**/

        foreach ($advPosArr as $item) {
            $hasRow = AdvPosModel::findFirst(['code'=>$item['cid']]);
            if(empty($hasRow)){
                AdvPosModel::insert([
                    'code'=>strval($item['cid']),
                    'name'=>strval($item['name']),
                    'is_disabled'=>intval($item['is_disabled']),
                    'width'=>intval($item['width']),
                    'height'=>intval($item['height']),
                ]);
            }else{
                $update=[];
                //是否需要更新
                if($hasRow['name']!=$item['name']){
                    $update['name']=strval($item['name']);
                }
                if($hasRow['is_disabled']!=$item['is_disabled']){
                    $update['is_disabled']=intval($item['is_disabled']);
                }
                if($hasRow['width']!=$item['width']){
                    $update['width']=intval($item['width']);
                }
                if($hasRow['height']!=$item['height']){
                    $update['height']=intval($item['height']);
                }
                if(!empty($update)){
                    AdvPosModel::update($update,['_id'=>$hasRow['_id']]);
                }
            }

        }

        foreach ($advArr as $item) {
            $hasRow = AdvModel::findFirst(['_id'=>$item['cid']]);
            if(empty($hasRow)){
                AdvModel::insert([
                    '_id'=>strval($item['cid']),
                    'name'=>strval($item['name']),
                    'description'=>strval($item['description']),
                    'position_code'=>strval($item['position_code']),
                    'type'=>strval($item['type']),
                    'right'=>strval($item['right']),
                    'channel_code'=>'',
                    'content'=>strval($item['content']),
                    'start_time'=>intval($item['start_time']),
                    'end_time'=>intval($item['end_time']),
                    'show_time'=>intval($item['show_time']),
                    'sort'=>intval($item['sort']),
                    'click'=>intval(0),
                    'link'=>strval($item['link']),
                    'is_disabled'=>intval($item['is_disabled']),
                ]);
            }else{
                $update=[];
                //是否需要更新
                if($hasRow['name']!=$item['name']){
                    $update['name']=strval($item['name']);
                }
                if($hasRow['description']!=$item['description']){
                    $update['description']=strval($item['description']);
                }
                if($hasRow['position_code']!=$item['position_code']){
                    $update['position_code']=strval($item['position_code']);
                }
                if($hasRow['type']!=$item['type']){
                    $update['type']=strval($item['type']);
                }
                if($hasRow['content']!=$item['content']){
                    $update['content']=strval($item['content']);
                }
                if($hasRow['start_time']!=$item['start_time']){
                    $update['start_time']=intval($item['start_time']);
                }
                if($hasRow['end_time']!=$item['end_time']){
                    $update['end_time']=intval($item['end_time']);
                }
                if($hasRow['show_time']!=$item['show_time']){
                    $update['show_time']=intval($item['show_time']);
                }
                if($hasRow['sort']!=$item['sort']){
                    $update['sort']=intval($item['sort']);
                }
                if($hasRow['link']!=$item['link']){
                    $update['link']=strval($item['link']);
                }
                if($hasRow['is_disabled']!=$item['is_disabled']){
                    $update['is_disabled']=intval($item['is_disabled']);
                }
                if(!empty($update)){
                    AdvModel::update($update,['_id'=>$hasRow['_id']]);
                }
            }
        }

        foreach ($advAppArr as $item) {
            $hasRow = AdvAppModel::findFirst(['_id'=>$item['cid']]);
            if(empty($hasRow)){
                AdvAppModel::insert([
                    '_id'=>strval($item['cid']),
                    'name'=>strval($item['name']),
                    'position'=>[strval($item['position'])],
                    'image'=>strval($item['image']),
                    'download_url'=>strval($item['download_url']),
                    'download'=>strval($item['download']),
                    'description'=>strval($item['description']),
                    'sort'=>intval($item['sort']),
                    'is_hot'=>intval($item['is_hot']),
//                    'is_self'=>intval($item['is_self']),//需要就打开
                    'is_disabled'=>intval($item['is_disabled']),
                ]);
            }else{
                $update=[];
                //是否需要更新
                if($hasRow['name']!=$item['name']){
                    $update['name']=$item['name'];
                }

                if(is_array($hasRow['position'])){
                    if(in_array($item['position'],$hasRow['position'])==false){
                        $update['position']=[strval($item['position'])];
                    }
                }else{
                    if($item['position']!=$hasRow['position']){
                        $update['position']=strval($item['position']);
                    }
                }

                if($hasRow['image']!=$item['image']){
                    $update['image']=strval($item['image']);
                }
                if($hasRow['download_url']!=$item['download_url']){
                    $update['download_url']=strval($item['download_url']);
                }
                if($hasRow['description']!=$item['description']){
                    $update['description']=strval($item['description']);
                }
                if($hasRow['sort']!=$item['sort']){
                    $update['sort']=intval($item['sort']);
                }
                if($hasRow['is_hot']!=$item['is_hot']){
                    $update['is_hot']=intval($item['is_hot']);
                }
                if($hasRow['is_disabled']!=$item['is_disabled']){
                    $update['is_disabled']=intval($item['is_disabled']);
                }
                if(!empty($update)){
                    AdvAppModel::update($update,['_id'=>$hasRow['_id']]);
                }
            }
        }
    }

    /**
     * 上报广告
     * @return void
     * @throws \Exception
     */
    public function reportAdv()
    {
        $advPosArr = AdvPosModel::find([],[],[],0,1000);
        $advArr = AdvModel::find([],[],[],0,1000);
        $this->centerService->pushAdv($advArr,$advPosArr);
    }

    /**
     * 上报应用广告
     * @return void
     * @throws \Exception
     */
    public function reportAdvApp()
    {
        $advArr = AdvAppModel::find([],[],[],0,1000);
        $this->centerService->pushAdvApp($advArr);
    }

    /**
     * 上报广告位
     * @return void
     * @throws \Exception
     */
    public function reportAdvPos()
    {
        $advPosArr = AdvPosModel::find([],[],[],0,1000);
        $this->centerService->pushAdvPos($advPosArr);
    }



    public function success($_id)
    {

    }

    public function error($_id, \Exception $e)
    {

    }
}
