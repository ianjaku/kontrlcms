<?php

namespace invacto\SimpleCMS\plugins\wysiwyg;


use invacto\SimpleCMS\plugins\Plugin;

class WYSIWYGPlugin extends Plugin
{

	public function setup()
	{
		$this->addAdminScriptFile(__DIR__ . "/dist/app.js");
		$this->addAdminStyleFile(__DIR__ . "/dist/style.css");
	}
}