<?php

namespace invacto\SimpleCMS\plugins\raw_value;


use Exception;
use invacto\SimpleCMS\plugins\Plugin;
use invacto\SimpleCMS\TemplateFunctionContext;

class ValuePlugin extends Plugin
{

	public function setup()
	{
		/**
		 * Params:
		 * 0: Name (snippet name)
		 * 1: DefaultValue
		 */
		$this->addTemplateFunction("value", function (TemplateFunctionContext $pluginContext, $params = []) {
			if (sizeof($params) < 1) {
				throw new Exception("'value' function needs at least 1 parameter (the snippet name)");
			}
			$name = $params[0];
			$defaultValue = (isset($params[1]) ? $params[1] : "");
			return $pluginContext->findSnippet($name, $defaultValue);
		});
	}

}