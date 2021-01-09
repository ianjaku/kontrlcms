<?php

namespace invacto\SimpleCMS;


use invacto\SimpleCMS\auth\Authenticator;
use invacto\SimpleCMS\plugins\Plugin;
use invacto\SimpleCMS\repos\Database;
use invacto\SimpleCMS\repos\SnippetRepo;
use Slim\App;
use Slim\Factory\AppFactory;
use Twig\Environment;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Error\SyntaxError;
use Twig\Loader\FilesystemLoader;

class AppContext
{
	private $appDir;
	private $imageDirectory;

	/**
	 * @var Environment Twig environment
	 */
	private $twig;

	/**
	 * @var App
	 */
	private $app;

	/**
	 * @var Database
	 */
	private $db;

	/**
	 * @var Authenticator
	 */
	private $authenticator;

	/**
	 * @var Plugin[]
	 */
	private $plugins;

	public function __construct($appDir)
	{
		$this->appDir = $appDir;
		$this->imageDirectory = $appDir . "/public/storage";
		$this->twig = $this->createTwig($appDir);
		$this->app = AppFactory::create();
		$this->db = $this->createDb();
		$this->authenticator = new Authenticator($_ENV["SECRET_KEY"]);
		$this->plugins = [];
	}

	public function getTwig() {
		return $this->twig;
	}

	public function getApp() {
		return $this->app;
	}

	public function getImageDir() {
		return $this->imageDirectory;
	}

	public function getAuthenticator() {
		return $this->authenticator;
	}

	public function addPlugin(Plugin $plugin) {
		$this->plugins[] = $plugin;
	}

	public function getPlugins() {
		return $this->plugins;
	}

	/**
	 * Renders a user page with the given context and settings
	 *
	 * @param string $page
	 * @param array $context
	 * @param array $settings (optional) in format ["beforeRender" => (Function($context): $context)]
	 *
	 * @return string
	 * @throws LoaderError
	 * @throws RuntimeError
	 * @throws SyntaxError
	 */
	public function renderPage(string $page, $context = [], $settings = []) {
		if (isset($settings["beforeRender"])) {
			$context = $settings["beforeRender"]($context);
		}

		return $this->getTwig()->load($page)->render($context);
	}

	/**
	 * Fetch the context required to render a page
	 *
	 * @param string $page
	 * @param array $extraContext
	 * @param array $settings in format ["customPageName?" => "..."]
	 *
	 * @return array
	 */
	public function fetchPageContext(string $page, $extraContext = [], $settings = []) {
		$snippets = SnippetRepo::allForPage($page);

		$context["__pageFile"] = (isset($settings["customPageName"]) ? $settings["customPageName"] : $page);
		$context["__snippets"] = $snippets;

		return array_merge($context, $extraContext);
	}

	private function createTwig($appDir) {
		$fileSystemLoader = new FilesystemLoader($appDir . "/views");
		return new Environment($fileSystemLoader, [
//        	"cache" => $appDir . "/cache"
		]);
	}

	private function createDb() {
		// TODO: throw error when environment variables are not set
		return new Database(
            $_ENV["DB_HOST"] . ":" . $_ENV["DB_PORT"],
            $_ENV["DB_NAME"],
            $_ENV["DB_USER"],
            $_ENV["DB_PASS"]
        );
	}

}