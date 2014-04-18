<?php

class Config {

    //数据库配置信息,需手动建立数据库
    static $db = array(
        'host' => 'host',    
        'user' => 'user',
        'dbname' => 'dbname',
        'pass' => 'pass',    
        'port' => 3306,    
        'charset' => 'utf8',    
    );
    
    //sphinx searchd服务器信息
    static $sphinx = array(
        'host' => '127.0.0.1',
        'port' => 9312    
    );

    //是否启用Gzip压缩
    static $gzip = true;

    //索引核心配置信息,用来生成索引临时表和生成sphinx参考配置片段
    static $index = array(
        'article' => array(
            'sortid' => 'int(11) unsigned|sql_attr_uint',
            'title' => 'varchar(255)|sql_field_string',
            'keywords' => 'varchar(255)|sql_field_string',
            'description' => 'text|sql_field_string',
            'addtime' => 'int(10)|sql_attr_timestamp',
        ),
    );

    //mmseg中文分词词典所在的目录,默认为{$MMSEG_PATH}/etc
    static $charsetDictpath = "/usr/local/mmseg/etc/";
}
