<?php

/*
 * Psearch [A journey always starts with the first step]
 *
 * @copyright Copyright (C) 2013 wine.cn All rights reserved.
 * @license http://www.apache.org/licenses/LICENSE-2.0.txt
 */

//----------------------------------------------------------------

/**
 * 项目入口文件
 *
 * @author   <mengfk@eswine.com>
 * @since    1.0
 */
define("ROOT_IN", true);
define("ROOT", dirname(__FILE__));
include ROOT.'/Inc/AutoLoad.Inc.php';
\Psearch\Inc\Core::getInstance()->run();
