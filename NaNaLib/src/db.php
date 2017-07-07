<?php

class Common_DB{

    private $_db = false;
    private $_dbmaster = false;
    private $_ismaster = false;
    private $_servers = false;
    private $_user = false;
    private $_passwd = false;
    private $_encode = 'utf8';
    private $_timeout = 1;
    private $_dbname = '';
    private $_trans = false;
    private $_dbcount = 1;
    private static $_dbpool = array();

    private function __construct($dbname,$is_master)
    {
        $this->_ismaster = $is_master;
        $this->_user = Conf_Mysql::$dbconf[$dbname]['user'];
        $this->_passwd = Conf_Mysql::$dbconf[$dbname]['passwd'];
        $this->_dbname = Conf_Mysql::$dbconf[$dbname]['dbname'];

        if(null !== Conf_Mysql::$dbconf['encode']){
            $this->_encode = Conf_Mysql::$dbconf['encode'];
        }

        $this->_servers = Conf_Mysql::$dbconf['servers'];

        if(null == $this->_servers){
            $this->_servers = Conf_Mysql::$dbconf['servers'];
        }

        $this->_dbcount = count($this->_servers);

        if(true === $is_master){
            $this->getMaster();
        }else{
            $this->getSlave();
        }
    }

    public function __destruct()
    {
        if($this->_trans && null != $this->_dbmaster){
            $this->_dbmaster->commit();
        }
    }

    public static function getDb($dbname,$is_master=false)
    {
        if(null == self::$_dbpool[$dbname]){
            self::$_dbpool[$dbname] = new Common_DB($dbname,$is_master);
        }
        return self::$_dbpool;
    }

    public function getMaster()
    {
        $dsn = $this->_servers[0].';dbname='.$this->_dbname;
        try{
            $this->_dbmaster = new PDO($dsn,
                $this->_user,
                $this->_passwd,
                array(
                    PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES '{$this->_encode}'",
                    PDO::ATTR_TIMEOUT => $this->_timeout,
                ));
        }catch (Exception $e){
            Systemlog::fatal("connect master mysql fail use: ".var_export($dsn,true).var_dump($e,true));
            throw new FsException(MYSQL_CONNECT_ERROR);
        }

        $this->_ismaster = true;
    }

    public function getSlave()
    {
        $time = microtime(true);
        $rand = intval(substr($time,11,4));
        $try_count = 0;

        while(!$this->_db && $try_count < Conf_Mysql::TRY_MAX) {
            $db_index = $rand % ($this->_dbcount-1) + 1;
            $dsn = $this->_servers[$db_index].';dbname='.$this->_dbname;
            try{
                $this->_db = new PDO($dsn,
                    $this->_user,
                    $this->_passwd,
                    array(
                        PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES '{$this->_encode}';",
                        PDO::ATTR_TIMEOUT => $this->_timeout,
                    ));
            }catch (Exception $e){
                Systemlog::fatal("connect mysql error".var_export($e,true));
                $rand++;
                $try_count++;
            }
        }
        if(Conf_Mysql::TRY_MAX === $try_count){
            Systemlog::fatal("connect slave mysql fail use: ".var_export($dsn,true));
            throw new FsException(MYSQL_CONNECT_ERROR);
        }
        return true;
    }

    public function select($sql)
    {
        if(true == $this->_ismaster) {
            $ret = $this->_dbmaster->prepare($sql);
            $ret->execute();
        }else{
            $ret = $this->_db->prepare($sql);
            $ret->execute();
        }

        if($ret->errorCode() !== '00000'){
            $error = $ret->errorInfo();
            Systemlog::fatal(var_export($error,true)." sql:$sql".$ret->errorCode());
            throw new FsException(SQL_ERROR);
        }else{
            $rows = $ret->FetchAll(PDO::FETCH_ASSOC);
        }
        return $rows;
    }

    public function update($sql)
    {
        if(true === $this->_ismaster){
            $this->_dbmaster->exec($sql);
        }else{
            $this->getMaster();
            $this->_dbmaster->exec($sql);
        }

        if($this->_dbmaster->errorCode() !== '00000'){
            $error = $this->_dbmaster->errorInfo();
            Systemlog::fatal(var_export($error,true)." sql:$sql");
            throw new FsException(SQL_ERROR);
        }
        return true;
    }

    public function insert($sql)
    {
        if(true === $this->_ismaster){
            $ret = $this->_dbmaster->exec($sql);
        }else{
            $this->getMaster();
            $ret = $this->_dbmaster->exec($sql);
        }

        if($this->_dbmaster->errorCode() !== '00000'){
            $error = $this->_dbmaster->errorInfo();
            Systemlog::fatal(var_export($error,true)." sql:$sql");
            if($this->_dbmaster->erroeCode() === '23000'){
                return -1;
                throw new FsException(SQL_ERROR);
            }
        }
        $ret = $this->_dbmaster->lastInsertId();
        return $ret;
    }

    public function startTransaction()
    {
        $this->_trans = true;
        $this->getMaster();
        $this->_ismaster = true;
        $this->_dbmaster->beginTransaction();
    }

    public function rollBack()
    {
        if(null != $this->_dbname && $this->_trans){
            $this->_dbmaster->rollBack();
            $this->_trans = false;
        }
    }

    public function commit()
    {
        if(null != $this->_dbmaster && $this->_trans){
            $this->_dbmaster->commit();
            $this->_trans = false;
        }
    }
}
