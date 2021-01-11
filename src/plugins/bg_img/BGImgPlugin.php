<?php

namespace invacto\SimpleCMS\plugins\bg_img;


use invacto\SimpleCMS\plugins\Plugin;
use invacto\SimpleCMS\TemplateFunctionContext;

class BGImgPlugin extends Plugin
{

	public function setup()
	{
		$this->addAdminStyleFile(__DIR__ . "/bg_img_style.css");
		$this->addAdminScriptFile(__DIR__ . "/bg_img_script.js");

		$this->addTemplateFunction("bgImg", function (TemplateFunctionContext $context, array $args) {
			$name = $args[0];
			$defaultValue = $args[1];

			$src = $context->findSnippet($name, null);
			if ($src == null) {
				$src = $defaultValue;
			} else {
				$src = '/storage/' . $src;
			}

			if ($this->isLoggedIn()) {
				$res = 'style="background-image: url(\''.$src.'\')"';
				$res .= ' data-page="'.$context->getPageName().'"';
				$res .= ' data-name="'.$name.'"';
				$res .= ' data-simplecms-bg-img';
				$res .= ' data-src="'.$src.'"';
				return $res;
			} else {
				return 'style="background-image: url(\''.$src.'\')"';
			}
		});
	}

}