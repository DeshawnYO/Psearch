<?php

if(version_compare(PHP_VERSION,'5.3.0','<') ) {
    die('require PHP 5.3+');
}
define("ROOT", dirname(__FILE__));
include ROOT.'/include/AutoLoad.php';
Core::getInstance()->run();
