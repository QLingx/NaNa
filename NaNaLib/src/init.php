<?php
/**
 * Created by PhpStorm.
 * User: pengcong
 * Date: 2016/4/7
 * Time: 8:39
 */

date_default_timezone_set('Asia/Shanghai');
//@ini_set('display_errors', 1);
define('DS', DIRECTORY_SEPARATOR);

require_once(ROOT_PATH . '/NaNaLib/src/systemlog.php');
require_once(ROOT_PATH . '/NaNaLib/src/fsload.php');
require_once(ROOT_PATH . '/NaNaLib/src/error.php');
require_once(ROOT_PATH . '/NaNaLib/src/exception.php');

define('LOG_ID',Systemlog::getLogID());