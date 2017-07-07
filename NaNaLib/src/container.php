<?php

class Container{

    protected $bindings = [];

    public function bind($abstract,$concrete=null,$shared=false)
    {
        if(!$concrete instanceof Closure){
            $concrete = $this->getClosure($abstract,$concrete);
        }
        $this->bindings[$abstract] = compact('concrete','shared');
    }

    protected function getClosure($abstract,$concrete)
    {
        echo "getClosure".PHP_EOL;
        return function($c) use($abstract,$concrete) {
            echo "getClosure__closure";
            var_dump($abstract,$concrete);
            $method = ($abstract == $concrete) ? 'build' : 'make';
            return $c->$method($concrete);
        };
    }

    public function make($abstract)
    {
        echo "make".PHP_EOL;
        $concrete = $this->getConcrete($abstract);
        var_dump($abstract,$concrete);
        //$concrete === $abstract || $concrete instanceof Closure;
        if($this->isBuildable($concrete,$abstract)){
            $object = $this->build($concrete);
        }else{
            $object = $this->make($concrete);
        }
        return $object;
    }

    public function isBuildable($concrete,$abstract)
    {
        return $concrete === $abstract || $concrete instanceof Closure;
    }

    protected function getConcrete($abstract)
    {
        echo "getConcrete";
        var_dump($abstract);
        if(!isset($this->bindings[$abstract])){
            return $abstract;
        }
        return $this->bindings[$abstract]['concrete'];
    }

    public function build($concrete)
    {
        echo "build";
        var_dump($concrete);
        if($concrete instanceof Closure){
            return $concrete($this);
        }

        $reflector = new ReflectionClass($concrete);

        if(!$reflector->isInstantiable()){
            echo $message = "Target [$concrete] is not instanceable";
        }
        $constructor = $reflector->getConstructor();
        if(is_null($constructor)){
            return new $concrete;
        }

        $dependencies = $constructor->getParameters();

        $instances = $this->getDependencies($dependencies);
        return $reflector->newInstanceArgs($instances);
    }

    public function getDependencies($parameters)
    {
        echo "getDependencies";
        var_dump($parameters);
        $dependencies = [];
        foreach ($parameters as $parameter) {
            $dependency = $parameter->getClass();
            if(is_null($dependency)){
                $dependencies[] = null;
            }else{
                $dependencies[] = $this->resolveClass($parameter);
            }
        }
        return (Array)$dependencies;
    }

    protected function resolveClass(ReflectionParameter $parameter)
    {
        echo "resolveClass";
        return $this->make($parameter->getClass()->name);
    }

}

interface Visit{

    public function go();

}

class Train implements Visit{

    private $flag = 0;

    public function go()
    {
        echo "go with train\n";
    }
}

class Traveller{

    protected $trafficTool;
    protected $callTool;

    public function __construct(Visit $trafficTool,Call $callTool)
    {
        $this->trafficTool = $trafficTool;
        $this->callTool = $callTool;
    }

    public function visitTibet()
    {
        $this->trafficTool->go();
        $this->callTool->call();
    }
}

interface Call{

    public function call();

}

class Phone{

    protected $callTool;

    public function __constructor(Call $callTool)
    {
        $this->callTool = $callTool;
    }
}

class Apple implements Call{

    public function call()
    {
        echo "apple call";
    }
}

$app = new Container();

$app->bind("Traveller","traveller");

$app->bind("Visit","Train");
//
$app->bind("Call","Apple");


$tra = $app->make("traveller");

$tra->visitTibet();
