<?php

namespace App\Utils;

/**
 * 生成model
 */
class DevUtil
{
    public $table;

    public function __construct(string $table = '')
    {
        $this->table = $table;
    }

    /**
     * 生成model
     */
    public function autoCreateModels()
    {
        $dir = APP_PATH . '/Resource/tables/';
        foreach (glob($dir . '/*/*.json') as $filePath) {
            $this->parseConfig($filePath);
        }
    }

    protected function parseConfig($configFile)
    {
        $config      = file_get_contents($configFile);
        $config      = json_decode($config, true);
        $namespace   = ucfirst(basename(dirname($configFile)));
        $tableName   = $config['table_name'];
        $className   = str_replace(' ', '', ucwords(str_replace('_', ' ', $tableName))) . 'Model';
        $connectName = $config['connect'];
        $fields      = $config['fields'];
        $indexes     = $config['index'] ?? [];
        $description = $config['description'];
        if (!empty($this->table) && $tableName != $this->table) {
            return;
        }
        LogUtil::info('Create table:' . $tableName);
        $tpl = '<?php

declare(strict_types=1);

namespace {$namespace};

use App\Core\Mongodb\MongoModel;

/**
 * {$description}
 * @package App\Models
{$property}
 */
class {$className} extends MongoModel
{
    static $connection = "{$connectName}";
    static $collection = "{$tableName}";
}';
        $tpl      = str_replace('{$namespace}', 'App\Models\\' . $namespace, $tpl);
        $tpl      = str_replace('{$description}', $description, $tpl);
        $tpl      = str_replace('{$className}', $className, $tpl);
        $tpl      = str_replace('{$connectName}', $connectName, $tpl);
        $tpl      = str_replace('{$tableName}', $tableName, $tpl);
        $property = [];
        foreach ($fields as $field) {
            $property[] = ' * @property ' . $field['type'] . ' ' . $field['name'] . ' ' . $field['description'];
        }
        $tpl      = str_replace('{$property}', join("\r\n", $property), $tpl);
        $filePath = APP_PATH . "/Models/{$namespace}/{$className}.php";
        FileUtil::mkdir($filePath);
        file_put_contents($filePath, $tpl);
        //        return;
        if (kProdMode && in_array($tableName, ['movie_history', 'comics_history', 'audio_history', 'novel_history', 'post_history'])) {
            for ($i = 0;$i < 100;$i++) {
                LogUtil::info('Create table:' . $tableName . '_' . $i);
                $this->initTable($tableName . '_' . $i, $indexes, $connectName);
            }
        } else {
            $this->initTable($tableName, $indexes, $connectName);
        }
    }

    /**
     * @param  string $tableName
     * @param  array  $indexes
     * @param  string $connectName
     * @return void
     */
    protected function initTable(string $tableName, array $indexes, string $connectName)
    {
        $cmd = [
            'listCollections' => 1,
        ];
        $connectName = "mongodb_{$connectName}";
        $result      = container()->get($connectName)->executeCommand($cmd);
        $tables      = [];
        foreach ($result as $item) {
            $tables[] = $item['name'];
        }
        // 创建集合
        if (!in_array($tableName, $tables)) {
            $cmd = [
                'create' => $tableName,
            ];
            container()->get($connectName)->executeCommand($cmd);
        }

        // 创建索引
        foreach ($indexes as $index) {
            $indexName   = strtolower($index['name']);
            $indexType   = strtolower($index['type']);
            $indexFields = [];
            foreach ($index['fields'] as $field => $sort) {
                $indexFields[$field] = intval($sort);
            }
            $cmd = [
                'createIndexes' => $tableName,
                'indexes'       => [
                    [
                        'key'    => $indexFields,
                        'name'   => "index_{$indexName}",
                        'unique' => $indexType == 'unique',
                    ],
                ]
            ];
            container()->get($connectName)->executeCommand($cmd);
        }
    }
}
