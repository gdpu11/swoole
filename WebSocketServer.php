<?php
if ('\\' === DIRECTORY_SEPARATOR) // Windows 环境下
    define('ROOT_PATH', strtr(__DIR__, DIRECTORY_SEPARATOR, '/') . '/');
else
    define('ROOT_PATH', __DIR__ . DIRECTORY_SEPARATOR);

//全局设置
include(ROOT_PATH.'Config/config_dev.php');

$GLOBALS['CONFIG'] = $config;

// print_r($argv);
// exit();
//自动加载类
function autoLoad($className){
    if ($className == 'Redis') {
        $redis = new Foo\Bar\Redis();
        return $redis;
    }
    $arr = explode("\\", $className);
    include(ROOT_PATH.implode("/", $arr).'.php');
}
spl_autoload_register('autoLoad');

$server = new swoole_websocket_server("0.0.0.0", 9502);

$server->on('open', function (swoole_websocket_server $server, $request) {
    echo "server: handshake success with fd{$request->fd}\n";
});

$server->on('message', function (swoole_websocket_server $server, $frame) {
    echo "receive from {$frame->fd}:{$frame->data},opcode:{$frame->opcode},fin:{$frame->finish}\n";
    $server->push($frame->fd, "this is server");
});

$server->on('close', function ($ser, $fd) {
    echo "client {$fd} closed\n";
});

$server->start();
?>