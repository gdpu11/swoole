<?php
/**
*   redis工具类
*
*   @author      李强
*   @version      V 1
*   @date        2017-5-11
*/
namespace Utils;
use Utils\RedisUtil;
use Utils\Common;
class Swoole
{
    private static $instances = array();

    public static function initSwoole()
    {
        $serv = new \swoole_server("0.0.0.0", 9501);

        $serv->set(array(
            'worker_num' => 8,   //工作进程数量
            'daemonize' => true, //是否作为守护进程
        ));

        //监听连接进入事件
        $serv->on('connect', array($this, 'connect'));

        //监听数据接收事件
        $serv->on('receive', array($this, 'receive'));

        //监听连接关闭事件
        $serv->on('close', array($this, 'clientClose'));

        //启动服务器
        $serv->start();

        return $serv;
    }

    public static function getServer()
    {
        if (empty(self::$instances))
        {
            self::$instances = self::initSwoole();
        }
        return self::$instances;
    }

    public static function connect($serv, $fd,$from_id) {
        //返回成功信息给客户端
        $serv->send($fd, 'Connect Success!'."\n");
        echo "Client: Connect.\n";
    }

    public static function receive($serv, $fd, $from_id, $data) {
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

    public static function sendToAll($serv,$data = array()) {
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

    public static function sendToUsers($serv,$users = array(),$data = array()) {
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


    public static function getAppKey($appid = 1) {
        $appkeyArr = array(
            0=>0,
            1=>1,
            ); 
        return $appkeyArr[$appid];
    }

    public static function checkUser($fd,$data) {
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

    public static function clientClose($serv, $fd) {
        echo "Client: Close.\n";
    }

    public static function failed($serv, $fd) {
        $serv->send($fd, Common::jsonError(10003));
        $serv->close($fd);
    }
    

}
