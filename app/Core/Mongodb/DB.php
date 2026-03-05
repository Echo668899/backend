<?php

namespace App\Core\Mongodb;

/**
 * @method mixed      findByID($id, string $idName = '_id', array $fields = [])
 * @method array|null findFirst(array $query = [], array $fields = [], array $sort = [])
 * @method array      find(array $query = [], array $fields = [], array $sort = [], int $skip = 0, int $limit = 10, $hint = null)
 * @method mixed      findAndModify(array $query = [], array $update = [], array $fields = [], bool $upsert = false, bool $new = true)
 * @method bool       updateRaw(array $document = [], array $where = [])
 * @method int|mixed  save(array $document, $incId = true)
 * @method bool       updateById(array $document, $id)
 * @method bool       update(array $document = [], array $where = [])
 * @method int|null   insert(array $document = [], bool $autoId = true, $session = null)
 * @method int        getInsertId($collectionName = '')
 * @method int        count(array $query = [], $hint = null)
 * @method bool       deleteById($id)
 * @method bool       delete(array $query = [], int $limit = 0)
 * @method bool       drop()
 * @method array|null aggregate(array $pipeline)
 * @method array|null aggregates(array $pipeline)
 *
 * @package App\Core\Mongodb
 */
class DB extends MongoModel
{
    /**
     * @param        $method
     * @param        $arguments
     * @return mixed
     */
    public function __call($method, $arguments)
    {
        if (method_exists(MongoModel::class, $method)) {
            return MongoModel::$method(...$arguments);
        }

        throw new \BadMethodCallException("Method {$method} does not exist on DB or MongoModel.");
    }

    /**
     * 指定连接
     * @param         $name
     * @return static
     */
    public static function connection($name)
    {
        static::$connection = $name;
        return new static();
    }

    /**
     * 指定表
     * @param        $table
     * @return $this
     */
    public function table($table)
    {
        static::$collection = $table;
        return $this;
    }
}
