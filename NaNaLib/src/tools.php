<?php

/**
 * Created by PhpStorm.
 * User: pengcong
 * Date: 2016/4/7
 * Time: 9:51
 */
class Common_Tools
{
    public static function getClientIp()
    {
        $uip = '';
        if (isset($_SERVER['REAL_IP'])) {
            $uip = $_SERVER['REAL_IP'];
        } elseif (isset($_SERVER['HTTP_X_FORWARDED_FOR'])) {
            $ips = explode(",", $_SERVER['HTTP_X_FORWARDED_FOR']);
            for($i = 0;$i < count($ips);$i++){
                if(!ereg("^(10|172\.16|192\.168)\.", $ips[$i])){
                    $uip = $ips[$i];
                    break;
                }
            }
        }else{
            $uip = $_SERVER['REMOTE_ADDR'];
        }
        return $uip;
    }
}
