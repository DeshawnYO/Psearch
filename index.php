<?php

define("ROOT", dirname(__FILE__));
include ROOT.'/Inc/AutoLoad.Inc.php';
\Psearch\Inc\Core::getInstance()->run();
