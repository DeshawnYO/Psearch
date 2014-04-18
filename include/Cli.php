<?php

class Cli {

    private static $obj = NULL;

    public static function getInstance() {
        if(!self::$obj) {
            self::$obj = new self;
        }
        return self::$obj;
    }

    private function __construct() {}

    public function run($argc, $argv) {
        $index = isset($argv[1]) ? $argv[1]: '';
        $op = isset($argv[2]) ? $argv[2]: '';
        $force = isset($argv[3]) ? $argv[3]: '';
        if(empty($index) || empty($op)) {
            echo "Usage:{PHP_BIN}/php -f {Psearch_PATH}/cli.php INDEXNAME COMMAND\n\n";
            echo "  INDEXNAME 为配置文件中写入的索引名称\n";
            echo "  COMMAND : init 将会在MySQL中生成临时存储表,用于生成索引\n";
            echo "            sphinx 根据生成的MySQL表生成sphinx配置文件片段\n";
            exit();
        }
        $this->opera($index, $op, $force);
    }

    public function opera($index, $op, $force) {
        echo "[".date('Y-m-d H:i:s')."] >>>>> Begin\n";
        $indexCfg = $this->checkIndexExists($index);
        switch($op) {
            case 'init':
                $this->initDb($index, $indexCfg, $force);
                break;
            case 'sphinx':
                $this->sphinxCfg($index, $indexCfg);
                break;
            default:
                Error::showException("{$op} Operater Not Exists");
        }
    }

    public function initDb($index, $indexCfg, $force) {
        $db = new DbMysql(\Psearch\Inc\Config::$db);
        $sql = "SHOW TABLES LIKE '{$index}'";
        $rst = $db->queryFirst($sql);
        if($rst) {
            if('force' !== $force) {
                echo "[".date('Y-m-d H:i:s')."]Warning:该数据表已存在,继续执行则会清空原有数据,强制执行请在命令最后加force\n";
                echo "eg. {PHP_BIN}/php -f {Psearch_PATH}/cli.php INDEXNAME init force\n";
                exit();
            } else {
                $sql = "DROP TABLE `{$index}`";
                $db->execute($sql);
                echo "[".date('Y-m-d H:i:s')."]删除原索引数据表并清除数据\n";
            }
        }
        $sql  = "CREATE TABLE `{$index}`(";
        $sql .= "`id` int(11) unsigned NOT NULL,";
        foreach($indexCfg as $key => $val) {
            list($value, $attr) = explode('|', $val);
            $sql .= "`{$key}` {$value} NOT NULL,";
        }
        $sql .= "PRIMARY KEY (`id`)";
        $sql .= ")ENGINE=MyISAM DEFAULT CHARSET=utf8";
        $db->execute($sql);
        echo "[".date('Y-m-d H:i:s')."]生成新索引数据表\n";
        echo "[".date('Y-m-d H:i:s')."] <<<<< Succeed\n";
        exit();
    }

    public function sphinxCfg($index, $indexCfg) {
        $dbCfg = Config::$db;
        $charsetPath = Config::$charsetDictpath;
        $path = ROOT."/SphinxCfg/".$index.".sphinx.conf";
        $sqlQuery = "SELECT id , id as keyid ";
        $attrQuery = array();
        $attrQuery[] = "\tsql_attr_uint = keyid";
        foreach($indexCfg as $k => $v) {
            $attrArr = explode('|', $v);
            if(isset($attrArr[1]) && !empty($attrArr[1])) {
                $attrQuery[] = "\t".$attrArr[1]." = ".(isset($attrArr[2]) && !empty($attrArr[2]) ? $attrArr[2] : $k);
            }
            $sqlQuery .= ','.$k.' ';
        }
        $sqlQuery .= "\\\n\t\tFROM {$index} \\";
        $sqlQuery .= "\n\t\tWHERE id>=\$start AND id<=\$end";
        $cfg = array();
        $cfg[] = "source {$index} \n{";
        $cfg[] = "\ttype     = mysql";
        $cfg[] = "\tsql_host = {$dbCfg['host']}";
        $cfg[] = "\tsql_user = {$dbCfg['user']}";
        $cfg[] = "\tsql_pass = {$dbCfg['pass']}";
        $cfg[] = "\tsql_db   = {$dbCfg['dbname']}";
        $cfg[] = "\tsql_port = {$dbCfg['port']}";
        $cfg[] = "\tsql_query_pre = SET NAMES {$dbCfg['charset']}";
        $cfg[] = "\tsql_query = \\";
        $cfg[] = "\t\t{$sqlQuery}";
        $cfg[] = "\tsql_query_range = SELECT MIN(id),MAX(id) FROM {$index}";
        $cfg[] = "\tsql_range_step = 1000";
        $cfg[] = implode("\n", $attrQuery);
        $cfg[] = "}";
        $cfg[] = "index {$index}{";
        $cfg[] = "\tsource = {$index}";
        $cfg[] = "\tpath = ".ROOT."/Data/{$index}";
        $cfg[] = "\tdocinfo = extern";
        $cfg[] = "\tcharset_type = zh_cn.utf-8";
        $cfg[] = "\tcharset_dictpath = ".$charsetPath;
        $cfg[] = "}";
        file_put_contents($path, implode("\n", $cfg));
        echo "[".date('Y-m-d H:i:s')."]Sphinx配置片段生成,位置:{$path}\n";
        echo "[".date('Y-m-d H:i:s')."] <<<<< Succeed\n";
        exit();
    }

    public function checkIndexExists($index) {
        $cfg = Config::$index;
        if(!isset($cfg[$index])) {
            Error::showException("{$index} Config Not  Exists");
        }
        return $cfg[$index];
    }
}
