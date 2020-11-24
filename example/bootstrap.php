<?php

require_once __DIR__ . '/../vendor/autoload.php';

use invacto\SimpleCMS\SimpleCMS;

$cms = new SimpleCMS(__DIR__);

$cms->page("/", "home.twig");
$cms->page("/about", "about.twig");

$cms->redirect("/login", "/simplecms/login");

$cms->run();
