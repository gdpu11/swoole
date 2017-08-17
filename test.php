<?php
use Utils\RedisUtil;
use Utils\Common;

ini_set("display_errors", "On");
error_reporting(E_ALL | E_STRICT);

if ('\\' === DIRECTORY_SEPARATOR) // Windows 环境下
    define('ROOT_PATH', strtr(__DIR__, DIRECTORY_SEPARATOR, '/') . '/');
else
    define('ROOT_PATH', __DIR__ . DIRECTORY_SEPARATOR);

//全局设置
include_once(ROOT_PATH.'Config/config_dev.php');

$GLOBALS['CONFIG'] = $config;

// print_r($argv);
// exit();
//自动加载类
function autoLoad($className){
    $arr = explode("\\", $className);
    include_once (ROOT_PATH.implode("/", $arr).'.php');
}
spl_autoload_register('autoLoad');
$key = 'a';
$value = RedisUtil::incr($key);
while ( $value <1000000) {
    RedisUtil::set($key.$value,$value);
    $value = RedisUtil::incr($key);
}
?>
