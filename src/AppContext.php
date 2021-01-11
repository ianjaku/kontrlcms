<?php

namespace invacto\SimpleCMS;


use invacto\SimpleCMS\auth\Authenticator;
use invacto\SimpleCMS\plugins\Plugin;
use invacto\SimpleCMS\TemplateFunctionContext;
use invacto\SimpleCMS\repos\Database;
use invacto\SimpleCMS\repos\SnippetRepo;
use Monolog\Handler\StreamHandler;
use Monolog\Logger;
use Slim\App;
use Slim\Factory\AppFactory;
use Twig\Environment;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Error\SyntaxError;
use Twig\Loader\FilesystemLoader;
use Twig\TwigFilter;
use Twig\TwigFunction;

class AppContext
{
	private $appDir;
	private $imageDirectory;

	/**
	 * @var array block strings in format (assuming head is a block) ["head" => ""]
	 */
	public $hooks = [];

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

	/**
	 * @var string
	 */
	private $errorPage = null;

	/**
	 * @var bool Make all snippets global
	 */
	private $globalNames = false;

//	private $twigFunctions = [];

	/**
	 * @var Logger
	 */
	private $logger;

	public function __construct($appDir)
	{
		$this->appDir = $appDir;
		$this->imageDirectory = $appDir . "/public/storage";
		$this->twig = $this->createTwig($appDir);
		$this->app = AppFactory::create();
		$this->db = $this->createDb();
		$this->authenticator = new Authenticator($_ENV["SECRET_KEY"]);
		$this->plugins = [];
		$this->logger = new Logger("general");
		$this->logger->pushHandler(new StreamHandler($appDir . "/errors.log", Logger::WARNING));
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

	public function setErrorPage($errorPage) {
		$this->errorPage = $errorPage;
	}

	public function getErrorPage() {
		return $this->errorPage;
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

	public function addHook(string $name, callable $callback) {
		if (!isset($this->hooks[$name])) {
			$this->hooks[$name] = [$callback];
		} else {
			$this->hooks[$name][] = $callback;
		}
	}

	public function getHooksFor($name){
		return $this->hooks[$name];
	}

	public function addTemplateFunction(string $name, $func) {
//		$this->twigFunctions[] = function (Environment $twig) use ($name, $func) {
		$this->twig->addFunction(new TwigFunction($name, function ($context, array $params = []) use ($func) {
			 $pluginContext = new TemplateFunctionContext($context, $this);
			 return call_user_func($func, $pluginContext, $params);
		}, ["is_safe" => ["html"], "needs_context" => true, "is_variadic" => true]));

		$this->twig->addFilter(new TwigFilter($name, function ($context, $content, array $params = []) use ($func) {
			 $params[] = $content;
			 $pluginContext = new TemplateFunctionContext($context, $this);
			 return call_user_func($func, $pluginContext, $params);
		}, ["is_safe" => ["html"], "needs_context" => true, "is_variadic" => true]));
//		};
	}

	public function setGlobalNames(bool $val) {
		$this->globalNames = $val;
	}

	public function areNamesGlobal() {
		return $this->globalNames;
	}

	public function getLogger() {
		return $this->logger;
	}

	public function getDB() {
		return $this->db;
	}

}