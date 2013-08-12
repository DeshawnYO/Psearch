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
 * 程序运行核心支持文件
 *
 * 进行参数过滤,路由转发,module初始化等
 *
 * @author   <mengfk@eswine.com>
 * @since    1.0
 */

namespace Psearch\Inc;

class Core {

    private static $obj = NULL;

    public static function getInstance() {
        if(!self::$obj) {
            self::$obj = new self;
        }
        return self::$obj;
    }

    private function __construct() {}

    public function run() {
        $this->init();
        $json = $this->filterParam();
        new \Psearch\Inc\Module($this->getParam($json));
    }

    public function init() {
        if(version_compare(PHP_VERSION,'5.3.0','<') ) {
            @set_magic_quotes_runtime (0);
        }
        if(function_exists('date_default_timezone_set')) {
            @date_default_timezone_set("Etc/GMT-8");
        }
        if(function_exists('ob_gzhandler') && \Psearch\Inc\Config::$gzip) {
            ob_start('ob_gzhandler');
        } else {
            ob_start();
        }
        ob_implicit_flush(0);
        \Psearch\Inc\View::outHeader();
    }

    public function filterParam() {
        $data = isset($GLOBALS['HTTP_RAW_POST_DATA']) ? $GLOBALS['HTTP_RAW_POST_DATA']: '';
        unset($_GET,$_POST,$_SESSION,$_SERVER,$_COOKIE,$_FILES);
        $json = json_decode($data, true);
        if($json) {
            return $json;
        } else {
            \Psearch\Inc\Error::showError("POST DATA EMPTY", 1001);
        }
    }

    public function getParam($json) {
        $index  = isset($json['index']) && !empty($json['index']) ? $json['index'] : '';
        $method = isset($json['method']) && !empty($json['method']) ? $json['method'] : '';
        $key    = isset($json['key']) && !empty($json['key']) ? $json['key'] : '';
        $field  = isset($json['field']) && !empty($json['field']) ? $json['field'] : '';
        $limit  = isset($json['limit']) && !empty($json['limit']) ? $json['limit'] : '';
        $order  = isset($json['order']) && !empty($json['order']) ? $json['order'] : '';
        $keyword= isset($json['keyword']) && !empty($json['keyword']) ? $json['keyword'] : '';
        $condition= isset($json['condition']) && !empty($json['condition']) ? $json['condition'] : '';
        return array($index, $method, $key, $field, $limit, $order, $keyword, $condition);
    }
}
