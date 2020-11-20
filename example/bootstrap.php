<?php

require_once __DIR__ . '/../vendor/autoload.php';

use invacto\SimpleCMS\RequestHelper;
use invacto\SimpleCMS\SimpleCMS;

//$cms = new SimpleCMS(__DIR__ . "/views", __DIR__ . "/public/storage");
$cms = new SimpleCMS(__DIR__);

$cms->page("/", "home.twig");


$cms->page("/about", "about.twig");
//$cms->handleRequest("GET", "/about/test", function (RequestHelper $helpers) {
//    return $helpers->redirect("/about");
//});

$cms->redirect("/login", "/simplecms/login");

$cms->run();
