<?php


namespace invacto\SimpleCMS\plugins;


use invacto\SimpleCMS\auth\Authenticator;
use invacto\SimpleCMS\Util;

class PluginContext
{
    private $twigContext;

    /**
     * @var Authenticator
     */
    private $authenticator;

    public function __construct($twigContext, Authenticator $authenticator)
    {
        $this->twigContext = $twigContext;
        $this->authenticator = $authenticator;
    }

    public function isLoggedIn() {
        return $this->authenticator->hasUser();
    }

    public function findSnippet($name, $valueIfNotFound = null) {
    	return Util::findSnippetInContext($this->twigContext, $name, $valueIfNotFound);
    }
}