<?php

define("ROOT", dirname(__FILE__));
error_reporting(0);
include ROOT.'/include/AutoLoad.php';
Cli::getInstance()->run($argc, $argv);
