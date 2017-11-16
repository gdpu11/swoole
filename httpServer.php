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


$http = new swoole_http_server("127.0.0.1", 9517);
$http->on('request', function ($request, $response) {
    // print_r($request->get);
    // print_r($request->post);
    // print_r($request->cookie);
    // print_r($request->files);
    // print_r($request->header);
    // print_r($request->server);
    $_GET = $request->get;

    // $response->cookie("User", "Swoole");
    // $response->header("X-Server", "Swoole");
    // $response->end("<h1>Hello Swoole!</h1>");

    $g = isset($_GET['g']) ? ucfirst($_GET['g']) : 'User';
    $c = isset($_GET['c']) ? ucfirst($_GET['c']) : 'User';
    $function = isset($_GET['f']) ? $_GET['f'] : 'login';
    $class = $g.'\\'.$c;

    if (method_exists($class, $function)) {
            $result = $class::$function();
    } else {
        echo 'what???';exit();
    }

    $response->end(json_encode($result));
    // $response->end("<h1>Hello Swoole. #".rand(1000, 9999)."</h1>");
});
$http->start();

?>