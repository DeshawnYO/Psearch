<?php

class Error {

    /**
     * ErrorCode:
     *     1001 - HTTP_RAW_POST_DATA不存在
     *     1002 - 请求的方法不存在
     *     1003 - 索引存储表不存在
     *     1004 - 数据插入失败
     *     1004 - 数据更新失败
     *     2001 - 无法连接MySQL数据库
     *     2002 - 数据库选择失败
     *     2003 - SQL查询失败
     *     2004 - SQL写入失败
     *     3001 - SphinxClient不存在
     *     3002 - 连接Sphinx失败
     *     3003 - Sphinx参数设置出错
     */
    static function showError($msg, $errcode = 1999) {
        $error = array(
            'message' => $msg,
            'errcode' => $errcode,
            'data' => '',
        );
        die(json_encode($error));
    }

    static function showException($message) {
        die("[Error] '{$message}'\n");
    }
}
