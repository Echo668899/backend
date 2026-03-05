<?php

declare(strict_types=1);

namespace App\Services\Common;

use App\Core\Services\BaseService;
use App\Utils\CommonUtil;
use App\Utils\LogUtil;

/**
 * es操作类
 * Class ElasticService
 * @package App\Services
 */
class ElasticService extends BaseService
{
    /**
     * 保存数据
     * @param       $documentId
     * @param       $document
     * @param       $typeName
     * @param       $indexName
     * @return bool
     */
    public static function save($documentId, $document, $typeName, $indexName)
    {
        $indexName = self::getPrefix($indexName);

        // es7以上移除了type,仅做语法保留
        $typeName = '_doc';
        $url      = "/{$indexName}/{$typeName}/{$documentId}";
        $result   = self::doRequest($url, 'POST', $document);
        if ($result) {
            $result = json_decode($result, true);
            if ($result['_version']) {
                return true;
            }
        }
        LogUtil::error("同步es失败:{$indexName} {$typeName} $documentId");
        LogUtil::error($result);
        return false;
    }

    /**
     * 获取前缀
     * @param         $indexName
     * @return string
     */
    public static function getPrefix($indexName)
    {
        $prefix = env()->path('elastic.prefix');
        return $prefix . $indexName;
    }

    /**
     * 查询
     * @param        $id
     * @param        $typeName
     * @param        $indexName
     * @return array
     */
    public static function get($id, $typeName, $indexName)
    {
        $indexName = self::getPrefix($indexName);
        // es7以上移除了type,仅做语法保留
        $typeName = '_doc';
        $url      = "/{$indexName}/{$typeName}/" . $id;
        $result   = self::doRequest($url, 'GET');
        if ($result) {
            $result = json_decode($result, true);
            if ($result['found'] > 0) {
                return $result['_source'];
            }
        }
        return [];
    }

    /**
     * 更新部分字段
     * @param       $documentId
     * @param       $document
     * @param       $typeName
     * @param       $indexName
     * @return bool
     */
    public static function update($documentId, $document, $typeName, $indexName)
    {
        $indexName = self::getPrefix($indexName);
        // es7以上移除了type,仅做语法保留
        $typeName = '_doc';
        $url      = "/{$indexName}/{$typeName}/{$documentId}/_update";
        $document = [
            'doc' => $document
        ];
        $result = self::doRequest($url, 'POST', $document);
        if ($result) {
            $result = json_decode($result, true);
            if ($result['_version']) {
                return true;
            }
        }
        return false;
    }

    /**
     * 查询
     * @param        $query
     * @param        $typeName
     * @param        $indexName
     * @return mixed
     */
    public static function search($query, $typeName, $indexName)
    {
        $indexName = self::getPrefix($indexName);
        foreach ($query['query'] as $key => $item) {
            if (empty($item)) {
                unset($query['query'][$key]);
            }
        }
        // es7以上移除了type,仅做语法保留
        $typeName = '_doc';
        $url      = "/{$indexName}/{$typeName}/_search";
        $url      = "/{$indexName}/_search";
        $result   = self::doRequest($url, 'GET', $query);
        if ($result) {
            $result = json_decode($result, true);
            return $result;
        }
        return null;
    }

    /**
     * 删除数据
     * @param       $typeName
     * @param       $indexName
     * @param       $documentId
     * @return bool
     */
    public static function delete($typeName, $indexName, $documentId = null)
    {
        $indexName = self::getPrefix($indexName);
        // es7以上移除了type,仅做语法保留
        $typeName = '_doc';
        $url      = "/{$indexName}/{$typeName}";
        if ($documentId) {
            $url .= '/' . $documentId;
            $result = self::doRequest($url, 'DELETE');
        } else {
            $url .= '/_delete_by_query?conflicts=proceed';
            $filter = new \stdClass();
            $result = self::doRequest($url, 'POST', ['query' => ['match_all' => $filter]]);
        }
        if ($result) {
            $result = json_decode($result, true);
            if ($result['result'] == 'deleted' || isset($result['deleted'])) {
                return true;
            }
        }
        return false;
    }

