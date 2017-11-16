<?php
    for ($i = $start, $j = 0; $i <= $limit; $i += $step, $j++) {
        // 给予键值
        yield $j => $i;
    }
}

$xrange = xrange(1, 10, 2);
foreach ($xrange as $key => $value) {
    echo $key . ' => ' . $value . "\n";
}
class MyIterator implements Iterator {
    private $position = 0;
    private $arr = array (
        'first', 'second', 'third',
    );
    
    public function __construct() {
        $this->position = 0;
    }
    
    public function rewind() {
        var_dump(__METHOD__);
        $this->position = 0;
    }
    
    public function current() {
        var_dump(__METHOD__);
        return $this->arr[$this->position];
    }
    
    public function key() {
        var_dump(__METHOD__);
        return $this->position;
    }
    
    public function next() {
        var_dump(__METHOD__);
        ++$this->position;
    }
    
    public function valid() {
        var_dump(__METHOD__);
        return isset($this->arr[$this->position]);
    }
    
}

$it = new MyIterator();

foreach($it as $key => $value) {
    echo "\n";
    print_r($key);
    print_r($value);
}
exit();
use Utils\RedisUtil;
use Utils\Common;

ini_set("display_errors", "On");
error_reporting(E_ALL | E_STRICT);

if ('\\' === DIRECTORY_SEPARATOR) // Windows 环境下
    define('ROOT_PATH', strtr(__DIR__, DIRECTORY_SEPARATOR, '/') . '/');
else
    define('ROOT_PATH', __DIR__ . DIRECTORY_SEPARATOR);

//全局设置
include_once(ROOT_PATH.'Config/config_dev.php');

$GLOBALS['CONFIG'] = $config;

// print_r($argv);
// exit();
//自动加载类
function autoLoad($className){
    $arr = explode("\\", $className);
    include_once (ROOT_PATH.implode("/", $arr).'.php');
}
spl_autoload_register('autoLoad');
$key = 'a';
$value = RedisUtil::incr($key);
while ( $value <100000) {
    RedisUtil::set($key.$value,$value);
    $value = RedisUtil::incr($key);
}
?>
