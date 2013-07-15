<?php

namespace Psearch\Inc;

class Config {

    static $db = array(
        'host' => 'localhost',    
        'user' => 'root',    
        'dbname' => 'Psearch',
        'pass' => 'root',    
        'port' => 3306,    
        'charset' => 'utf8',    
    );

    static $sphinx = array(
        'host' => '127.0.0.1',
        'port' => 9312    
    );

    static $gzip = true;

    static $index = array(
        'article' => array(
            'sortid' => 'int(11) unsigned|sql_attr_uint',
            'title' => 'varchar(255)|sql_field_string',
            'keywords' => 'varchar(255)|sql_field_string',
            'description' => 'text|sql_field_string',
            'addtime' => 'int(10)|sql_attr_timestamp',
        ),
    );

    static $charsetDictpath = "/usr/local/mmseg/etc/";
}
