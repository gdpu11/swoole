<?php
namespace Utils;

use Utils\CurlUtils;
use Utils\MatrixOperate;
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

    
    //一致性hash算法
    public static function myHash($str) {  
        // hash(i) = hash(i-1) * 33 + str[i]  
        $hash = 0;  
        $s    = md5($str);  
        $seed = 5;
        $len  = 32;
        for ($i = 0; $i < $len; $i++) {  
            // (hash << 5) + hash 相当于 hash * 33  
            //$hash = sprintf("%u", $hash * 33) + ord($s{$i});  
            //$hash = ($hash * 33 + ord($s{$i})) & 0x7FFFFFFF;  
            $hash = ($hash << $seed) + $hash + ord($s{$i});  
        }  
       
        return $hash & 0x7FFFFFFF;  
    }

    /**
     * [getAudioInfoByFile description]
     * @param  [type] $filePath   [description]
     * @param  string &$error     [description]
     * @param  string $upFileName [description]
     * @return [type]             [description]
     */
    public static function ai($temp) {
        $Matrix = new MatrixOperate();

        for ($i=5; $i < 10; $i++) { 
            $x = array();
            $y = array();
            $data = $temp;

            //矩阵求解
            foreach ($data as $key => &$value) {
                if ($key<$i||!isset($data[$key+1])) {
                    continue;
                }
                $t = array_slice($data, $key-$i,$i);
                array_unshift($t,1);
                if (count($t)<$i+1) {
                    break;
                }
                $t1 = $t;
                foreach ($t1 as $k => &$v) {
                    if ($k==0) {
                        continue;
                    }
                    array_splice($t, 2*$k, 0, $v*$v);
                    // array_splice($t, 3*$k, 0, $v*$v*$v);
                }

                // print_r($t);
                // exit();
                // echo count($t)."\r\n";
                $x[] = $t;
                $y[] = array($data[$key+1]);
            }
            // print_r($x);
            // print_r($x[1]);
            // print_r($y[0]);
            // exit();
            // exit();
            $xT = $Matrix->operate($x,'T');
           
            $xT_x = $Matrix->operate($xT,'*',$x);
            // print_r($xT_x);
            // exit();
            $xT_x_xIvs = $Matrix->operate($xT_x,'-1');

            $xT_x_xIvs_xT = $Matrix->operate($xT_x_xIvs,'*',$xT);

            $xT_x_xIvs_xT_y = $Matrix->operate($xT_x_xIvs_xT,'*',$y);
            // print_r($xT_x_xIvs_xT_y);
            $s = 0;
            $k = 0;

            for ($j=0; $j+$i < $key+2; $j++) { 
                $need[0] = array_slice($data, $j,$i);
                array_unshift($need[0],1);
                if (count($need[0])<$i+1) {
                    break;
                }
                $t1 = $need[0];
                foreach ($t1 as $k1 => &$v) {
                    if ($k1==0) {
                        continue;
                    }
                    array_splice($need[0], 2*$k1, 0, $v*$v);
                    // array_splice($need[0], 3*$k1, 0, $v*$v*$v);
                }
                $num = $Matrix->operate($need,'*',$xT_x_xIvs_xT_y);
                $num = $num[0][0];
                if (!isset($data[$j+$i])) {
                    continue;
                }

                if (($data[$j+$i]-$data[$j+$i-1]>0&&$num-$data[$j+$i-1]>0)||($data[$j+$i]-$data[$j+$i-1]<0&&$num-$data[$j+$i-1]<0)) {
                    $k++;
                }
                // if (number_format($num,1,'.','')==number_format($data[$j+$i],1,'.','')) {
                //     $k++;
                // }
                
                $s++;
            }
            // print_r($need[0]);
            echo "num:$num---";
            echo "s:$s---";
            echo "k:$k---";
            echo "i:$i---";
            $rate = $k/$s*100;
            echo $rate."%\r\n";

        }
    }
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

    public static function scanBlackWords($msg) {
        if (empty($msg)) {
            return false;
        }
        
        $url =  'http://musiclib.admin.kugou.com/interface/index.php?m=safety&a=fileNameSafetySearch';
        $params = array(
            'fileName'=>$msg,
            'clientTime'=>time(),
            );

        $params['token'] = md5("safety:fileNameSafetySearch".$params['clientTime'].$params['fileName']);
        return CurlUtils::sendGet($url.'&'.http_build_query($params));
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