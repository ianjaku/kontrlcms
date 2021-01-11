<?php

namespace invacto\KontrlCMS\plugins;


use invacto\KontrlCMS\AppContext;

abstract class Plugin
{
    public $scriptFunctions = [];
    public $adminScriptFunctions = [];

    public $styleFunctions = [];
    public $adminStyleFunctions = [];

    public $handledRequests = [];

	/**
	 * @var AppContext
	 */
    public $appContext;

	public abstract function setup();

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

    public function handleRequest(string $type, string $path, callable $callback) {
    	$this->handledRequests[] = [$type, $path, $callback];
	}

    public function addTemplateFunction(string $name, $func) {
    	$this->appContext->addTemplateFunction($name, $func);
    }

    public function isLoggedIn() {
    	return $this->appContext->getAuthenticator()->hasUser();
	}

    public function addHook(string $name, callable $callback) {
    	$this->appContext->addHook($name, $callback);
	}

}