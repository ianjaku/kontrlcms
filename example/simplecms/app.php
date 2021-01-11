<?php

require_once __DIR__ . '/../../vendor/autoload.php';

use invacto\KontrlCMS\KontrlCMS;

$cms = new KontrlCMS(__DIR__ . "/..");

$cms->page("/", "home.twig");
$cms->page("/about", "about.twig");
$cms->page("/blog", "posts.twig");

$cms->redirect("/login", "/kontrlcms/login");

$cms->addPlugin(new \invacto\KontrlCMS\plugins\text\TextPlugin());
$cms->addPlugin(new \invacto\KontrlCMS\plugins\img\ImgPlugin());
$cms->addPlugin(new \invacto\KontrlCMS\plugins\bg_img\BGImgPlugin());
$cms->addPlugin(new \invacto\KontrlCMS\plugins\wysiwyg\WYSIWYGPlugin());
$cms->addPlugin(new \invacto\KontrlCMS\plugins\page_settings\PageSettingsPlugin());
$cms->addPlugin(new \invacto\KontrlCMS\plugins\posts\PostsPlugin([
	[
		"name" => "blog",
		"view" => "post.twig",
		"slug" => "post_title"
	]
]));

$cms->run();
