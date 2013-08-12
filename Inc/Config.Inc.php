<?php

/*
 * Psearch [A journey always starts with the first step]
 *
 * @copyright Copyright (C) 2013 wine.cn All rights reserved.
 * @license http://www.apache.org/licenses/LICENSE-2.0.txt
 */

//----------------------------------------------------------------

defined("ROOT_IN") || die("Access Denied");

/**
 * 配置文件
 * 
 * 详细配置信息请参考Readme https://github.com/dreamans/Psearch
 *
 * @author   <mengfk@eswine.com>
 * @since    1.0
 */

namespace Psearch\Inc;

class Config {

    //数据库配置信息,需手动建立数据库
    static $db = array(
        'host' => '127.0.0.1',    
        'user' => 'dbuser',    
        'dbname' => 'Psearch',
        'pass' => 'dbpass',    
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
