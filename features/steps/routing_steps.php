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
    assertEquals($world->encaminar(new $world->frontController), $output);
});


