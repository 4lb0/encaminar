<?php

define('BASE_PATH', '/');

require_once '../lib/Encaminar.php';

class FrontController
{
    /** @Route("GET /") */
    public function index()
    {
        $this->_render("Welcome!");
    }
    /** @Route("GET /hello/:name") */
    public function hello($params)
    {
        $this->_render("Hello " . $params['name']);
    }
    
    private function _render($message)
    {
    	$base = BASE_PATH;
		echo <<<EOHTML
		<html>
		<body>
			<h1>Encaminar</h1>
			<p>A PHP routing library</p>
			<a href="$base">Home</a>
			<a href="$base/hello/world">Hello world</a>
			<a href="$base/hello/hello">Hello hello</a>
			<p><strong>$message</strong></p>
		</body>
EOHTML;
    }
}

$encaminar = new Encaminar();
$encaminar->setBasePath(BASE_PATH)
          ->setUrl($_SERVER['REQUEST_URI'])
          ->setHttpMethod($_SERVER['REQUEST_METHOD']);

$encaminar(new FrontController);
