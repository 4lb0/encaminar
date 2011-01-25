<?php

set_include_path(__DIR__ . PATH_SEPARATOR . get_include_path());

require_once __DIR__ . '/addendum/annotations.php';
require_once __DIR__ . '/Net/URL/Mapper.php';
require_once __DIR__ . '/Encaminar/Route.php';
require_once __DIR__ . '/Encaminar/NotFound.php';

class Encaminar
{

	const MATCH_METHOD_PARAM = '__METHOD';
	
    const ANNOTATION = 'Route';

    protected $url;
    
    protected $httpMethod;
    
    protected $basePath;
    
    protected $controller;
    
    public function __construct($controller = null)
    {
    	$this->controller = $controller;
    }

    public function setUrl($url)
    {
        $this->url = rtrim($url, '/');
        return $this;
    } 

    public function setHttpMethod($httpMethod)
    {
        $this->httpMethod = strtoupper($httpMethod);
        return $this;
    }

    public function setBasePath($path)
    {
        $this->basePath = rtrim($path, '/') . '/';
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
    
    public function getBasePath()
    {
    	return $this->basePath;
    }
    
    public function addRoute($method, $path, $httpMethods = array('GET'))
    {
        if (is_string($httpMethods)) {
            $httpMethods = $httpMethods === '*' ? 
                array('GET', 'POST', 'PUT', 'DELETE'):
                explode(',', $httpMethods);
        }
        $mapper = Net_URL_Mapper::getInstance();
        $match = array(self::MATCH_METHOD_PARAM => $method->getName());
        $path = $this->getBasePath() . rtrim($path, '/');
        $path = str_replace('//', '/', $path);
        foreach ($httpMethods as $httpMethod) {
            $httpMethod = strtoupper($httpMethod);
            $mapper->connect("$httpMethod..$path", $match);
        }
    }

    public function __invoke($controller = null)
    {
    	if (!$controller) {
    		$controller = $this->controller;
    	}
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
        $path = $this->getUrl();
        do {
        	$url = $this->getHttpMethod() . '..' . $path;
	        $match = Net_URL_Mapper::getInstance()->match($url);
	        if ($match) {
	        	$method = $match[self::MATCH_METHOD_PARAM];
	        	unset($match[self::MATCH_METHOD_PARAM]);
	            $response = $controller->$method($match);
	            if ($response instanceof self) {
	            	$response->setHttpMethod($this->getHttpMethod());
	            	$response->setUrl($this->getUrl());
	            	$response->setBasePath($path);
	            	return $response();
	            }
	            if (!isset($slashPos)) {
	            	return $response;
	            }	            
	        }
	        $slashPos = strrpos($path, '/');
	        $path = substr($path, 0, $slashPos);
        } while ($slashPos !== false);
        throw new Encaminar\NotFound();
    }
}

