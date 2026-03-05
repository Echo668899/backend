<?php

namespace App\Core\Mongodb;

use App\Utils\LogUtil;
use MongoDB\BSON\ObjectId;
use MongoDB\Driver\Session;

abstract class MongoModel
{
    /**
     * 连接
     * @var string
     */
    public static $connection = 'default';
    /**
     * 表
     * @var string
     */
    public static $collection;

    /**
     * 通过id查询
     * @param         $id
     * @param  string $idName
     * @param         $fields
     * @return mixed
     */
    public static function findByID($id, $idName = '_id', $fields = [])
    {
        $item = self::findFirst([$idName => $id], $fields);
        return empty($item) ? null : $item;
    }

    /**
     * 查找一条数据
     * @param  array $query
     * @param  array $fields
     * @param  mixed $sort
     * @return array
     * @throws
     */
    public static function findFirst($query = [], $fields = [], $sort = [])
    {
        $result = self::find($query, $fields, $sort, 0, 1);
        return empty($result) ? null : $result[0];
    }

    /**
     * @param  array      $query
     * @param  array      $fields
     * @param  array      $sort
     * @param  int        $skip
     * @param  int        $limit
     * @param  null|mixed $hint
     * @return array
     */
    public static function find($query = [], $fields = [], $sort = [], $skip = 0, $limit = 10, string $hint = null)
    {
        if ($skip < 0) {
            $skip = 0;
        }
        $tableName = static::$collection;
        $opts      = [
            'find'  => $tableName, // collection表名
            'limit' => $limit,
            'skip'  => $skip
        ];
        if ($query) {
            $opts['filter'] = $query;
        }
        if ($sort) {
            $opts['sort'] = $sort;
        }
        if ($hint) {
            $opts['hint'] = 'index_' . $hint;
        }
        if ($fields) {
            $projection = [];
            foreach ($fields as $key => $field) {
                if (is_string($key)) {
                    $projection[$key] = $field;
                } else {
                    $projection[$field] = 1;
                }
            }
            $opts['projection'] = $projection;
        }
        return self::connect()->executeCommand($opts);
    }

    /**
     * @return MongoDbConnection
     */
    public static function connect()
    {
        $keyName = 'mongodb_' . static::$connection;
        if (!container()->offsetExists($keyName)) {
            return null;
        }
        return container()->get($keyName);
    }

    /**
     * 查找并修改
     * @param  array $query
     * @param  array $update 用$inc  $set 包裹
     * @param  array $fields
     * @param  bool  $upsert
     * @param  bool  $new    false返回修改前数据 true返回修改后数据
     * @return mixed
     * @throws
     */
    public static function findAndModify($query = [], $update = [], $fields = [], $upsert = false, $new = true)
    {
        $tableName = static::$collection;
        $opts      = [
            'findAndModify' => $tableName,
            'query'         => $query,
            'update'        => $update,
            'upsert'        => $upsert,
            'new'           => $new,
        ];
        if ($fields) {
            $projection = [];
            foreach ($fields as $field) {
                $projection[$field] = 1;
            }
            $opts['fields'] = $projection;
        }
        $result = self::connect()->executeCommand($opts);
        if (isset($result[0]['value']) && !empty($result[0]['value'])) {
            return $result[0]['value'];
        }
        return null;
    }

    /**
     * 修改数据(可以使用操作符)
     * @param        $document
     * @param        $where
     * @return mixed
     * @throws
     */
    public static function updateRaw($document = [], $where = [])
    {
        $tableName = static::$collection;
        $cmd       = [
            'update'  => $tableName, // collection表名
            'updates' => [
                [
                    'q'     => $where,
                    'u'     => $document,
                    'multi' => true
                ]
            ]
        ];
        $result = self::connect()->executeCommand($cmd);
        if ($result[0]['n'] == 0 && !empty($result[0]['writeErrors'])) {
            $cmd['error'] = sprintf('%s in %s line %s', $result[0]['writeErrors'][0]['errmsg'], __FILE__, __LINE__);
            LogUtil::error($cmd);
            return false;
        }
        return $result[0]['ok'] == 1;
    }

    /**
     * 存在更新,不存在写入
     * @param  array     $document
     * @param            $incId
     * @return int|mixed
     */
    public static function save(array $document, $incId = true)
    {
        if (!empty($document['_id'])) {
            self::updateById($document, $document['_id']);
            $id = $document['_id'];
        } else {
            $id = self::insert($document, $incId);
        }
        return $id;
    }

    /**
     * @param  array $document
     * @param        $id
     * @return bool
     */
    public static function updateById(array $document, $id)
    {
        return self::update($document, ['_id' => $id]);
    }

