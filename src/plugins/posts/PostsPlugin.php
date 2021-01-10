<?php


namespace invacto\SimpleCMS\plugins\posts;


use Exception;
use invacto\SimpleCMS\plugins\Plugin;
use invacto\SimpleCMS\plugins\PluginContext;
use invacto\SimpleCMS\repos\SnippetRepo;
use invacto\SimpleCMS\RequestHelper;

class PostsPlugin extends Plugin
{

	/**
	 * @var PostsSettings
	 */
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
	 * 			"slug" => "post_title", (The snippet name on which it's based)
	 * 			"prefix?" => "/blog",
	 * 		]
	 *  ]
	 */
	public function __construct($settings)
	{
		$this->settings = new PostsSettings($settings);
	}

	public function setup()
	{
		$this->addAdminScriptFile(__DIR__ . "/posts_script.js");

		foreach ($this->settings->getAll() as $postSettings) {
			$this->initResource($postSettings);
		}

		if ($this->isLoggedIn()) {
			$settings = $this->settings;
			$this->addHook("head", function ($content) use ($settings) {
				$jsDataJSON = json_encode($settings->getJSData());
				return $content . "
					<script>
						const _POSTS_SETTINGS = `$jsDataJSON`;
					</script>
				";
			});
		}

		/**
		 * Params:
		 *  0: Post Type ex: "blog",
		 * 	1: Max Post Count (default: 20) "blog"
		 */
		$settings = $this->settings;
		$this->addTemplateFunction("recent_posts", function (PluginContext $context, $params = []) use ($settings) {
			if (sizeof($params) < 1) {
				throw new Exception("Requires at least 1 parameter to the 'recent_posts' function");
			}

			$postType = $params[0];
			$count = (isset($params[1]) ? intval($params[2]) : 20);

			$postTypeSettings = $settings->getSettingsFor($postType);
			if ($postTypeSettings == null) {
				throw new Exception("Post type with name $postType does not exist. (from recent_posts call)");
			}
			$normalizedPostType = $postTypeSettings->getPageNamePrefix();
			$snippets = SnippetRepo::customQuery()
				->where("page", "LIKE", $normalizedPostType . "%")
				->orderBy("inserted_at", "desc")
				->limit($count)
				->get();

			return $this->postsFromSnippets($snippets, $postTypeSettings);
		});

		/**
		 * Params:
		 * 0: Post (from recent_posts for example)
		 * 1: Name (like "post_title")
		 * 2: Default value (optional)
		 */
		$this->addTemplateFunction("post_value", function (PluginContext $context, $params = []) {
			if (sizeof($params) < 2) {
				throw new Exception("The 'post_value' requires at least 2 parameters 'post' and 'name'.");
			}
			$post = $params[0];
			$name = $params[1];
			if (isset($post[$name])) {
				return $post[$name];
			}
			if (isset($params[2])) {
				return $params[2];
			}
			return "";
		});

		$this->addTemplateFunction("post_url", function (PluginContext $context, $params = []) {
			if (sizeof($params) < 1) {
				throw new Exception("The 'post_link' requires at least 1 parameters 'post'.");
			}

			$post = $params[0];
			$postSettings = $this->settings->getSettingsFor($post["__postType"]);
			if ($postSettings == null) throw new Exception("Could find the postSettings for the given postType");

			$postId = $postSettings->getIdFromPageName($post["__pageName"]);
			$slug = $postSettings->getSlugFor($post);

			return $postSettings->getPageUrl($postId, $slug);
		});
	}

	/**
	 * Brings snippets into single posts
	 *
	 * @param $snippets
	 * @param PostSettings $settings
	 * @return array ["__posts.blog.id.." => ["post_title" => "..."]]
	 */
	private function postsFromSnippets($snippets, PostSettings $settings) {
		$posts = [];
		foreach ($snippets as $snippet) {
			if (!isset($posts[$snippet->page])) {
				$posts[$snippet->page] = [
					"__postType" => $settings->getName(),
					"__postUrl" => $settings->getPageUrl(),
					"__pageName" => $snippet->page
				];
			}
			$posts[$snippet->page][$snippet->name] = $snippet->value;
		}
		return $posts;
	}

	private function initResource(PostSettings $settings) {
		$appContext = $this->appContext;
		$this->handleRequest("GET", $settings->getPageUrl(), function (RequestHelper $helper) use ($settings, $appContext) {
			$params = $helper->getUrlParameters();
			$postId = $params["id"];
			$pageName = $settings->getPageName($postId);

			$pageData = [
				"__post__.id" => $postId,
				"__post__.slug" => $params["slug"],
				"__post__.settings" => $settings
			];
			$context = $appContext->fetchPageContext($pageName, $pageData, ["customPageName" => $pageName]);

			if (!$this->isLoggedIn() && sizeof($context["snippets"]) === 0) {
				return $helper->notFound();
			}
			return $appContext->renderPage($settings->getView(), $context);
		});

		return $settings;
	}

}