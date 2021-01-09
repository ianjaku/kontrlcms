<?php


namespace invacto\SimpleCMS\plugins\posts;


use invacto\SimpleCMS\plugins\Plugin;
use invacto\SimpleCMS\repos\SnippetRepo;
use invacto\SimpleCMS\RequestHelper;

class PostsPlugin extends Plugin
{

	private $settings;

	/**
	 * PostsPlugin constructor.
	 *
	 * @param $settings array The postsplugin settings in the format below
	 *
	 * SettingsFormat (every name with ? after it is optional):
	 *  [
	 *  	[
	 * 			"name" => "blog",
	 * 			"view" => "myPost.twig",
	 * 			"prefix?" => "/blog",
	 * 		]
	 *  ]
	 */
	public function __construct($settings)
	{
		$this->settings = $settings;
	}

	public function setup()
	{
		$this->addAdminScriptFile(__DIR__ . "/posts_script.js");

		foreach ($this->settings as $resourceSettingsData) {
			$this->initResource($resourceSettingsData);
		}
	}

	private function initResource($resourceSettingsData) {
		$settings = new PostSettings($resourceSettingsData);

		if ($this->isLoggedIn()) {
			$this->addHook("head", function ($content) use ($settings) {
				$jsData = json_encode($settings->getJavascriptData());
				return $content . "
					<script>
						const _POSTS_SETTINGS = `$jsData`;
					</script>
				";
			});
		}

		$appContext = $this->appContext;
		$this->handleRequest("GET", $settings->getPageUrl(), function (RequestHelper $helper) use ($settings, $appContext) {
			$params = $helper->getUrlParameters();
			$postId = $params["id"];
			$pageName = $settings->getPageName($postId);

			// TODO: Throw 404 if no data can be found

			$pageData = [
				"__post__.id" => $postId,
				"__post__.slug" => $params["slug"],
				"__post__.settings" => $settings
			];
			$context = $appContext->fetchPageContext($pageName, $pageData, ["customPageName" => $pageName]);
			// Check if context is empty (other than the parts we added ourselves ofc)
			return $appContext->renderPage($settings->getView(), $context);
		});
	}

}