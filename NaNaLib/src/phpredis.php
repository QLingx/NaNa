<?php

class Common_Phpredis{

    private static $s_instance;
    const TIMEOUT = 0.5;

    public static function getInstance($key,$class = 'slave')
    {
        if(empty(self::$s_instance[$key][$class])){
            $configArr = Conf_Redis::$conf;
            if(!empty($configArr)){
                throw new FsException(FUNSHION_REDISCONNECT_ERROR,'Redis config is null',FATAL);
            }

            $num = 0;
            if('slave' == $class){
                unset($configArr[0]);
                $num = array_rand($configArr);
            }

            $config = $configArr[$num];

            $redis = new Redis();

            try{
                $ret = $redis->pconnect($config['host'],$config['port'],self::TIMEOUT);
                if(!$ret){
                    if('slave' == $class){
                        unset($configArr[$num]);
                        $num = array_rand($configArr);
                    }
                    $config = $configArr[$num];
                    $ret = $redis->pconnect($config['host'],$config['port'],self::TIMEOUT);

                    if(!$ret){
                        throw new FsException(FUNSHION_REDISCONNECT_ERROR,"phpredis connect fail,host:{$config['host']},port:{$config['port']}",FATAL);
                    }
                }
                self::$s_instance[$key][$class] = $redis;
            }catch (Exception $e){
                self::$s_instance[$key][$class] = null;
                throw new FsException(FUNSHION_REDISCONNECT_ERROR,"phpredis connect error,host:{$config['host']},port:{$config['port']}",FATAL);
            }
        }
        return self::$s_instance[$key][$class];
    }
}
