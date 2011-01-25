<?php

require_once __DIR__ . '/addendum/annotations.php';
require_once 'Net/URL/Mapper.php';
require_once __DIR__ . '/Route.php';

class Encaminar
{

    const ANNOTATION = 'Route';

    protected $url;
    
    protected $httpMethod;

    public function setUrl($url)
    {
        $this->url = $url;
        return $this;
    } 

    public function setHttpMethod($httpMethod)
    {
        $this->httpMethod = strtoupper($httpMethod);
        return $this;
    }
    
    public function getUrl()
    {
        return $this->url;
    } 

    public function getHttpMethod()
    {
        return $this->httpMethod;
    }
    
    public function addRoute($method, $path, $httpMethods = array('GET'))
    {
        if (is_string($httpMethods)) {
            $httpMethods = $httpMethods === '*' ? 
                array('GET', 'POST', 'PUT', 'DELETE'):
                explode(',', $httpMethods);
        }
        $mapper = Net_URL_Mapper::getInstance();
        $match = array('method' => $method->getName());
        foreach ($httpMethods as $httpMethod) {
            $httpMethod = strtoupper($httpMethod);
            $mapper->connect("$httpMethod..$path", $match);
        }
    }

    public function __invoke($controller)
    {
        $this->parseRoutes($controller);
        return $this->dispatch($controller);
    }

    public function parseRoutes($controller)
    {
        $reflection = new ReflectionAnnotatedClass($controller);
        foreach ($reflection->getMethods() as $method) {
            if ($method->hasAnnotation(self::ANNOTATION)) {
                $annotation = $method->getAnnotation(self::ANNOTATION)->value;
                list($httpMethod, $path) = explode(' ', $annotation);
                $this->addRoute($method, $path, $httpMethod);
            }
        }
    }

    public function dispatch($controller)
    {
        $path = $this->getHttpMethod() . '..' . $this->getUrl();
        $match = Net_URL_Mapper::getInstance()->match($path);
        if ($match) {
            return $controller->$match['method']();
        }
    }
}

