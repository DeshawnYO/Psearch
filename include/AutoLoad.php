<?php

class AutoLoad {

    static $files = array();

    public static function load ($classname) {
        $file = ROOT .'/include/'. str_replace("\\","/", $classname).'.php';
        if (!isset(self::$files[$file])) {
            if(is_file($file)) {
                require $file;
                self::$files[$file] = true;
            } else {
                Error::showException("{$file} not exists");
            }
        }
    }
}

function __autoload ($classname) {
    AutoLoad::load($classname);
}
