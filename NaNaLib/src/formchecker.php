<?php

/**
 * Created by PhpStorm.
 * User: pengcong
 * Date: 2016/4/8
 * Time: 14:50
 */
class Common_Formchecker
{

    public static function getValidData($arrkeys)
    {
        $filters = array_merge(Common_Commonfilter::$filter, Conf_Formfilter::$filter);

        foreach ($arrkeys as $key) {
            if (!isset($filters[$key])) {
                $msg = "never set filter key[{$key}]";
                throw new FsException(NEVER_SET, $msg, FATAL);
            }
            $arrfilter = $filters[$key];
            if (isset($arrfilter[$key])) {
                $key = $arrfilter['key'];
            }
            $first = strpos($key, '_');
            $prefix = substr($key, 0, $first);
            $real = substr($key, $first + 1);
            switch ($prefix) {
                case 'get':
                    $res[$real] = self::filter($key, $_GET[$real], $arrfilter);
                    break;
                case 'post':
                    $res[$real] = self::filter($key, $_POST[$real], $arrfilter);
                    break;
                case 'rawjson':
                    $raw_json = self::_getRawJsonData();
                    $res[$real] = self::filter($key, $raw_json[$real], $arrfilter);
                    break;
                case 'url':
                    $res[$real] = self::filter($key, self::getUrlParam(), $arrfilter);
                    break;
                default:
                    Systemlog::fatal("form config error please check");
                    break;
            }
        }

        return $res;
    }

    private static function filter($key, $value, $arrfilter)
    {
        if (null == $value) {
            if ($arrfilter['isoption']) {
                $value = $arrfilter['default'];
            } else {
                $msg = "not option parm is nerver set key[$key]";
                throw new FsException(NEED_PARAM, $msg, WARNGING);
            }
        } elseif ($arrfilter['nerverfilter'] == true) {
            return $value;
        } else {
            switch ($arrfilter['type']) {
                case 'int':
                    if (!is_numeric(trim($value))) {
                        $msg = "form filter need int key[$key]";
                        throw new FsException(FROM_FILTER_FAIL, $msg, WARNGING);
                    }
                    if ($value < $arrfilter['left'] || $value > $arrfilter['right']) {
                        $msg = "form filter need int between {$arrfilter['left']} and {$arrfilter['right']} key[$key]";
                        throw new FsException(FORM_FILTER_FAIL, $msg, WARNING);
                    }
                    $value = intval($value);
                    break;
                case 'string':
                    if (is_string($value)) {
                        $msg = "form filter need string key[$key]";
                        throw new FsException(FORM_FILTER_FAIL, $msg, WARNING);
                    }
                    if (strlen($value) < $arrfilter['left'] || strlen($value) > $arrfilter['right']) {
                        $msg = "form filter need stringlen between {$arrfilter['left']} and {$arrfilter['right']} key[$key]";
                        throw new FsException(FORM_FILTER_FAIL, $msg, WARNING);
                    }
                    if (true !== $arrfilter['nerverfilter']) {
                        $value = trim(self::filterInput($value));
                    }
                    break;
                case 'enum':
                    if (!in_array($value, $arrfilter['enum'])) {
                        $msg = "key [$key] must in enum " . var_export($arrfilter['enum'], true);
                        throw new FsException(FORM_FILTER_FAIL, $msg, WARNGING);
                    }
                    break;
                case 'array':
                    if (is_array($value)) {
                        $msg = "form filter need array key [$key]";
                        throw new FsException(FORM_FILTER_FAIL, $msg, WARNING);
                    }
                    if (count($value) < $arrfilter['left'] || count($value) > $arrfilter['right']) {
                        $msg = "form filter need array length between {$arrfilter['left']} and {$arrfilter['right']} key[$key]";
                        throw new FsException(FORM_FILTER_FAIL, $msg, WARNGING);
                    }

                    if (true !== $arrfilter['nerverfilter']) {
                        foreach ($value as &$item) {
                            $item = self::recurFilterInput($item);
                        }
                    }
                    break;

            }
        }
        return $value;
    }

    private static function recurFilterInput($obj)
    {
        if (is_array($obj) || is_object($obj)) {
            foreach ($obj as &$item) {
                if (is_array($item) || is_object($item)) {
                    self::recurFilterInput($item);
                } else {
                    $item = self::filterInput($item);
                }
            }
        }else{
            $obj = self::filterInput($obj);
        }
        return $obj;
    }

    private static function filterInput($str)
    {
        $farr = array(
            "/<(\\/?)(script|i?frame|style|html|body|title|link|meta|object|\\?|\\%)([^>]*?)>/isU",
            "/(<[^>]*)on[a-zA-Z]+\s*=([^>]*>)/isU",
            "/select|insert|update|delete|hex|substr|count|create|alter|drop|truncate|union|into|load_file|outfile/i"
        );

        $str = preg_replace($farr,'',$str);
//        return mysql_escape_string($str);
        return addslashes($str);
    }

    private static function _getRawJsonData()
    {
        $result = null;
        $input = file_get_contents('php://input');
        if(!empty($input)){
            $result = json_decode($input,true);
        }
        return $result;
    }

    private static function getUrlParam()
    {
        $query_string = $_SERVER['QUERY_STRING'];
        $tmp = explode('&',$query_string);
        $uri = $tmp[0];
        $uri = trim($uri,'/');
        $tmp = explode('/',$uri);
        return $tmp[count($tmp) - 1];
    }
}