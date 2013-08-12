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
 * 加载AutoLoad类文件
 *
 * @author   <mengfk@eswine.com>
 * @since    1.0
 */

namespace Psearch\Inc {
    
    class Import {
    
        public static function load ($classname) {
            $file = ROOT .'/'. str_replace("\\","/",substr($classname,8)).'.Inc.php';
            if(!is_file($file)) {
                \Psearch\Inc\Error::showException("{$file} not exists");
            } else {
                require $file;
            }
        }
    }
}

namespace {
    function __autoload ($classname) {
        \Psearch\Inc\Import::load($classname);
    }
}
