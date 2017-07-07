<?php
/**
 * Created by PhpStorm.
 * User: pengcong
 * Date: 2016/4/7
 * Time: 8:52
 */
define (FATAL, 1);
define (WARNING, 2);
define (NOTICE, 3);

//异常号码定义
define (NEVER_SET_FILTER, '2');//参数没有设置filter配置
define (FORM_FILTER_FAIL, '3');//参数校验失败
define (NEED_PARAM, '4');//必选参数缺失
define (SQL_ERROR, '11');//sql 错误
define (SQL_OR_REDIS_ERROR, '12');//write sql or redis 错误
define (FAV_NUM_EXECEED, '13');//收藏数目超过上限
define (WRITE_DATABASE_FAIL, '14');//数据写入失败 操作数据库
define (HTTP_REQUEST_FAIL, '15');//接口请求失败 CURL请求

define (RESOURCE_UNAUTHORIZED,'401'); //资源没有授权
define (RESOURCE_NOT_EXIST, '404');//访问资源不存在
define (RESOURCE_DISABLE, '405');//访问资源不存在
define (VERIFY_TOKEN_FAIL, '406');//访问资源不存在
define (FATAL_ERROR, '409');//终止性错误

define (UNKNOWN_ERROR, '111');//访问资源不存在
define (SPHINX_QUERY_ERROR, '511');//sphinx query error
define (SPHINX_SET_CONFIG_ERROR, '512');//sphinx query error
define (QUERY_NUM_EXCEED_LIMIT, '513');//query num exceed limit(2000).
define (API_PARAM_ERROR, '203');//api 接口参数错误
define (MID_NOT_EXIST, '204');//添加分集时 mid不存在
define (MID_DUP, '205');//添加分集时 相同serial id 或num已存在
define (ADD_MEDIA_ERROR, '206');//添加媒体失败
define (ADD_EPISODE_ERROR, '207');//添加媒体失败
define (CANNOT_ENABLE_ID, '208');//不能开启媒体、分集或视频
define (PRODUCE_PIC_FAIL, '209');//生产图片出错
define (UPLOAD_PIC_INVALIDE, '210');//上传的图片不符合要求

define (THIRD_PARTY_TOKEN_NOT_GET, '800');//
define (THIRD_PARTY_OPENID_NOT_GET, '801');//
define (THIRD_PARTY_USERINFO_NOT_GET, '802');//
define (FUNSHION_USERNAMEPASSWD_ERROR, '803');//
define (FUNSHION_FUDIDNOTGET_ERROR, '804');//
define (FUNSHION_LOGIN_FORBIDDEN, '805'); //1小时内连续登录失败次数过多，禁止登录
define (FUNSHION_INTERNAL_ERROR, '806');  //帐号系统内部错误
define (FUNSHION_REDISCONNECT_ERROR, '901');//
define (FUNSHION_REDISWRONGCONFKEY_ERROR, '902');//


class Common_Error
{
    public static $err_msg=array//异常对应错误信息
    (
        '2'=>'never set form filter',
        '3'=>'form filter fail',
        '4'=>'need more param',
        '11'=>'sql error',
        '12'=>'write sql or redis error',
        '13'=>'favourite num execeed limit',
        '14' => '抱歉，系统出现异常，请稍后！',
        '15' => '对不起，网络系统异常，请重试！',
        '203'=>'api param error',
        '204'=>'api mid not exist',
        '205'=>'api mid dup',
        '206'=>'add media fail',
        '207'=>'add episode fail',
        '208'=>'can not enable id',
        '209'=>'produce pic fail',
        '210'=>'upload pic invalide',
        '401'=>'resource unauthorized',
        '404'=>'resource not exist',
        '405'=>'resource disable',
        '406'=>'verify fail',
        '409'=>'fatal error',
        '111'=>'unknown error',
        '511'=>'sphinx query error',
        '512'=>'sphinx set config error',
        '513'=>'query num exceed limit(2000)',
        '800'=>'您的会话已过期，请重新登陆！',
        '801'=>'您的会话已无效，请重新登陆！',
        '802'=>'您的会话已过期或无效，请重新登陆！',
        '803'=>'用户名或者密码不正确，请重新输入！',
        '804'=>'fudid not get',
        '805'=>'对不起，您被禁止登录，请稍后再试！',
        '806'=>'对不起，账号系统内部错误,请稍后重试！',
        '901'=>'redis cannot connect',
        '902'=>'redis wrong conf key',
    );
}