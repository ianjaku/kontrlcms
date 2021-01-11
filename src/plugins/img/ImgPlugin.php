<?php

namespace invacto\KontrlCMS\plugins\img;


use invacto\KontrlCMS\plugins\Plugin;
use invacto\KontrlCMS\TemplateFunctionContext;

class ImgPlugin extends Plugin
{

	public function setup()
	{
		$this->addAdminStyleFile(__DIR__ . "/img_style.css");
		$this->addAdminScriptFile(__DIR__ . "/img_script.js");

		$this->addTemplateFunction('img', function (TemplateFunctionContext $context, array $params) {
			$name = $params[0];
			$defaultValue = isset($params[1]) ? $params[1] : "";
			$altDefault = isset($params[2]) ? $params[2] : "";
			$other = isset($params[3]) ? $params[3] : "";

			$src = $context->findSnippet($name, null);
			$alt = $context->findSnippet($name . "__alt", $altDefault);
			if ($src == null) {
				$src = $defaultValue;
			} else {
				$src = '/storage/' . $src;
			}
			if ($this->isLoggedIn()) {
				return '<img src="'.$src.'" alt="'.$alt.'" data-simplecms-img data-page="'.$context->getPageName().'" data-name="'.$name.'" '.$other.'>';
			} else {
				return '<img src="'.$src.'" alt="'.$alt.'" '.$other.'>';
			}
		});
	}

}