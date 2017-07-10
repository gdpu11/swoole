<?php

//创建Server对象，监听 *:9501端口
$serv = new swoole_server("0.0.0.0", 9501);

$serv->set(array(
    'worker_num' => 8,   //工作进程数量
    'daemonize' => true, //是否作为守护进程
));

//监听连接进入事件
$serv->on('connect', function ($serv, $fd) {
	//返回成功信息给客户端
    $serv->send($fd, 'Connect Success!');
    echo "Client: Connect.\n";
});

//监听数据接收事件
$serv->on('receive', function ($serv, $fd, $from_id, $data) {
		//$serv->connections 所有连接对象（群聊，或群发）
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