    /**
     * 修改数据(只修改提交的数据)
     * @param        $document
     * @param        $where
     * @return mixed
     * @throws
     */
    public static function update($document = [], $where = [])
    {
        unset($document['_id']);
        $document['updated_at'] = !isset($document['updated_at']) ? time() : $document['updated_at'];
        $tableName              = static::$collection;
        $cmd                    = [
            'update'  => $tableName, // collection表名
            'updates' => [
                [
                    'q'     => $where,
                    'u'     => ['$set' => $document],
                    'multi' => true
                ]
            ]
        ];
        $result = self::connect()->executeCommand($cmd);
        if ($result[0]['n'] == 0) {
            $cmd['error'] = sprintf('%s in %s line %s', $result[0]['writeErrors'][0]['errmsg'], __FILE__, __LINE__);
            LogUtil::error($cmd);
            return false;
        }
        return $result[0]['ok'] == 1;
    }

    /**
     * 写入
     * @param  array    $document
     * @param  bool     $autoId
     * @param  Session  $session
     * @return bool|int
     * @throws
     */
    public static function insert($document = [], $autoId = true, $session = null)
    {
        $document['created_at'] = !isset($document['created_at']) ? time() : $document['created_at'];
        $document['updated_at'] = !isset($document['updated_at']) ? time() : $document['updated_at'];
        $tableName              = static::$collection;
        if ($autoId) {
            if (empty($document['_id'])) {
                $document['_id'] = intval(self::getInsertId($tableName));
                if ($document['_id'] <= 0) {
                    $document['_id'] = 1;
                }
            }
        } else {
            if (empty($document['_id'])) {
                $objectId        = new ObjectId();
                $document['_id'] = strval($objectId);
            }
        }
        $cmd = [
            'insert'    => $tableName, // collection表名
            'documents' => [$document]
        ];
        $result = self::connect()->executeCommand($cmd);
        if ($result[0]['n'] == 0) {
            $cmd['error'] = sprintf('%s in %s line %s', $result[0]['writeErrors'][0]['errmsg'], __FILE__, __LINE__);
            LogUtil::error($cmd);
        }
        return $result[0]['n'] > 0 ? $document['_id'] : null;
    }

    /**
     * 获取自增id
     * @param            $collectionName
     * @return float|int
     */
    public static function getInsertId($collectionName = '')
    {
        $collectionName = empty($collectionName) ? static::$collection : $collectionName;
        $cmd            = [
            'findAndModify' => 'collection_ids',
            'query'         => ['name' => $collectionName],
            'update'        => ['$inc' => ['id' => 1]],
            'upsert'        => true,
            'new'           => true
        ];
        $item = self::connect()->executeCommand($cmd);
        if (isset($item[0]['value']['id'])) {
            return intval($item[0]['value']['id']);
        }
        return 1;
    }

    /**
     * 统计
     * @param  array      $query
     * @param  null|mixed $hint
     * @return int
     * @throws
     */
    public static function count($query = [], string $hint = null)
    {
        $tableName = static::$collection;
        $cmd       = [
            'count' => $tableName,
        ];
        if ($query) {
            $cmd['query'] = $query;
        }
        if ($hint) {
            $cmd['hint'] = 'index_' . $hint;
        }
        $result = self::connect()->executeCommand($cmd);
        return $result[0]['n'] * 1;
    }

    /**
     * 删除数据
     * @param      $id
     * @return int
     */
    public static function deleteById($id)
    {
        return self::delete(['_id' => $id]);
    }

    /**
     * 删除数据
     * @param  array $query
     * @param        $limit
     * @return mixed
     * @throws
     */
    public static function delete(array $query = [], $limit = 0)
    {
        $tableName = static::$collection;
        $cmd       = [
            'delete'  => $tableName,
            'deletes' => [
                [
                    'q'     => empty($query) ? new \stdClass() : $query,
                    'limit' => $limit
                ]
            ]
        ];
        $result = self::connect()->executeCommand($cmd);
        return $result[0]['ok'] == 1;
    }

    /**
     * 删除集合
     * @return mixed
     * @throws
     */
    public static function drop()
    {
        $tableName = static::$collection;
        $cmd       = [
            'drop' => $tableName,
        ];
        $result = self::connect()->executeCommand($cmd);
        return $result[0]['ok'] == 1;
    }

    /**
     * 聚合
     * @param             $pipeline
     * @return array|null
     * @throws
     */
    public static function aggregate($pipeline, string $hint = null)
    {
        $tableName = static::$collection;
        try {
            $cmd = [
                'aggregate' => $tableName,
                'pipeline'  => $pipeline,
                'cursor'    => new \stdClass(),
            ];
            if ($hint) {
                $cmd['hint'] = 'index_' . $hint;
            }
            $result = self::connect()->executeCommand($cmd);
            return empty($result) ? null : $result[0];
        } catch (\Exception $exception) {
        }
        return null;
    }

    /**
     * 聚合
     * @param             $pipeline
     * @return array|null
     * @throws
     */
    public static function aggregates($pipeline, string $hint = null)
    {
        $tableName = static::$collection;
        try {
            $cmd = [
                'aggregate' => $tableName,
                'pipeline'  => $pipeline,
                'cursor'    => new \stdClass()
            ];
            if ($hint) {
                $cmd['hint'] = 'index_' . $hint;
            }
            $result = self::connect()->executeCommand($cmd);
            return empty($result) ? null : $result;
        } catch (\Exception $exception) {
        }
        return null;
    }
}
