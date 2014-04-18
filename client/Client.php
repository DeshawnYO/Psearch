<?php

/**
 * 客户端测试代码
 */

$data = array(
    'method' => 'save',    
    'index' => 'test',
    'key' => 1,
    'field' => array(
        'testid' => rand(1,9999),
        'value' => '这里是值',
    ), 
);
$data=json_encode($data);
$rst = curlPost("http://192.168.1.203/Psearch/", $data);
echo $rst;
exit();

function curlPost($url, $data=array(),$ref = '',  $timeout = 10, $header = "") {
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt ($ch,CURLOPT_REFERER, $ref);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
    curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type:application/json'));
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_USERAGENT, "Psearch-Client");
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HEADER, 0);
    curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $timeout);
    curl_setopt($ch, CURLOPT_TIMEOUT, $timeout);
    $result = curl_exec($ch);
    curl_close($ch);
    return $result;
}
