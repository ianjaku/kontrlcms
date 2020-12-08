<?php


namespace invacto\SimpleCMS\plugins\bg_img;


use invacto\SimpleCMS\plugins\Plugin;
use invacto\SimpleCMS\plugins\PluginContext;
use Twig\TwigFunction;

class BGImgPlugin extends Plugin
{

	public function setup()
	{
		$this->addStyleFile(__DIR__ . "/bg_img_style.css");
		$this->addScriptFile(__DIR__ . "/bg_img_script.js");

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
				return 'style="background-image: url(\''.$src.'\')" data-simplecms-bg-image="'.$name.'"';
			} else {
				return 'style="background-image: url(\''.$src.'\')"';
			}
		});
	}

}