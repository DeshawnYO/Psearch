<?php

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
