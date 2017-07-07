<?php
/**
 * Created by PhpStorm.
 * User: pengcong
 * Date: 2016/4/7
 * Time: 8:33
 */

define('ROOT_PATH',dirname(dirname(__FILE__)));
define('APP_PATH',ROOT_PATH.'/AppTest');
define('LOG_PATH','/data/log/home');
require_once(APP_PATH.'/conf/defines.php');
require_once(ROOT_PATH.'/NaNaLib/src/init.php');

$dispatcher = Common_Dispatcher::getInstance();
$dispatcher->dispatch();
