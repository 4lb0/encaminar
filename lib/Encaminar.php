<?php
/**
 * Encaminar
 * 
 * PHP Version 5.3
 * 
 * @category Encaminar
 * @package  Encaminar
 * @author   Rodrigo Arce <rsarce@gmail.com>
 * @license  MIT License - https://github.com/4lb0/encaminar/
 * @link     https://github.com/4lb0/encaminar
 */

set_include_path(__DIR__ . PATH_SEPARATOR . get_include_path());

require_once __DIR__ . '/addendum/annotations.php';
require_once __DIR__ . '/Net/URL/Mapper.php';
require_once __DIR__ . '/Encaminar/Route.php';

/**
 * Encaminar
 * 
 * @category Encaminar
 * @package  Encaminar
 * @author   Rodrigo Arce <rsarce@gmail.com>
 * @license  MIT License - https://github.com/4lb0/encaminar/LICENSE
 * @link     https://github.com/4lb0/encaminar
 */
class Encaminar
{
    /**
     * Match param to set the method 
     * @var string
     */
    const MATCH_METHOD_PARAM = '__METHOD';
    /**
     * Annotation name
     * @var string
     */
    const ANNOTATION = 'Route';
    /**
     * Current URL to match
     * @var string
     */
    protected $url;
    /**
     * Current HTTP Method
     * @var unknown_type
     */
    protected $httpMethod;
    /**
     * Base path
     * @var string
     */
    protected $basePath;
    /**
     * Controller object
     * @var object
     */    
    protected $controller;
    /**
     * Constructor.
     * 
     * @param object $controller Controller object
     */
    public function __construct($controller = null)
    {
        $this->controller = $controller;
    }
    /**
     * Set the URL to match
     * 
     * @param string $url URL
     * 
     * @return Encaminar
     */
    public function setUrl($url)
    {
        $this->url = rtrim($url, '/');
        return $this;
    } 
    /**
     * Set the HTTP method
     * 
     * @param string $httpMethod HTTP method
     * 
     * @return Encaminar
     */
    public function setHttpMethod($httpMethod)
    {
        $this->httpMethod = strtoupper($httpMethod);
        return $this;
    }
    /**
     * Set the base path
     * 
     * @param string $path Base path
     * 
     * @return Encaminar
     */
    public function setBasePath($path)
    {
        $this->basePath = rtrim($path, '/') . '/';
        return $this;
    } 
    /**
     * Get the URL
     * 
     * @return string
     */
    public function getUrl()
    {
        return $this->url;
    } 
    /**
     * Get the HTTP method
     * 
     * @return string
     */
    public function getHttpMethod()
    {
        return $this->httpMethod;
    }
    /**
     * Get the base path
     * 
     * @return string
     */    
    public function getBasePath()
    {
        return $this->basePath;
    }
    /**
     * Add a route
     * 
     * @param string       $method      Controller's method to call later
     * @param string       $path        Path
     * @param array/string $httpMethods HTTP methods in array or comma separated
     * 
     * @return Encaminar
     */
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
            $mapper->connect($this->url($httpMethod, $path), $match);
        }
        return $this;
    }
    /**
     * Build the url to use in Net_URL_Mapper
     * 
     * @param string $httpMethod HTTP method
     * @param string $path       Path 
     * 
     * @return string
     */
    protected function url($httpMethod, $path)
    {
        return "$httpMethod..$path";
    } 
    /**
     * Parse and dispatch the routes
     * 
     * @param object $controller Controller object. If null use the controller 
     *                           passed in the constructor.
     * 
     * @throws Encaminar\NotFound
     * 
     * @return mixed
     */
    public function __invoke($controller = null)
    {
        if (!$controller) {
            $controller = $this->controller;
        }
        $this->parseRoutes($controller);
        return $this->dispatch($controller);
    }
    /**
     * Parse the routes of the given controller
     * 
     * @param object $controller Controller object.
     * 
     * @return Encaminar
     */
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
        return $this;
    }
    /**
     * Try to match the HTTP method and path given with the routes saved.
     * If match returns an array with the method and the params matched.
     * 
     * @param string $httpMethod Http method
     * @param string $path       Path
     * 
     * @return array
     */
    public function match($httpMethod, $path)
    {
        $url = $this->url($httpMethod, $path);
        $match = Net_URL_Mapper::getInstance()->match($url);
        if (!$match) {
            return array(false, false);
        }
        $method = $match[self::MATCH_METHOD_PARAM];
        unset($match[self::MATCH_METHOD_PARAM]);
        return array($method, $match);
    }
    /**
     * Dispatch the route matched with the controller.
     * If the controller match with no method try to match with the parent "folder" 
     * paths and if one of them returns another Encaminar instance try to dispatch 
     * that instance too.
     * 
     * @param object $controller Controller object.
     * 
     * @throws Encaminar\NotFound
     * 
     * @return mixed
     */
    public function dispatch($controller)
    {
        $path = $this->getUrl();
        do {
            list($method, $params) = $this->match($this->getHttpMethod(), $path);
            if ($method) {
                $response = $controller->$method($params);
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
        require_once __DIR__ . '/Encaminar/NotFoundException.php';
        throw new Encaminar\NotFoundException();
    }
}