<?php
use Utils\RedisUtil;
use Utils\Common;
use Utils\Swoole;

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


Swoole::initTcpServer();
/*

$accecpt = array(
    'appid'=>1,
    'time'=>1,
    'token'=>1,
    'source'=>1,//1:android,2:ios,3:pc
    'uid'=>1,
    'type'=>0,
    );

$sendSysMsg = array(
    'appid'=>1,
    'time'=>1,
    'token'=>1,
    'source'=>1,//1:android,2:ios,3:pc
    'uid'=>1,
    'type'=>1,
    'title'=>'title',
    'content'=>'content',
    'target'=>0,//0:all,1:android,2:ios,3:pc
    );

$sendUserMsg = array(
    'appid'=>1,
    'time'=>1,
    'token'=>1,
    'source'=>1,//1:android,2:ios,3:pc
    'uid'=>1,
    'type'=>1,
    'title'=>'title',
    'content'=>'content',
    'target'=>'1,2,3',//0:all,1:android,2:ios,3:pc,字符串：用户id
    );
*/

?>
