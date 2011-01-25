<?php

$steps->Given('/^a class "([^"]*)" with methods "(.*)"$/', function($world, $class, $methods) {
	eval("class $class { $methods }");
});

$steps->When('/^I set the url "([^"]*)"$/', function($world, $url) {
    $world->encaminar->setUrl($url);
});

$steps->And('/^I set the method "([^"]*)"$/', function($world, $method) {
    $world->encaminar->setHttpMethod($method);
});

$steps->And('/^I set up the front controller as "([^"]*)"$/', function($world, $class) {
    $world->frontController = $class;
});

$steps->Then('/^the output should be "([^"]*)"$/', function($world, $output) {
    assertEquals($output, $world->encaminar(new $world->frontController));
});

$steps->Then('/^the exception should be "([^"]*)"$/', function($world, $exception) {
    try {
    	$world->encaminar(new $world->frontController);
    } catch (Exception $e) {
    	assertEquals($exception, get_class($e));
    	return;
    }
    throw new Exception("An exception '$exception' was expected");
});

