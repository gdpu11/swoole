<?php
namespace Test;

use DB\ali;
use DB\zidian;
use Utils\RedisUtil;
use Utils\CurlUtils;
use Utils\Common;

class Test
{

	//php G:\nginx\swoole\cli.php Test zidian
	//关注者--粉丝
	//id
	// value
	// 5bi
	// bushou
	// bihuashu
	// chubushoubihuashu
	// pinyin
	// intro
	// detail
	public static function splicChn($str){
		$return = Common::getWord($str);
		echo implode('',$return[1]).':<br />';
		$fenci = array();
		foreach ($return[1] as $key => $value) {
			if (in_array($key, $return[0])) {
				$fenci[] = ' ';
			}
			$fenci[] = $value;
		}
		$str = implode('',$fenci);
		echo $str;
		echo '<br />';
		$strArr = explode(' ', $str);
		print_r($strArr);
		exit();
	}
	public static function zidian(){
		$return = self::splicChn('原标题：跳楼产妇母亲：医院血口喷人！我怎么可能让女儿痛到寻死！主治医生已停职配合调查');
		$return = self::splicChn('既可以像最大熵模型一样加各种领域国内通过发行代币形式包括首次代币发行（ICO）进行融资的活动大量涌现');
		// $return = self::splicChn('既可以像最大熵模型一样加各种领域');
		$return = self::splicChn('南京市长江大桥');
		$return = self::splicChn('进行融资的活动大量涌现');
		$return = self::splicChn('家“刷脸,阿拉伯数码');
		$return = self::splicChn('在家“刷脸”就能远程阅卷了！哀乐按年广州越秀嗷嗷待哺秀法院推出微信阅卷服务');
		$return = self::splicChn('精心挑选真实的数据集为案例');
		// $return = self::splicChn('双宋宣布婚讯后首同框现身');
		// $return = self::splicChn('中国人民银行网站发布公告称');
		// $return = self::splicChn('各类代币发行融资活动应当立即停止');
		
	}
	public static function analyse(){
		exit();
	}
	public static function changeCr(){
		$str = 'sadsadasdaefdsfsdss';
		$stime=microtime(true);
		Common::changeCr($str);
		$etime=microtime(true);//获取程序执行结束的时间
 		$total=$etime-$stime; 
 		echo $total.'<br/>';
	}

	//php G:\nginx\swoole\cli.php Test getAliCli
	public static function getAliCli(){
		$one = array(
			'id'=>1,
			'company'=>1,
			'name'=>1,
			'hot'=>1,
			'url'=>1,
			'city'=>1,
			'main'=>1,
			'mode'=>1,
			'add_time'=>1,
			);
		print_r(ali::getSums($one,'hot'));
		print_r(ali::getCount());
		print_r(ali::getAll());
		ali::add($one);
		// ali::setTbFields();
		// ali::ifTbField();
	}
	
}