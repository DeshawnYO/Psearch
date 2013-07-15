<?php

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
