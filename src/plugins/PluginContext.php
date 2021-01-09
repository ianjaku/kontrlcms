<?php

namespace invacto\SimpleCMS\plugins;


use invacto\SimpleCMS\AppContext;
use invacto\SimpleCMS\Util;

class PluginContext
{
    private $twigContext;

	/**
	 * @var AppContext
	 */
    private $appContext;

    public function __construct($twigContext, AppContext $appContext)
    {
        $this->twigContext = $twigContext;
        $this->appContext = $appContext;
    }

    public function isLoggedIn() {
        return $this->appContext->getAuthenticator()->hasUser();
    }

    public function findSnippet($name, $valueIfNotFound = null) {
    	return Util::findSnippetInContext($this->twigContext, $name, $valueIfNotFound);
    }
}