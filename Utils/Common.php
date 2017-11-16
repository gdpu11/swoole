<?php
namespace Utils;

use Utils\CurlUtils;
use Utils\RedisUtil;
use DB\zidian;
use DB\cnword;
use DB\ciku;

/**
 * curl封装
 * @author mmfei<wlfkongl@163.com>
 */
class Common
{

    public static function getAudioInfoByFile($filePath, &$error='', $upFileName='') {
        // 获取临时存储文件名
        $fileSize = filesize($filePath);

        include ROOT_PATH.'Utils'.DIRECTORY_SEPARATOR.'Getid3'.DIRECTORY_SEPARATOR.'getid3.php';
        $getID3 = new \getID3;
        $audioInfo = $getID3->analyze($filePath, $fileSize, $upFileName);
        if (!$audioInfo || isset($audioInfo['error'])) {
            $error = isset($audioInfo['error'][0]) ? $audioInfo['error'][0] : '';
            return 0;
        }

        // 组合返回信息
        $duration = isset($audioInfo['playtime_seconds']) ? $audioInfo['playtime_seconds'] : 0;
        $bitrate = isset($audioInfo['audio']) && isset($audioInfo['audio']['bitrate']) ? floor($audioInfo['audio']['bitrate']/1000) : 0;
        $samplerate = isset($audioInfo['audio']) && isset($audioInfo['audio']['sample_rate']) ? intval($audioInfo['audio']['sample_rate']) : 0;
        $data = array(
                'fileSize' => intval($audioInfo['filesize']),
                'fileType' => strtolower($audioInfo['fileformat']),
                'duration' => intval($duration*100)/100,
                'bitrate' => $bitrate,
                'samplerate' => $samplerate,
            );

        unset($getID3);
        unset($audioInfo);

        return $data;
    }
    /*
    中华字典词库表
    id
    type
    value
    1:语句切割
        1):所有类型切割--------2^(n-1)
            用递归：f(n) = 2*f(n-1);
        2):从第一个开始取，最坏的是--------n*(n+1)/2
        3):
        单个切割，分析语义词性--------n
        获取介词以及动词作为切割点
        在分别对每一段进行语义分析
            1:取第一个字，匹配下一个字是否为一起的词，直到不连续为止
            2：从这个不连续的词开始继续步骤一


    */
    public static function getWord($str,$index = 0,$return = array()){
        $strArr = self::mb_str_split($str);
        foreach ($strArr as $key => $value) {
            if ($key<$index) {
                continue;
            }
            $index = self::getWordIndex($strArr,$index);
            if (!isset($strArr[$index+1])) {
                break;
            }
            $return[] = $index;
        }
        return array($return,$strArr);
    }


    public static function getWordIndex($strArr,$index){

        $one = zidian::getOne(array('value'=>$strArr[$index]));
        // $cnword = cnword::getAll(array('words'=>array('likex'=>$strArr[$index].$strArr[$index+1])),1,200,array('words'=>'words'));
        // $ciku = ciku::getAll(array('value'=>array('likex'=>$strArr[$index].$strArr[$index+1])),1,200,array('value'=>'value'));
        // $cnword = ArrayUtil::getArrayColumn($cnword,'words');
        // $ciku = ArrayUtil::getArrayColumn($ciku,'value');
        $find = $strArr[$index];
        foreach ($strArr as $key => $value) {
            if ($key<=$index) {
                continue;
            }
            $find .=$value;
            // if (0) {
            // if (stripos($one['detail'], $find)) {
            if (stripos($one['detail'], $find)||ciku::getOne(array('value'=>$find))||cnword::getOne(array('words'=>$find))) {
            // if (stripos($one['detail'], $find)||in_array($find, $cnword)||in_array($find, $ciku)) {
                continue;
            }else{
                return $key;
            }
        }
        return $key;
        $workType = self::getWordType($one['detail']);
        print_r($strArr);
        exit();
    }
    

    public static function getWordType($work){
        preg_match_all('#【(.*?)】#', $work, $detail);
        return $detail[1];
    }

    public static function mb_str_split($str){  
        return preg_split('/(?<!^)(?!$)/u', $str );  
    } 

    /**
     * [changeCr 获取英文字符串大小写所有组合]
     * abc
     * 8种：abc,abC,aBc,aBC,Abc,AbC,ABc,ABC
     * @param  [type] $str [description]
     * @return [type]      [description]
     */
    public static function changeCr($str){
        $len = strlen($str);    
        $count = pow(2,$len);
        $result = array();
        for ($i=0; $i < $count; $i++) { 
            $val = decbin($i);
            $val = str_pad($val,$len,"0",STR_PAD_LEFT);
            for ($k=0,$l=strlen($val) ; $k < $l; $k++) {
                $str[$k] = $val[$k]?(($str[$k] >= 'a' && $str[$k] <= 'z') ? ($str[$k] & chr(-33)) : $str[$k])
                :(($str[$k] >= 'A' && $str[$k] <= 'Z') ? ($str[$k] | chr(32)) : $str[$k]);
            }
            $result[] = $str;
        }
        return $result;
    }

    /**
     * [getRedisByKey 获取接口数据缓存]
     * @author 李强 qianglee@kugou.net
     * @date   2017-01-12
     * @param  [type]  $key        [缓存key]
     * @return [type]              [description]
     */
    public static function getRedisByKey($cachKey) {
        if (!empty($cachKey)&&RedisUtil::exists($cachKey)) {
            return unserialize(RedisUtil::get($cachKey));
        }
        return array();
    }

    /**
     * [setRedisByKey 设置接口数据缓存]
     * @author 李强 qianglee@kugou.net
     * @date   2017-01-12
     * @param  [type]  $key        [缓存key]
     * @param  [type]  $data        [缓存数据]
     * @param  integer $time       [缓存时间]
     * @return [type]              [description]
     */
    public static function setRedisByKey($CacheKey,$data,$time = 3600) {
        if (empty($CacheKey)) {
            self::outError(100007);
        }
        //json_encode()时候，json_decode()出来数据接口会变
        RedisUtil::set($CacheKey,serialize($data),$time);
    }

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
     * [objectToArray 对象转为数组]
     * @author qianglee@kugou.net
     * @date   2017-04-26
     * @param  [type] $code    [description]
     * @param  string $message [description]
     * @param  array  $data    [description]
     * @return [type]          [description]
     */
    public static function objectToArray(&$object) {
             $object =  json_decode( json_encode( $object),true);
             return  $object;
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
        return self::outJson($res);
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
        return self::outJson($res);
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