<?php

/*
 * Psearch [A journey always starts with the first step]
 *
 * @copyright Copyright (C) 2013 wine.cn All rights reserved.
 * @license http://www.apache.org/licenses/LICENSE-2.0.txt
 */

//----------------------------------------------------------------

/**
 * 视图
 *
 * @author   <mengfk@eswine.com>
 * @since    1.0
 */

namespace Psearch\Inc;

class View {

    public static function outHeader() {
        header('Cache-control: private');
        header('Content-Type: text/html; charset= utf-8');
        header('X-Powered-By:Psearch');
    }

    public static function output($data, $msg = 'Succeed') {
        $rest = array(
            'message' => $msg,
            'errcode' => 0,
            'data' => $data,
        );
        die(json_encode($rest));
    }
}
