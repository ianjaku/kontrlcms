<?php

namespace invacto\KontrlCMS\plugins\wysiwyg;


use invacto\KontrlCMS\plugins\Plugin;
use invacto\KontrlCMS\TemplateFunctionContext;

class WYSIWYGPlugin extends Plugin
{

	public function setup()
	{
		$this->addAdminScriptFile(__DIR__ . "/dist/app.js");
		$this->addAdminStyleFile(__DIR__ . "/dist/style.css");

		$this->addTemplateFunction("wysiwyg", function (TemplateFunctionContext $context, $args) {
			$name = $args[0];
			$defaultValue = (sizeof($args) > 1 ? $args[1] : "");

			$text = $context->findSnippet($name, $defaultValue);
			if ($context->isLoggedIn()) {
				return '<div
					class="simplecms__editor"
					data-page="'.$context->getPageName().'"
					data-name="'.$name.'"
					>'.$text.'</div>';
			} else {
				return $text;
			}
		});
	}
}