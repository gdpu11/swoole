<?php
namespace Test;

class TestPid
{

	//php G:\nginx\swoole\cli.php TestPid scanBlackWords
	public static function showPid(){
		echo posix_getpid()."\r\n";
		echo getmypid()."\r\n";
		exit();
	}
}