    /**
     * 创建索引
     * @param       $indexName
     * @param       $analysis
     * @param       $mappings
     * @return bool
     */
    public static function initIndex($indexName, $analysis, $mappings)
    {
        $indexName = self::getPrefix($indexName);
        // es7以上移除了type,仅做语法保留
        $typeName = '_doc';
        $body     = [
            'settings' => [
                'analysis' => $analysis
            ],
            'mappings' => [
                'properties' => $mappings
            ]
        ];
        $result = self::doRequest("/$indexName", 'PUT', $body);
        if ($result) {
            $result = json_decode($result, true);
            if (isset($result['acknowledged']) && $result['acknowledged'] === true) {
                return true;
            }
            LogUtil::error($result);
        }
        return false;
    }

    /**
     * 分析
     * @param             $indexName
     * @param             $field
     * @param             $text
     * @return mixed|null
     */
    public static function analyze($indexName, $field, $text)
    {
        $indexName = self::getPrefix($indexName);
        $url       = "/$indexName/_analyze";
        $data      = [
            'field' => $field,
            'text'  => $text,
        ];
        $result = self::doRequest($url, 'POST', $data);
        if ($result) {
            return json_decode($result, true);
        }
        return null;
    }

    /**
     * 设置
     * @return bool
     */
    public static function settings()
    {
        $document = [
            'index.max_result_window' => 10000 * 30
        ];
        $result = self::doRequest('/_all/_settings', 'PUT', $document);
        if ($result) {
            $result = json_decode($result, true);
            if ($result['acknowledged'] == true) {
                return true;
            }
        }
        return false;
    }

    /**
     * 批量写入（bulk）
     * @param  array  $operations 每个元素包含 ['_id' => 'xxx', 'doc' => [...]]
     * @param  string $typeName
     * @param  string $indexName
     * @return bool
     */
    public static function bulk(array $operations, $typeName, $indexName)
    {
        $indexName = self::getPrefix($indexName);
        // es7以上移除了type,仅做语法保留
        $typeName = '_doc';

        $bulkBody = '';
        foreach ($operations as $op) {
            $meta = [
                'index' => [
                    '_index' => $indexName,
                    '_id'    => $op['_id']
                ]
            ];
            $bulkBody .= json_encode($meta, JSON_UNESCAPED_UNICODE) . "\n";
            $bulkBody .= json_encode($op['doc'], JSON_UNESCAPED_UNICODE) . "\n";
        }

        $url    = '/_bulk';
        $result = self::doRequest($url, 'POST', $bulkBody, 40, true); // 第5参数表示 raw body

        if ($result) {
            $result = json_decode($result, true);
            if (isset($result['errors']) && $result['errors'] === false) {
                return true;
            }
            LogUtil::error($result);
        }
        return false;
    }

    /**
     * 执行请求
     * @param        $url
     * @param        $method
     * @param  array $data
     * @param  int   $timeout
     * @param  bool  $raw
     * @return mixed
     */
    protected static function doRequest($url, $method, $data = [], $timeout = 40, $raw = false)
    {
        $config     = env()->get('elastic')->toArray();
        $connection = $config['host'] ?? 'http://localhost:9200';
        $url        = $connection . $url;

        if (!in_array($method, ['DELETE', 'PUT', 'GET', 'POST'])) {
            return null;
        }
        if (!$raw) {
            if (is_array($data) && !empty($data)) {
                $data = json_encode($data);
            }
        }
        $header = [
            'X-HTTP-Method-Override:' . $method
        ];
        if (!empty($data)) {
            $header[] = 'Content-Type: application/json; charset=utf-8';
            $header[] = 'Content-Length: ' . strlen($data);
        }
        $ch = CommonUtil::initCurl($url, $header, $timeout);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $method); // 设置请求方式
        if (!empty($data)) {
            curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        }
        $rs = curl_exec($ch);
        curl_close($ch);
        return $rs;
    }
}
