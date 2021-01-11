<?php


namespace invacto\SimpleCMS\plugins\text;


use invacto\SimpleCMS\plugins\Plugin;
use invacto\SimpleCMS\TemplateFunctionContext;

class TextPlugin extends Plugin
{

    public function setup()
    {
    	$this->addAdminStyleFile(__DIR__ . "/text_style.css");
		$this->addAdminScriptFile(__DIR__ . "/text_script.js");

		$this->addTemplateFunction("text", function (TemplateFunctionContext $context, array $params) {
			$name = $params[0];
			$defaultValue = (isset($params[1]) ? $params[1] : "");
//			$global = isset($params[2]) && $params[2];

//			if ($global) {
//				$text = $context->findGlobalSnippet($name, $defaultValue);
////				$text = $context->findSnippet($name, $defaultValue);
//			} else
//			}
			$text = $context->findSnippet($name, $defaultValue);
			if ($context->isLoggedIn()) {
                return '<span
                	class="simplecms__editable"
                	data-page="'.$context->getPageName().'"
                	data-name="' . $name . '" 
                	spellcheck="false">' . $text . '</span>';
            } else {
                return $text;
            }
		});
	}

}