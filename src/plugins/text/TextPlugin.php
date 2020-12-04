<?php


namespace invacto\SimpleCMS\plugins\text;


use invacto\SimpleCMS\plugins\Plugin;
use invacto\SimpleCMS\plugins\PluginContext;

class TextPlugin extends Plugin
{

    public function setup()
    {
    	$this->addStyleFile(__DIR__ . "/text_style.css");
		$this->addScriptFile(__DIR__ . "/text_script.js");

		$this->addTemplateFunction("text", function (PluginContext $context, array $params) {
			$name = $params[0];
			$text = $context->findSnippet($name, $params[1]);
            if ($context->isLoggedIn()) {
                return '<span class="simplecms__editable" data-name="' . $name . '"  spellcheck="false">' . $text . '</span>';
            } else {
                return $text;
            }
		});
	}

}