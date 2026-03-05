<?php

namespace App\Jobs\Es;

use App\Jobs\BaseJob;
use App\Models\Comics\ComicsTagModel;
use App\Models\Movie\MovieTagModel;
use App\Models\User\UserUpModel;
use App\Utils\CommonUtil;
use App\Utils\LogUtil;

/**
 * ES-词库
 */
class EsIkJob extends BaseJob
{
    public static $savePath = APP_PATH . '/Resource/dic';
    public static $esPath   = '/usr/local/elasticsearch/config/analysis-ik';

    public function handler($_id)
    {
        $this->up();
        $this->tag();
    }

    public function up()
    {
        $where     = [];
        $count     = UserUpModel::count($where);
        $pageSize  = 1000;
        $totalPage = ceil($count / $pageSize);

        $rows = [];
        for ($page = 1; $page <= $totalPage; $page++) {
            $skip  = ($page - 1) * $pageSize;
            $items = UserUpModel::find($where, ['_id', 'nickname'], ['_id' => -1], $skip, $pageSize);
            foreach ($items as $item) {
                $nicknames = explode(',', $item['nickname']);
                foreach ($nicknames as $nickname) {
                    if (strpos($nickname, 'jpg') !== false || strpos($nickname, 'png')) {
                        continue;
                    }
                    if (mb_strlen($nickname) >= 8) {
                        continue;
                    }
                    $rows[] = $this->parse($nickname, 'nr', 900);
                }
            }
        }
        $this->save('up', $rows);
    }

    /**
     * @param  string $str  词语
     * @param  string $pos  词性
     * @param  int    $freq 词频
     * @return string
     */
    public function parse(string $str, string $pos, int $freq)
    {
        /**
         * 词性标签    说明    示例词
         * n    名词    人，汽车
         * nr    人名    张三，李四
         * ns    地名    北京，上海
         * nt    机构团体名    中科院，微软
         * nz    其他专名    阿里巴巴，微信
         * v    动词    跑，跳
         * vd    副动词    慢慢走
         * vn    名动词（名词性动词）    喜欢，爱
         * a    形容词    漂亮，高兴
         * ad    副形词    很漂亮
         * an    名形词    美丽的花
         * d    副词    很，非常
         * m    数词    一，三百
         * q    量词    个，件
         * r    代词    我，你
         * p    介词    在，对
         * c    连词    和，但是
         * u    助词    的，了
         * e    叹词    啊，哎
         * o    拟声词    咚咚，哗啦
         * h    前缀    超级
         * k    后缀    化，性
         * x    非语素字    ￥，#
         */
        // ik 插件
        return $str;
        // ansj插件 : 词典各字段之间使用tab(\t)分割，而不是空格。
        return "$str\t$pos\t$freq";
    }

    /**
     * @param       $type
     * @param       $items
     * @return void
     */
    public function save($type, $items)
    {
        $commons = file_get_contents(APP_PATH . "/Resource/dic/common_{$type}.dic");
        $commons = !empty($commons) ? value(function () use ($commons) {
            $split = CommonUtil::getSplitChar($commons ?: '');
            return explode($split, $commons);
        }) : [];
        $items = array_merge($commons, $items);
        $items = array_unique($items);
        foreach ($items as $index => $item) {
            if (empty($item)) {
                unset($items[$index]);
            }
            $items[$index] = trim($item);
        }
        sort($items);
        file_put_contents(self::$savePath . "/{$type}.dic", join("\r\n", $items));
        //        file_put_contents(self::$esPath . "/{$type}.dic", join("\r\n", $items));
        LogUtil::info(self::$savePath . "/{$type}.dic");
        //        LogUtil::info(self::$esPath . "/{$type}.dic");
    }

    public function tag()
    {
        $where = [];
        $rows  = [];

        $items = MovieTagModel::find($where, ['_id', 'name'], ['_id' => -1], 0, 1000);
        foreach ($items as $item) {
            if (mb_strlen($item['name']) >= 8) {
                continue;
            }
            $rows[] = $this->parse($item['name'], 'nr', 900);
        }

        $items = ComicsTagModel::find($where, ['_id', 'name'], ['_id' => -1], 0, 1000);
        foreach ($items as $item) {
            if (mb_strlen($item['name']) >= 8) {
                continue;
            }
            $rows[] = $this->parse($item['name'], 'nr', 900);
        }
        $this->save('tag', $rows);
    }

    public function success($_id)
    {
        $config = <<<'EOF'
<?xml version="1.0" encoding="UTF-8"?>
<!DOCTYPE properties SYSTEM "http://java.sun.com/dtd/properties.dtd">
<properties>
        <comment>IK Analyzer 扩展配置</comment>
        <!--用户可以在这里配置自己的扩展字典 -->
        <entry key="ext_dict"></entry>
         <!--用户可以在这里配置自己的扩展停止词字典-->
        <entry key="ext_stopwords"></entry>
        <!--用户可以在这里配置远程扩展字典 -->
         <entry key="remote_ext_dict">http://127.0.0.1:8088/api/server/ik</entry>
        <!--用户可以在这里配置远程扩展停止词字典-->
        <!-- <entry key="remote_ext_stopwords">words_location</entry> -->
</properties>
EOF;
        //        改用接口词库,实现动态更新
        //    file_put_contents(self::$esPath.'/IKAnalyzer.cfg.xml', $config);
    }

    public function error($_id, \Exception $e)
    {
    }
}
