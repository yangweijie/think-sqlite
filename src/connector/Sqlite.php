<?php
// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK ]
// +----------------------------------------------------------------------
// | Copyright (c) 2006~2019 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: liu21st <liu21st@gmail.com>
// +----------------------------------------------------------------------
declare (strict_types = 1);

namespace think\db\connector;

use Medoo\Medoo;
use think\db\Connection;

/**
 * mysql数据库驱动
 */
class Sqlite extends Connection
{

    /**
     * 数据库连接参数配置
     * @var array
     */
    protected $config = [
        // 数据库类型
        'type'            => 'sqlite',

        // 数据库名
        'database'        => '',
        // 连接dsn
        'dsn'             => '',
        // 数据库连接参数
        'params'          => [],
        // 数据库编码默认采用utf8
        'charset'         => 'utf8',
        // 数据库表前缀
        'prefix'          => '',
        // 数据库调试模式
        'debug'           => false,
        // 数据库部署方式:0 集中式(单一服务器),1 分布式(主从服务器)
        'deploy'          => 0,

        // 是否严格检查字段是否存在
        'fields_strict'   => true,
        // 是否需要进行SQL性能分析
        'sql_explain'     => false,
        // Builder类
        'builder'         => '',
        // Query类
        'query'           => '',
    ];

    /**
     * 解析pdo连接的dsn信息
     * @access protected
     * @param  array $config 连接信息
     * @return string
     */
    protected function parseDsn(array $config): string
    {
        $dsn = 'sqlite:' . $config['database'];
        return $dsn;
    }

    public function connect(array $config = [], $linkNum = 0){
        if (!isset($this->links[$linkNum])) {
            if (empty($config)) {
                $config = $this->config;
            } else {
                $config = array_merge($this->config, $config);
            }
            $this->dbName  = $config['database'];
            if (empty($config['dsn'])) {
                $config['dsn'] = $this->parseDsn($config);
            }
            $startTime = microtime(true);
            $this->links[$linkNum] = new Medoo([
                'database_type' => 'sqlite',
                'database_file' => $config['database']
            ]);
            // 记录数据库连接信息
            $this->logger('[ Sqlite ] CONNECT :[ UseTime:' . number_format(microtime(true) - $startTime, 6) . 's ] ' . $config['dsn']);
        }
        return $this->links[$linkNum];
    }

    /**
     * 取得数据表的字段信息
     * @access public
     * @param  string $tableName
     * @return array
     */
    public function getFields(string $tableName): array
    {
        list($tableName) = explode(' ', $tableName);
        $sql             = 'PRAGMA table_info( ' . $tableName . ' )';
        $result = $this->connect()->query($sql)->fetchAll();
        $info   = [];
        if (!empty($result)) {
            foreach ($result as $key => $val) {
                $val = array_change_key_case($val);
                $info[$val['name']] = [
                    'name'    => $val['name'],
                    'type'    => $val['type'],
                    'notnull' => 1 === $val['notnull'],
                    'default' => $val['dflt_value'],
                    'primary' => '1' == $val['pk'],
                    'autoinc' => '1' == $val['pk'],
                ];
            }
        }
        return $this->fieldCase($info);
    }

    /**
     * 取得数据库的表信息
     * @access public
     * @param  string $dbName
     * @return array
     */
    public function getTables(string $dbName = ''): array
    {
        $sql = "SELECT name FROM sqlite_master WHERE type='table' "
            . "UNION ALL SELECT name FROM sqlite_temp_master "
            . "WHERE type='table' ORDER BY name";
        $result = $this->connect()->query($sql)->fetchAll();
        $info   = [];
        foreach ($result as $key => $val) {
            $info[$key] = current($val);
        }
        return $info;
    }

    /**
     * SQL性能分析
     * @access protected
     * @param  string $sql
     * @return array
     */
    protected function getExplain(string $sql): array
    {
        return [];
    }

    protected function supportSavepoint(): bool
    {
        return true;
    }

}
