<?php

namespace invacto\SimpleCMS;


class TemplateFunctionContext
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

    public function findSnippet($name, $valueIfNotFound = null, $globalFallback = false) {
    	if ($this->appContext->areNamesGlobal()) {
    		return $this->findGlobalSnippet($name, $valueIfNotFound);
		}
    	return Util::findSnippetInContext($this->twigContext, $name, $valueIfNotFound, $globalFallback);
    }

    public function findGlobalSnippet($name, $valueIfNotFound = null) {
    	return Util::findGlobalSnippetInContext($this->twigContext, $name, $valueIfNotFound);
	}

	public function getPageName() {
    	if ($this->appContext->areNamesGlobal()) {
    		return "__global__";
		}
    	return $this->twigContext["__pageFile"];
	}
}