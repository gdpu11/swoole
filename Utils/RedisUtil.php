<?php
/**
*   redis工具类
*
*   @author      李强
*   @version      V 1
*   @date        2017-5-11
*/
namespace Utils;

class RedisUtil
{
    private static $instances = array();

    private static function getRedis()
    {
        $key = getmypid();
        if (empty(self::$instances[$key]))
        {
            self::$instances[$key] = new \Redis();

            self::$instances[$key]->connect('127.0.0.1', '6379');
        }
        return self::$instances[$key];
    }

    public static function ttl($key)
    {
        return self::getRedis()->ttl($key);
    }

    public static function expire($key, $second)
    {
        return self::getRedis()->expire($key, $second);
    }

    public static function expireat($key, $timestamp)
    {
        return self::getRedis()->expireat($key, $timestamp);
    }

    public static function persist($key)
    {
        return self::getRedis()->persist($key);
    }

    public static function type($key)
    {
        return self::getRedis()->type($key);
    }

    public static function renamenx($key, $newkey)
    {
        return self::getRedis()->renamenx($key, $newkey);
    }

    public static function rename($key, $newkey)
    {
        return self::getRedis()->rename($key, $newkey);
    }

    public static function keys($pattern)
    {
        return self::getRedis()->keys($pattern);
    }

    public static function del($key)
    {
        return self::getRedis()->del($key);
    }

    public static function randomkey()
    {
        return self::getRedis()->randomkey();
    }

    public static function select($index)
    {
        return self::getRedis()->select($index);
    }

    public static function ping()
    {
        return (self::getRedis()->ping());
    }

    public static function info(){
        return (self::getRedis()->info());
    }

    public static function exists($key)
    {
        return self::getRedis()->exists($key);
    }

    //----------------------------- 字符串(String) 操作 --------------------------

    public static function get($key)
    {
        return self::getRedis()->get($key);
    }

    public static function set($key, $value, $expire = -1)
    {
        return ($expire == -1) ?
            self::getRedis()->set($key, $value):
            self::getRedis()->set($key, $value, $expire);
    }

    public static function setnx($key, $value)
    {
        return self::getRedis()->setnx($key, $value);
    }

    # 该方法不能在事务中运行
    public static function setex($key, $value, $expire = 30)
    {
        return self::getRedis()->setex($key, $expire, $value);
    }

    public static function setrange($key, $value, $offset = 0)
    {
        return self::getRedis()->setrange($key, $offset, $value);
    }

    public static function append($key, $value)
    {
        return self::getRedis()->append($key, $value);
    }

    public static function mset($mapping)
    {
        return self::getRedis()->mset($mapping);
    }

    public static function mget($keys, $index=false)
    {
        if (false === $index) {
            return self::getRedis()->mget($keys);
        } else {
            return array_combine($keys, self::getRedis()->mget($keys));
        }
    }

    public static function incr($key)
    {
        return self::getRedis()->incr($key);
    }

    public static function incrby($key, $num)
    {
        return self::getRedis()->incrby($key, $num);
    }

    public static function decr($key)
    {
        return self::getRedis()->decr($key);
    }

    public static function getset($key, $value)
    {
        return self::getRedis()->getset($key, $value);
    }

    //----------------------------- 哈希(Hashs) 操作 --------------------------

    public static function hset($key, $field, $value)
    {
        return self::getRedis()->hset($key, $field, $value);
    }

    public static function hget($key, $field)
    {
        return self::getRedis()->hget($key, $field);
    }

    public static function hsetnx($key, $field, $value)
    {
        return self::getRedis()->hsetnx($key, $field, $value);
    }

    public static function hmget($key, $fields)
    {
        return self::getRedis()->hmget($key, $fields);
    }

    public static function hmset($key, $mapping)
    {
        return self::getRedis()->hmset($key, $mapping);
    }

    public static function hkeys($key)
    {
        return self::getRedis()->hkeys($key);
    }

    public static function hvals($key)
    {
        return self::getRedis()->hvals($key);
    }

    public static function hgetall($key)
    {
        return self::getRedis()->hgetall($key);
    }

    public static function hlen($key)
    {
        return self::getRedis()->hlen($key);
    }

    public static function hincrby($key, $field, $increment)
    {
        return self::getRedis()->hincrby($key, $field, $increment);
    }

    //----------------------------- 列表(Listing) 操作 --------------------------

