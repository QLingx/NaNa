<?php
/**
 * Created by PhpStorm.
 * User: pengcong
 * Date: 2016/5/13
 * Time: 14:06
 */

class Common_Dispatcher{

    protected static $_instance = null;
    protected $_action = '';
    protected $_defaultaction = 'Action_Index';
    private $_controller = false;

    protected function __construct($controller)
    {
        $this->_controller = $controller;
        $this->setController();
    }

    public static function getInstance($controller = false)
    {
        if(is_null(self::$_instance)){
            self::$_instance = new Common_Dispatcher($controller);
        }
        return self::$_instance;
    }

    public function dispatch()
    {
        $this->doAction();
    }

    public function doAction()
    {
        $ac_name = $this->_action;
        try{
            $action = new $ac_name();
            $action->actionInvoker();
        }catch (Exception $e){
            header('Location:errorpage');
        }
    }

    private function setController()
    {
        $query_string = $_SERVER['QUERY_STRING'];
//        $query_string = $_SERVER['REQUEST_URI'];
        $query_string = trim($query_string,'/');
        $tmp = explode('&',$query_string);
        if(false === $this->_controller){
            $tmp = explode('/',$tmp[0]);
            if(count($tmp) < 1){
                $this->_action = $this->_defaultaction;
            }else{
                $this->_action = 'Action_'.ucfirst($tmp[0]);
            }
        }else{
//            $c = 'Controller_'.$this->_controller;
//            $this->_action = $c::$route[$tmp[0]];
        }
    }


}