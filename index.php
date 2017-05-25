<?php

//创建Server对象，监听 127.0.0.1:9501端口
$serv = new swoole_server("101.201.71.186", 9501); 

//监听连接进入事件
$serv->on('connect', function ($serv, $fd) {  
    echo "Client: Connect.\n";
});

//监听数据接收事件
$serv->on('receive', function ($serv, $fd, $from_id, $data) {
	foreach ($serv->connections as $key => $value) {
	    $serv->send($value, 'fd:'.$fd.'-'.'from_id:'.$from_id.'-'.'data:'.$data.'-');
	}
});

//监听连接关闭事件
$serv->on('close', function ($serv, $fd) {
    echo "Client: Close.\n";
});

//启动服务器
$serv->start(); 

?>