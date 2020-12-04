<?php


namespace invacto\SimpleCMS\plugins;


use invacto\SimpleCMS\auth\Authenticator;

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
        $snippets = $this->twigContext['__snippets'];

        $value = $valueIfNotFound;
        foreach ($snippets as $snippet) {
            if ($snippet['name'] === $name) {
                $value = $snippet['value'];
                break;
            }
        }
        return $value;
    }
}