<?php

namespace invacto\SimpleCMS\plugins;


use invacto\SimpleCMS\auth\Authenticator;
use Twig\Environment;
use Twig\TwigFilter;
use Twig\TwigFunction;

abstract class Plugin
{
    public $scriptFunctions = [];
    public $adminScriptFunctions = [];

    public $styleFunctions = [];
    public $adminStyleFunctions = [];

    public $functions = [];
    /**
     * @var Authenticator
     */
    public $authenticator = null;

    public abstract function setup();

//    public function setup() {
////        $this->addScript(function() {
////            return $this->readFile(__DIR__ . "/testscript.js");
////        });
//        $this->addScriptFile(__DIR__ . "/testscript.js");
//        $this->addStyleFile(__DIR__ . "/teststyle.css");
//
////        $this->addScriptFile(__DIR__ . "/testscript.js");
//        $this->registerTemplateFunction("checkLogin", function (PluginContext $context, array $params) {
//            if ($context->isLoggedIn()) {
//                return 'Logged in';
//            } else {
//                return 'Not Logged in';
//            }
//        });
//    }

    public function addStyle(callable $styleFunction) {
        $this->styleFunctions[] = $styleFunction;
    }

    public function addAdminStyle(callable $styleFunction) {
        $this->adminStyleFunctions[] = $styleFunction;
    }

    public function addStyleFile($path) {
        $this->addStyle(function () use ($path) {
            return $this->readFile($path);
        });
    }

    public function addAdminStyleFile($path) {
        $this->addAdminStyle(function () use ($path) {
            return $this->readFile($path);
        });
    }

    public function addScript(callable $scriptFunction) {
        $this->scriptFunctions[] = $scriptFunction;
    }

    public function addAdminScript(callable $scriptFunction) {
        $this->adminScriptFunctions[] = $scriptFunction;
    }

    public function readFile($path) {
        return file_get_contents($path);
    }

    public function addScriptFile($filePath) {
        $this->addScript(function () use ($filePath) {
            return $this->readFile($filePath);
        });
    }

    public function addAdminScriptFile($filePath) {
        $this->addAdminScript(function () use ($filePath) {
            return $this->readFile($filePath);
        });
    }

    public function addTemplateFunction(string $name, $func) {
        $this->functions[] = function (Environment $twig) use ($name, $func) {
            $twig->addFunction(new TwigFunction($name, function ($context, array $params = []) use ($func) {
                $pluginContext = new PluginContext($context, $this->authenticator);
                return call_user_func($func, $pluginContext, $params);
            }, ["is_safe" => ["html"], "needs_context" => true, "is_variadic" => true]));

            $twig->addFilter(new TwigFilter($name, function ($context, $content, array $params = []) use ($func) {
            	$params[] = $content;
				$pluginContext = new PluginContext($context, $this->authenticator);
				return call_user_func($func, $pluginContext, $params);
			}, ["is_safe" => ["html"], "needs_context" => true, "is_variadic" => true]));
        };
    }

}