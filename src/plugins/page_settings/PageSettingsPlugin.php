<?php

namespace invacto\KontrlCMS\plugins\page_settings;


use invacto\KontrlCMS\PageContext;
use invacto\KontrlCMS\plugins\Plugin;

class PageSettingsPlugin extends Plugin
{
	public function setup()
	{
		$this->addAdminScriptFile(__DIR__ .  "/page_settings_script.js");

		$this->addHook("head", function ($headContent, PageContext $pageContext) {
			$defaultValue = (isset($_ENV["APP_NAME"]) ? $_ENV["APP_NAME"] : "Kontrl CMS App");
			$title = $pageContext->findSnippet("__page-settings.page_title", $defaultValue, true);
			$desc = $pageContext->findSnippet("__page-settings.page_description", "", true);
			$keywords = $pageContext->findSnippet("__page-settings.page_keywords", "", true);

			return $headContent . "
				<meta property=\"og:title\" content=\"$title\" />
				<meta property=\"og:type\" content=\"website\" />
				<meta property=\"og:description\" content=\"$desc\" />
				<title>$title</title>
				<meta name=\"description\" content=\"$desc\">
				<meta name=\"keywords\" content=\"$keywords\">
			";
		});
	}

}