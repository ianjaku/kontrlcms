<?php

namespace invacto\SimpleCMS\plugins\bg_img;


use invacto\SimpleCMS\plugins\Plugin;
use invacto\SimpleCMS\plugins\PluginContext;
use Twig\TwigFunction;

class BGImgPlugin extends Plugin
{

	public function setup()
	{
		$this->addAdminStyleFile(__DIR__ . "/bg_img_style.css");
		$this->addAdminScriptFile(__DIR__ . "/bg_img_script.js");

		$this->addTemplateFunction("bgImg", function (PluginContext $context, array $args) {
			$name = $args[0];
			$defaultValue = $args[1];

			$src = $context->findSnippet($name, null);
			if ($src == null) {
				$src = $defaultValue;
			} else {
				$src = '/storage/' . $src;
			}

			if ($this->authenticator->hasUser()) {
				return 'style="background-image: url(\''.$src.'\')" data-simplecms-bg-image="'.$name.'" data-simplecms-bg-src="'.$src.'"';
			} else {
				return 'style="background-image: url(\''.$src.'\')"';
			}
		});
	}

}