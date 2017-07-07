<?php
/**
 * Created by PhpStorm.
 * User: pengcong
 * Date: 2016/4/7
 * Time: 8:52
 */

class FsException extends Exception{

    const FATAL_ERROR = 0;

    public function __construct($code,$msg='',$level = 1)
    {
        $abmsg = Common_Error::$err_msg["$code"];
        if(null === $abmsg){
            $abmsg = 'internal error!';
            $code = '11';
        }
    }
}