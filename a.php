<?php
ini_set("display_errors", "On");
error_reporting(E_ALL | E_STRICT);
// phpinfo();
// 定义一个值为服务端根路径的常量 ROOT_PATH
if ('\\' === DIRECTORY_SEPARATOR) // Windows 环境下
    define('ROOT_PATH', strtr(__DIR__, DIRECTORY_SEPARATOR, '/') . '/');
else
    define('ROOT_PATH', __DIR__ . DIRECTORY_SEPARATOR);

include ROOT_PATH.'Utils'.DIRECTORY_SEPARATOR.'Getid3'.DIRECTORY_SEPARATOR.'getid3.php';
$getID3 = new \getID3;
// $filePath = 'D:\\php\\workplace\\trunk\\subject\\resource\\styles\\activity\\nz\\images\\Music_bg.jpg';
$filePath = 'D:\\php\\workplace\\trunk\\subject\\resource\\styles\\activity\\nz\\music\\0.mp3';
$audioInfo = $getID3->analyze($filePath);
print_r($audioInfo);

$dir = 'D:\song';
$file=glob($dir.'\*.mp3');
foreach ($file as $key => $value) {
	$audioInfo = $getID3->analyze($value);
	if (isset($audioInfo['bitrate'])) {
		echo $audioInfo['bitrate'];
		echo "\r\n";
	}
	
	if (isset($audioInfo['bitrate'])&&$audioInfo['bitrate']<128000) {
		print_r($audioInfo);
	}
}
// $file=scandir($dir);
// $filePath = 'D:\song\1.mp3';
// $audioInfo = $getID3->analyze($filePath);

?>