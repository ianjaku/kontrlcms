<?php

require_once __DIR__ . '/../vendor/autoload.php';

use invacto\SimpleCMS\SimpleCMS;

$cms = new SimpleCMS(__DIR__ . "/views", __DIR__ . "/public/storage");

$cms->page("/", "home.twig");
$cms->page("/about", "about.twig");

$cms->run();
