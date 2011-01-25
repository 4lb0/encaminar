<?php

require_once '../lib/Encaminar.php';

class FrontController
{
    /** @Route("GET /") */
    public function index()
    {
        return "Home";
    }
    /** @Route("GET /hello") */
    public function hello()
    {
        return "Hello";
    }
}

$encaminar = new Encaminar();
$url = strrchr($_SERVER['REQUEST_URI'], '/~albo/pragmore/2011/rubra/logistica/workspace/Encaminar/demo');
$encaminar->setUrl($url)->setHttpMethod($_SERVER['REQUEST_METHOD']);
echo $encaminar(new FrontController);
