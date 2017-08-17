<?php
namespace Test;

use DB\ali;
use Utils\RedisUtil;
use Utils\CurlUtils;

class Test
{

	//php G:\nginx\swoole\cli.php Test getAliCli
	//关注者--粉丝
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