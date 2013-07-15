<?php

namespace Psearch\Inc;

/**
 * @class DBMySQL
 * @brief MySQL操作类
 * @auth mengfk<mengfk@eswine.com>
 * @since 1.0
 */
class DbMysql {

    var $dblink;

    var $config;

    public function __construct($config) {
        $this->config = $config;
    }

    private function connect() {
        if(!$this->dblink || !mysql_ping($this->dblink)) {
            $this->dblink = mysql_connect($this->config['host'].':'.$this->config['port'], $this->config['user'], $this->config['pass']);
            if(!$this->dblink) {
                \Psearch\Inc\Error::showError('Could not connect: ' . mysql_error(), 2001);
            }
            if(!empty($this->config['dbname'])) {
                if(!mysql_select_db($this->config['dbname'], $this->dblink)) {
                    \Psearch\Inc\Error::showError('Mysql Database Select Error: ' . mysql_error(), 2002);
                }
            }
            $version = mysql_get_server_info($this->dblink);
            if($version >= '4.1' && isset($this->config['charset'])) {
                mysql_query("SET NAMES '". $this->config['charset'] ."'", $this->dblink);
            }
            if($version >'5.0.1'){
                mysql_query("SET sql_mode=''",$this->dblink);
            }
        }
    }

    public function query($sql) {
        $this->connect();
        $result = array();
        if($query = mysql_query($sql, $this->dblink)) {
            if(mysql_num_rows($query) >0) {
                while($row = mysql_fetch_assoc($query)){
                    $result[] = $row;
                }
            }
            mysql_free_result($query);
        } else {
            \Psearch\Inc\Error::showError("Mysql Query Error:".mysql_error($this->dblink), 2003);
        }   
        return $result;
    }

    public function execute($sql) {
        $this->connect();
        $result = mysql_query($sql, $this->dblink);
        if($result == false) {
            \Psearch\Inc\Error::showError("Mysql Execute Error:".mysql_error($this->dblink), 2004);
        } else {
            $numrows = mysql_affected_rows($this->dblink);
            $last_insid = mysql_insert_id($this->dblink);
            return $last_insid ? $last_insid : $numrows;
        }
    }

    public function queryFirst($sql) {
        $result = $this->query($sql);
        return isset($result[0]) ? $result[0]: array();
    }

}
