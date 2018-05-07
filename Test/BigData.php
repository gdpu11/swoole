<?php
namespace Test;

use DB\user_hash_id;
use DB\user_info;
use DB\user_comment;
use DB\user_uid;
use DB\user_uid_myisam;

use Utils\RedisUtil;
use Utils\CurlUtils;
use Utils\Common;

class BigData
{
	public static function hello(){
		echo 'hello';exit();
	}
	public static function getHash(){
		$id = rand(1,8388608);
		echo $id;
		echo self::myHash($id);
		exit();
	}

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
	//php G:\nginx\swoole\cli.php BigData getuseruid
	public static function getuseruid(){
		$test_key = 'big_data:test_key';
		$id = rand(1,8388608);
		// $id = RedisUtil::incr($test_key);
		$data =  user_uid::getOne(array('id'=>$id),array('id'=>'id'));
		print_r($data); 
		// $data =  user_uid_myisam::getOne(array('id'=>$id),array('id'=>'id'));
		// print_r($data);
		print_r($id); 
		exit();
	}
	//php G:\nginx\swoole\cli.php BigData register
	public static function buildCommentUser(){
		$test_key = 'big_data:test_key';
		$id = rand(1,1000000);
		// $id = RedisUtil::incr($test_key);
		$data =  user_comment::getOne(array('id'=>$id),array('id'=>'id'));
		print_r($data); 
		print_r($id); 
		exit();
	}
	public static function register(){
		user_info::getInstance('big_data');
		for ($i=30000000; $i < 40000000; $i++) { 
			$user = array(
				'phone'=>$i,
				'user_name'=>'user_name_'.$i,
				'pass'=>'pass_'.$i,
			);
			
			$one = user_hash_id::getOne(array('hash'=>'big_data_'.$user['user_name']));

			if ($one) {
				echo $user['user_name']."\r\n";
				echo '该用户已注册'."\r\n";
			}else{
				user_info::add($user);
				$id = user_info::getLastId(array('PARTITION_INDEX'=>1));
				echo $id."\r\n";
				user_hash_id::add(array('hash'=>'big_data_'.$user['user_name'],'id'=>$id));
				echo '注册成功'.$id."\r\n";
			}
		}
	}
	
}