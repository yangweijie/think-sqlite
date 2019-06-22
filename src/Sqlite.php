<?php
// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK ]
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: liu21st <liu21st@gmail.com>
// +----------------------------------------------------------------------
declare (strict_types = 1);
namespace think\db;

use think\Collection;
use think\db\Connection as BaseConnection;
use think\db\connector\Sqlite as Connection;
use think\db\BaseQuery;
use think\Exception;

class Sqlite extends BaseQuery
{

    /**
     * 架构函数
     * @access public
     * @param Connection $connection 数据库连接对象
     */
    public function __construct(Connection $connection)
    {
        $this->connection = $connection;

        $this->prefix = $this->connection->getConfig('prefix');
    }

    /**
     * 获取当前的数据库Connection对象
     * @access public
     * @return BaseConnection
     */
    public function getConnection(): BaseConnection
    {
        return $this->connection;
    }

    public function getBuilderClass(){
    	return 'think\db\builder\Sqlite';
    }



}