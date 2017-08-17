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

//创建Server对象，监听 *:9501端口
$serv = new swoole_server("0.0.0.0", 9501);

$serv->set(array(
    'worker_num' => 8,   //工作进程数量
    'daemonize' => true, //是否作为守护进程
));

//监听连接进入事件
$serv->on('connect', 'connect');

//监听数据接收事件
$serv->on('receive', 'receive');

//监听连接关闭事件
$serv->on('close', 'clientClose');

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
    'target'=>'1,2,3',//0:all,1:android,2:ios,3:pc
    );

$onlineList = '5sing_msg_online_list';
$onlineUser = '5sing_msg_online_user_';

function connect($serv, $fd,$from_id) {
    //返回成功信息给客户端
    $serv->send($fd, 'Connect Success!'."\n");
    echo "Client: Connect.\n";
}

function receive($serv, $fd, $from_id, $data) {
    $data = json_decode($data);
    
    $data = Common::objectToArray($data);

    if (checkUser($fd,$data)) {
        switch (intval($data['type'])) {

            case 1://发送系统消息
                sendToAll($serv,$data);
                break;
            
            case 2://
                $users = explode(',', $data['target']);
                sendToUsers($serv,$users,$data);
                break;

            default:
                sendToAll($serv,$data);
                break;
        }
    }else{
        failed($serv, $fd);
    }
    
}

function sendToAll($serv,$data = array()) {
        $sendData = array(
            'type'=>1,
            'title'=>$data['title'],
            'content'=>$data['content'],
            );
        $list = RedisUtil::smembers('5sing_msg_online_list'.$data['target']);
        switch (intval($data['target'])) {
            case 1:
                foreach ($list as $key => $value) {
                    $value = explode('_', $value);
                    $value = $value[0];
                    $serv->send($value, json_encode($sendData));
                }
                break;
            case 2:
                foreach ($list as $key => $value) {
                    $value = explode('_', $value);
                    $value = $value[0];
                    $serv->send($value, json_encode($sendData));
                }
                break;
            case 3:
                foreach ($list as $key => $value) {
                    $value = explode('_', $value);
                    $value = $value[0];
                    $serv->send($value, json_encode($sendData));
                }
                break;
            
            default:
                foreach ($serv->connections as $key => $value) {
                    $serv->send($value, json_encode($sendData));
                }
                break;
        }
        
}

function sendToUsers($serv,$users = array(),$data = array()) {
    $sendData = array(
        'type'=>2,
        'title'=>$data['title'],
        'content'=>$data['content'],
        );
    foreach ($users as $key => $value) {
        if (RedisUtil::exists('5sing_msg_online_user_'.$value)) {
            $fd = RedisUtil::get('5sing_msg_online_user_'.$value);
            $serv->send($fd, json_encode($sendData));
        }
    }
}


function getAppKey($appid = 1) {
    $appkeyArr = array(
        0=>0,
        1=>1,
        ); 
    return $appkeyArr[$appid];
}

function checkUser($fd,$data) {
    // if (isset($data['uid'])&&RedisUtil::sismember($onlineKey,$fd.'_'.$data['uid'])) {
    if (isset($data['uid'])&&RedisUtil::exists('5sing_msg_online_user_'.$data['uid'])) {
        return true;
    }else{
        if (count($data)<6 || !isset($data['source']) || !isset($data['uid']) || !isset($data['type']) || !isset($data['appid'])|| !isset($data['time'])|| !isset($data['token'])) {
            return false;
        }

        $appkey = getAppKey($data['appid']);

        $token = md5($data['appid'].'_'.$appkey.'_'.$data['time'].'_'.$data['source'].'_connect_5sing');

        // if ($token == $data['token']) {
        if (1) {
            RedisUtil::sadd('5sing_msg_online_list'.$data['source'],$fd.'_'.$data['uid']);
            RedisUtil::set('5sing_msg_online_user_'.$data['uid'],$fd,3600);
            return true;
        }else{
            return false;
        }
    }

}

function clientClose($serv, $fd) {
    echo "Client: Close.\n";
}

function failed($serv, $fd) {
    $serv->send($fd, Common::jsonError(10003));
    $serv->close($fd);
}


//启动服务器
$serv->start();

?>
