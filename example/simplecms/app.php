<?php

require_once __DIR__ . '/../../vendor/autoload.php';

use invacto\SimpleCMS\SimpleCMS;

$cms = new SimpleCMS(__DIR__ . "/..");

$cms->page("/", "home.twig");
$cms->page("/about", "about.twig");

$cms->redirect("/login", "/simplecms/login");

$cms->addPlugin(new \invacto\SimpleCMS\plugins\text\TextPlugin());
$cms->addPlugin(new \invacto\SimpleCMS\plugins\img\ImgPlugin());
$cms->addPlugin(new \invacto\SimpleCMS\plugins\bg_img\BGImgPlugin());

$cms->run();
