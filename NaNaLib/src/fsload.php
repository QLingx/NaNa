<?php

/**
 * Created by PhpStorm.
 * User: pengcong
 * Date: 2016/4/7
 * Time: 8:52
 */
class FSLoad
{

    public static function init()
    {
        $ret = spl_autoload_register(__CLASS__ . '::autoload');
    }

    public static function autoload($classname)
    {
        if (class_exists($classname)) {
            return true;
        }

        $first = strpos($classname, '_');
        $prefix = substr($classname, 0, $first);
        $load_app_path = APP_PATH;
        if (isset($GLOBALS['__app_path']) && !empty($GLOBALS['__app_path'])) {
            $load_app_path = end($GLOBALS['__app_path']);
        }

        $autopath = array(
            'Action' => $load_app_path . '/action/',
            'Lib' => $load_app_path . '/lib',
            'Dao' => $load_app_path . '/dao',
            'Controller' => $load_app_path . '/controller',
            'Service' => $load_app_path . '/service',
            'Data' => $load_app_path . '/data',
            'Conf' => ROOT_PATH . '/conf/',
            'Common' => ROOT_PATH . '/NaNaLib/src/',
            'Entity' => $load_app_path . '/entity',
        );

        if (isset($autopath[$prefix])) {
            $last = strrpos($classname, '_') + 1;
            switch ($prefix) {
                case 'Action':
                    $append = '_ac';
                    break;
                case 'Service':
                    $append = '_sv';
                    break;
                case 'Dao':
                    $append = '_dao';
                    break;
                case 'Conf':
                    $append = '_conf';
                    break;
                case 'Data':
                    $append = '_da';
                    break;
                default:
                    $append = '';
                    break;
            }

            $filename = strtolower(substr($classname, $last)) . $append;
            $middle = strtolower(strstr(substr($classname, $first, $last - $first), '_', '/'));
            $file = $autopath[$prefix] . $middle . $filename . '.php';
//            if($prefix === 'Action'){
//                var_dump($file);die;
//            }
            if(!file_exists($file)){
                throw new FsException(FATAL_ERROR, 'file [' . $file . '] not found');
            }
            require $file;
        }
    }


    public static function standarAutoload($className)
    {
        $className = ltrim($className,'\\');
        $filename = '';
        $namespace = '';

        if($lastNsPos = strrpos($className,'\\')){
            $namespace = substr($className,0,$lastNsPos);
            $className = substr($className,$lastNsPos);
            $filename = str_replace('\\',DIRECTORY_SEPARATOR,$namespace).DIRECTORY_SEPARATOR;

            $filename.= str_replace('_',DIRECTORY_SEPARATOR,$className).'.php';
        }

        require $filename;
    }
}

FSLoad::init();