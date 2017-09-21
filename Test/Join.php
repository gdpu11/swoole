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

		print_r(ali::getCount());
		exit();
		// ali::setTbFields();
		// ali::ifTbField();
	}
	
}