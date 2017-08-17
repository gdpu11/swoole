<?php
// phpinfo();
// 定义一个值为服务端根路径的常量 ROOT_PATH
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

$argv[1] = 'Test\\'.ucfirst($argv[1]);
$argv[1]::$argv[2]();


//连接数据库
// DBConnect::getInstance($config);


// print_r(exec("/web/shell/mkdir.sh"));
// echo hello_world();


?>