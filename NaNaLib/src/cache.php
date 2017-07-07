<?php

class Common_Cache{

    private static $_switch = true;

    public static function setStatus($status = true)
    {
        self::$_switch = $status;
    }

    public static function set($pid,$key,$value)
    {
        if(false === self::$_switch){
            return false;
        }
        try{
            $redis = Common_Phpredis::getInstance($pid,'master');
            $redis->set($key,$value);
        }catch (Exception $e){
            Systemlog::fatal("Redis set key exception:{$pid},{$key},{$value}".$e->getMessage());
            return false;
        }
        return true;
    }

    public static function del($pid,$key)
    {
        if(false === self::$_switch){
            return false;
        }

        try{
            $redis = Common_Phpredis::getInstance($pid,'master');
            $redis->del($key);
        }catch (Exception $e){
            Systemlog::fatal("Redis del key exception:{$pid},{$key}".$e->getMessage());
            return false;
        }

        return true;
    }

    public static function setex($pid,$key,$expire,$value)
    {
        if(false === self::$_switch){
            return false;
        }

        try{
            $redis = Common_Phpredis::getInstance($pid,'master');
            $redis->setex($key,$expire,$value);
        }catch (Exception $e){
            Systemlog::fatal("Redis set key exception:{$pid},{$key},{$expire},{$value}".$e->getMessage());
            return false;
        }

        return true;
    }

    public static function get($pid,$key,$class = 'slave')
    {
        if(false === self::$_switch || $_GET['ca'] === Conf_Common::$conf['ca']){
            return false;
        }

        try{
            $redis = Common_Phpredis::getInstance($pid,$class);
            $ret = $redis->get($key);
        }catch (Exception $e){
            Systemlog::fatal("get cache exception $pid,$key".$e->getMessage());
            Systemlog::notice("cache_log:no:{$key}");
            return false;
        }

        if(null === $ret){
            Systemlog::notice("cache_log:no:{$key}");
            return false;
        }

        Systemlog::notice("cache_log:yes:{$key}");
        return $ret;

    }

    public static function getCache($pid,$key,$class='slave')
    {
        if(false === self::$_switch || $_GET['ca'] === Conf_Common::$conf['ca']){
            return false;
        }
        $ret = false;
        try{
            $redis = Common_Phpredis::getInstance($pid,$class);
            $ttl = $redis->ttl($key);
            if($ttl > 0){
                $ret = $redis->get($key);
            }
        }catch (Exception $e){
            Systemlog::fatal("get cache exception {$pid},{$key}".$e->getMessage());
            Systemlog::notice("cache_log:no:{$key}");
            return false;
        }

        if(!$ret){
            Systemlog::notice("cache_log:no:{$key}");
        }else{
            Systemlog::notice("cache_log:yes:{$key}");
        }
        return $ret;
    }
}
