<?php

class SplClassLoader{

    private $_fileExtension = '.php';
    private $_namespace;
    private $_includePath;
    private $_namespaceSeparator = '\\';


    public function __construct($ns=null,$includePath = null)
    {
        $this->_namespace = $ns;
        $this->_includePath = $includePath;
    }

    public function getNamespaceSeparator()
    {
        return $this->_namespaceSeparator;
    }

    public function setIncludePath($includePath)
    {
        $this->_includePath = $includePath;
    }

    public function getIncludePath()
    {
        return $this->_includePath;
    }

    public function setFileExtension($fileExtension)
    {
        $this->_fileExtension = $fileExtension;
    }

    public function getFileExtension()
    {
        return $this->_fileExtension;
    }


    public function register()
    {
        spl_autoload_register(array($this,'loadClass'));
    }

    public function unregister()
    {
        spl_autoload_unregister(array($this,'loadClass'));
    }

    public function loadClass($className)
    {
        if(null === $this->_namespace
        || $this->_namespace.$this->_namespaceSeparator === substr($className,0,strlen($this->_namespace.$this->_namespaceSeparator))){
            $fileName = '';
            $nameSpace = '';

            if(false !== ($lastNsPos = strripos($className,$this->_namespaceSeparator))){
                $nameSpace = substr($className,0,$lastNsPos);
                $className = substr($className,$lastNsPos+1);
                $fileName = str_replace($this->_namespaceSeparator,DIRECTORY_SEPARATOR,$nameSpace).DIRECTORY_SEPARATOR;
            }

            $fileName .= str_replace('_',DIRECTORY_SEPARATOR,$className).$this->_fileExtension;
            require ($this->_includePath !== null ? $this->_includePath.DIRECTORY_SEPARATOR : '').$fileName;
        }
    }
}
