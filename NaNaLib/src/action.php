<?php

/**
 * Created by PhpStorm.
 * User: pengcong
 * Date: 2016/4/8
 * Time: 14:27
 */
class Common_Action
{

    protected $form_check;
    private $_general_parm;
    protected $input;
    protected $_template = '';
    protected $user_verify = false;
    protected $output;
    protected $data;
    protected $cache_key = false;
    protected $cache_param = false;
    protected $cache_control = false;
    protected $ab_permit = false;
    private $iscache = '0';
    private $encrypt = false;

    public function __construct()
    {
        $this->_general_parm = array(
//            'get_cl',
//            'get_uc',
//            'get_ca',
//            'get_ca',
//            'get_udid',
        );
        $this->output = array(
            'retCode' => '200',
            'retMsg' => 'ok',
        );
    }

    public function actionInvoker()
    {
        try {
            if ('POST' === $_SERVER['REQUEST_METHOD']) {
                Systemlog::notice(var_export($_POST, true));
            }
            $this->beforeInvoker();
            $this->abControl();

            if (null != $this->form_check) {
                if (is_array($this->input)) {
                    $this->input = array_merge($this->input, Common_Formchecker::getValidData($this->form_check));
                } else {
                    $this->input = Common_Formchecker::getValidData($this->form_check);
                }
            }

            $this->input['common'] = Common_Formchecker::getValidData($this->_general_parm);
            if (false !== $this->user_verify) {
                $uid = 0;//
                if ($uid < 0) {
                    throw new FsException(VERIFY_TOKEN_FAIL, "error code $uid");
                }
            }

            //执行业务逻辑之前,参数需要准备好,参数有问题就直接结束
            $this->actionExecute();
        } catch (FsException $e) {
            $this->processException($e);
        } catch (Exception $e) {
            $this->processUnknowException($e);
        }
        //进行数据整理,职责清晰
        $this->afterInvoker();
    }

    public function beforeInvoker()
    {
    }

    public function afterInvoker()
    {
        if ($this->cache_control) {
            header("Cache-Control: max-age=" . Conf_Cache::$conf[$this->cache_param]['max_age']);
        }
        header("Access-Control-Allow-Origin:*");
        header("iscache:" . $this->iscache);
        if (is_array($this->output)) {
            if (is_array($this->data)) {
                $this->output = array_merge($this->output, $this->data);
                $this->output = json_encode($this->output);
            } else {
                $this->output = json_encode($this->output);
            }
        }

        if ('' === $this->_template) {
            if (false !== $this->encrypt && $this->input['common']['ca'] !== Conf_Common::$conf['ca']) {
                echo Common_Security::encryptData($this->output);
            } else {
                echo $this->output;
            }
        } else {

        }
    }

    public function abControl()
    {
    }

    public function processException($e)
    {
        $errno = $e->getCode();
        $msg = $e->getMessage();

        $this->output['retCode'] = $errno;
        $this->output['retMsg'] = $msg;
    }

    public function processUnknowException($e)
    {
        $errno = $e->getCode();
        $msg = $e->getMessage();
        $trace = $e->getTraceAsString();

        Systemlog::fatal("unknown exception found $msg \n$trace");

        $this->output['retCode'] = "13";
        $this->output['retMsg'] = "internal error";
    }
}