    public static function lpush($key, $value)
    {
        if(is_array($value) && count($value) > 0) {
            array_unshift($value, $key);
            return call_user_func_array(array(self::getRedis(), 'lpush'), $value);
        } else {
            return self::getRedis()->lpush($key, $value);
        }
    }

    public static function rpush($key, $value)
    {
        if(is_array($value) && count($value) > 0) {
            array_unshift($value, $key);
            return call_user_func_array(array(self::getRedis(), 'rpush'), $value);
        } else {
            return self::getRedis()->rpush($key, $value);
        }
    }

    public static function lpop($key)
    {
        return self::getRedis()->lpop($key);
    }

    public static function rpop($key)
    {
        return self::getRedis()->rpop($key);
    }

    public static function lrange($key, $start, $stop)
    {
        return self::getRedis()->lrange($key,  $start, $stop);
    }

    public static function lindex($key, $index)
    {
        return self::getRedis()->lindex($key, $index);
    }

    public static function lset($key, $index, $value)
    {
        return self::getRedis()->lset($key, $index, $value);
    }

    public static function lrem($key, $count, $value)
    {
        return self::getRedis()->lrem($key, $count, $value);
    }

    public static function lpushx($key, $value)
    {
        return self::getRedis()->lpushx($key, $value);
    }

    public static function rpushx($key, $value)
    {
        return self::getRedis()->rpushx($key, $value);
    }

    public static function llen($key)
    {
        return self::getRedis()->llen($key);
    }

    //----------------------------- 集合(Sets) 操作 --------------------------

    public static function sadd($key, $value)
    {
        if(is_array($value) && count($value) > 0) {
            array_unshift($value, $key);
            return call_user_func_array(array(self::getRedis(), 'sadd'), $value);
        } else {
            return self::getRedis()->sadd($key, $value);
        }
    }
    
    

    public static function spop($key)
    {
        return self::getRedis()->spop($key);
    }

    public static function srandmember($key)
    {
        return self::getRedis()->srandmember($key);
    }

    public static function smembers($key)
    {
        return self::getRedis()->smembers($key);
    }

    public static function sismember($key, $value)
    {
        return self::getRedis()->sismember($key, $value);
    }

    public static function srem($key, $value)
    {
        return self::getRedis()->srem($key, $value);
    }

    public static function smove($skey, $dkey, $value)
    {
        return self::getRedis()->smove($skey, $dkey, $value);
    }

    public static function scard($key)
    {
        return self::getRedis()->scard($key);
    }


    //----------------------------- 有序集合(Sorted Sets) 操作 --------------------------

    public static function zadd($key, $score, $value)
    {
        return self::getRedis()->zadd($key, $score, $value);
    }
    
    /**
     * add by hukaisheng;
     */
     public static function zcard($key){
        return self::getRedis()->zcard($key);
     }

    public static function zrange($key, $start, $stop, $withscores = true)
    {
        return self::getRedis()->zrange($key, $start, $stop, $withscores);
    }

    public static function zrevrange($key, $start, $stop, $withscores = true)
    {
        return self::getRedis()->zrevrange($key, $start, $stop, $withscores) ;
    }

    public static function zrangebyscore($key, $max, $min, $options)
    {
        # $options = array('withscores' => withscores, 'limit' => array(offset, count))
        return self::getRedis()->zrangebyscore($key, $max, $min, $options);
    }
    public static function zrevrangebyscore($key, $max, $min, $options)
    {
        # $options = array('withscores' => withscores, 'limit' => array(offset, count))
        return self::getRedis()->zrevrangebyscore($key, $max, $min, $options);
    }

    public static function zcount($key, $min, $max)
    {
        return self::getRedis()->zcount($key, $min, $max);
    }

    public static function zrem($key, $value)
    {
        return self::getRedis()->zrem($key, $value);
    }

    public static function zscore($key, $member)
    {
        return self::getRedis()->zscore($key, $member);
    }


    //----------------------------- 事务(Transactions) 操作 --------------------------
    /**
     * @param   $mode       模式Redis:MUTIL 或 Redis:PIPELINE
     * @return  MmqRedis    其实是返回Redis对象 出于ide的包奖
     */
    public static function multi($mode = Redis::PIPELINE)
    {
        return self::getRedis()->multi($mode);
    }

    public static function exec()
    {
        return self::getRedis()->exec();
    }

    public static function unwatch()
    {
        return self::getRedis()->unwatch();
    }

    public static function discard()
    {
        return self::getRedis()->discard();
    }

}
