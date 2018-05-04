<?php
namespace Test;

use DB\shares;
use Utils\RedisUtil;
use Utils\ArrayUtil;
use Utils\CurlUtils;
use Utils\Common;

class sharesHelper
{

	//php G:\nginx\swoole\cli.php sharesHelper redis
	public static function redis(){
        // RedisUtil::set(1,1);
        RedisUtil::expire(1,60);
        RedisUtil::expire(1,300);

	}
	public static function saveData(){
		for ($i=5768; $i < 100000; $i++) { 
			// echo $i.'---';
			self::save($i);
			// sleep(1);
		}
	}
	public static function save($id){
		$id = str_pad($id,7,"0",STR_PAD_LEFT);
		// $url1 = 'http://pdfm2.eastmoney.com/EM_UBG_PDTI_Fast/api/js?id='.$id;
		// $url = 'http://pdfm2.eastmoney.com/EM_UBG_PDTI_Fast/api/js?id='.$id.'&TYPE=k&js=((x))&rtntype=5&isCR=false&fsData1514442718956=fsData1514442718956';
		// $data = CurlUtils::sendGet($url1);
		// $data = CurlUtils::sendGet($url);
		$data = CurlUtils::sendGet('http://pdfm2.eastmoney.com/EM_UBG_PDTI_Fast/api/js?id='.$id.'&TYPE=k&js=((x))&rtntype=5&isCR=false&fsData1514442718956=fsData1514442718956');
		// $data = str_replace('fsData1514442718956(', '', $data);
		$data = ltrim($data,'(');	
		$data = rtrim($data,')');	
		$data = json_decode($data);
		if (!$data) {
			echo $id;
			return 0;
		}

// 		CREATE TABLE `shares` (
//   `id` int(10) DEFAULT NULL COMMENT '股票id',
//   `name` varchar(20) DEFAULT NULL COMMENT '股票名字',
//   `start` int(5) DEFAULT NULL COMMENT '开盘',
//   `end` int(5) NOT NULL COMMENT '收盘',
//   `top` int(5) DEFAULT NULL COMMENT '最高',
//   `bottom` int(5) DEFAULT NULL COMMENT '最低',
//   `volume` varchar(10) DEFAULT NULL COMMENT '成交量',
//   `valume_money` varchar(10) NOT NULL COMMENT '成交金额',
//   `amplitude` varchar(10) DEFAULT NULL COMMENT '振幅',
//   `day` date DEFAULT NULL COMMENT '日期'
// ) ENGINE=InnoDB DEFAULT CHARSET=latin1;


		$addData = array();
		foreach ($data->data as $key => $value) {
			$value = explode(',', $value);
			$one = array(
				'id'=>$data->code,
				'name'=>$data->name,
				'day'=>$value[0],
				'start'=>$value[1],
				'end'=>$value[2],
				'top'=>$value[3],
				'bottom'=>$value[4],
				'volume'=>$value[5],
				'valume_money'=>$value[6],
				'amplitude'=>$value[7],
				);
			$addData[] = $one;
		}
		shares::getInstance('shares');
		// print_r($addData);
		shares::add($addData);
		
	}

	//php G:\nginx\swoole\cli.php sharesHelper getData
	public static function getData(){
		shares::getInstance('shares');
		// $count = shares::getCount();

		$list = shares::getAll(array('group'=>'name'),1,100,array('id'=>'id','name'=>'name'));
		foreach ($list as $key => $value) {
			if (empty($value['id'])) {
				continue;
			}
			// echo $value['id'].':'.$value['name']."\r\n";
			// $count = shares::getCount(array('name'=>$value['name']));
			// $data = shares::getAll(array('name'=>$value['name'],'order'=>'day asc','group'=>'day'),1,$count,array('start'=>'start','end'=>'end',));
			// $data = ArrayUtil::getArrayColumn($data,'end',0);
			// Common::ai($data);

			echo $value['id'].':'.$value['name']."\r\n";
			$count = shares::getCount(array('name'=>$value['name']));
			$data = shares::getAll(array('name'=>$value['name'],'order'=>'day asc','group'=>'day'),1,$count,array('start'=>'start','end'=>'end',));
			$s = 0;
            $k = 0;
			foreach ($data as $key => $value) {
				if ($key==0) {
					continue;
				}
			}
			$data = ArrayUtil::getArrayColumn($data,'end',0);
			Common::ai($data);
		}

	}


	public static function getAli(){


		ini_set("display_errors", "On");
		error_reporting(E_ALL | E_STRICT);
		print_r(ali::getAll(array(),1,20,array(),0));

		/*$one = array(
			'id'=>123456,
			'company'=>1,
			'name'=>1,
			'hot'=>1,
			'url'=>1,
			'city'=>1,
			'main'=>1,
			'mode'=>1,
			'add_time'=>1,
			);
		ali::add($one);
		print_r(ali::getSums($one,'hot'));
		print_r(ali::getCount());
		print_r(ali::getAll(array(),1,20,array(),0));
		ali::delete($one);*/
		// ali::setTbFields();
		// ali::ifTbField();
	}
	
}