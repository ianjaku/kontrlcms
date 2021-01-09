<?php

namespace invacto\SimpleCMS\plugins\wysiwyg;


use invacto\SimpleCMS\plugins\Plugin;
use invacto\SimpleCMS\plugins\PluginContext;

class WYSIWYGPlugin extends Plugin
{

	public function setup()
	{
		$this->addAdminScriptFile(__DIR__ . "/dist/app.js");
		$this->addAdminStyleFile(__DIR__ . "/dist/style.css");

		$this->addTemplateFunction("wysiwyg", function (PluginContext $context, $args) {
			$name = $args[0];
			$defaultValue = (sizeof($args) > 1 ? $args[1] : "");

			$text = $context->findSnippet($name, $defaultValue);
			if ($context->isLoggedIn()) {
				return '<div class="simplecms__editor" data-simplecms-name="'.$name.'">'.$text.'</div>';
			} else {
				return $text;
			}
		});
	}
}