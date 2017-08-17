<?php
namespace Utils;

use Utils\CurlUtils;
use Utils\RedisUtil;

/**
 * curl封装
 * @author mmfei<wlfkongl@163.com>
 */
class Common
{
    /**
     * [filterWildcardChar 过滤通配符]
     * @param  [type] $arr [description]
     * @return [type]      [description]
     */
    public static function filterSpe(&$arr){
        $sql = array(
        'and','execute','\'','"','or','=',
        'update','count','chr','mid','master','truncate',
        'char','declare','select','create','delete','insert',
        '%20','%','like',
        );
        foreach ($arr as $key => $value) {
            //mysql_escape_string() 函数在字符串中某些预定义的字符前添加反斜杠。
            //quotemeta() 函数在字符串中某些预定义的字符前添加反斜杠。
            //预定义的字符：句号（.）反斜杠（\）加号（+）星号（*）问号（?）方括号（[]）脱字号（^）美元符号（$）圆括号（()）
            $arr[$key] = quotemeta(mysql_escape_string($arr[$key]));
            //mysql_real_escape_string()跟quotemeta都不转义% 和_。
            $chars = array('%','_');
            $arr[$key] = str_ireplace($arr, '', $arr[$key]);
            foreach ($chars as $k => $v) {
                $arr[$key] = str_replace($v,'\\'.$v,$arr[$key]);
            }
        }
    }   
    /***
     *防止xss注入 
     */
    public static function SafeFilter (&$arr)
    {
        if (is_array($arr))
        {
            foreach ($arr as $key => $value)
            {
                if (!is_array($value))
                {
                    $arr[$key] = strip_tags($value);
                    $arr[$key]=str_replace(array('&','<','>',"'","\\"),array('&amp;','&lt;','&gt;',"",""),$arr[$key]);
                }
                else
                {
                    self::SafeFilter($arr[$key]);
                }
            }
        }
    }

    /**
     * [outError 错误]
     * @author qianglee@kugou.net
     * @date   2017-04-26
     * @param  [type] $code    [description]
     * @param  string $message [description]
     * @param  array  $data    [description]
     * @return [type]          [description]
     */
    public static function jsonError($code, $message='', $data=array()) {
        // 是否自定义错误消息
        if (!isset($message) || empty($message)) {
            $message = self::_getErrorMsg($code);
        }
        if (!isset($data)) {
            $data = array();
        }
        $res = array('code'=>$code, 'message'=>$message, 'success'=>false, 'data'=>$data);
        self::outJson($res);
    }

    /**
     * [outSuccess 成功返回]
     * @author qianglee@kugou.net
     * @date   2017-04-26
     * @param  array      $data [description]
     * @return [type]           [description]
     */
    public static function jsonSuccess($data=array()){
        $res = array('code'=>0,'message'=>'success', 'success'=>true,'data'=>$data);
        self::outJson($res);
    }

    /**
     * [outJson 输出json]
     * @param  [type] $data [数据]
     * @return [type]       [description]
     */
    protected static function outJson($data = null)
    {
        header('Content-type: application/json; charset='.PAGE_CHARSET);
        return json_encode($data);
        // exit();
    }
    /**
     * [_getErrorMsg 获取错误信息]
     * @param  [type] $errCode [description]
     * @return [type]          [description]
     */
    private static function _getErrorMsg($errCode) {
        $msg = '';
        switch (intval($errCode)) {
            case 0: $msg = '成功'; break;
            case 10001: $msg = '参数不足'; break;
            case 10002: $msg = '操作失败'; break;
            case 10003: $msg = 'token验证失败'; break;
            case 10004: $msg = '请求超时'; break;
            default:
                $msg = "未知错误码{$errCode}";
                break;
        }
        return $msg;
    }
}