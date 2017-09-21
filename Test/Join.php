<?php
namespace Test;

use DB\ali;
use DB\zidian;
use Utils\RedisUtil;
use Utils\CurlUtils;
use Utils\Common;

class Join
{


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
		ali::add($one);
		print_r(ali::getSums($one,'hot'));
		print_r(ali::getCount());
		print_r(ali::getAll(array(),1,20,array(),0));
		ali::delete($one);
		// ali::setTbFields();
		// ali::ifTbField();
	}
	
}