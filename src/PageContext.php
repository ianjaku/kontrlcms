<?php

namespace invacto\KontrlCMS;


/**
 * Class PageContext
 * @package invacto\SimpleCMS
 *
 * Wrapper around the context received from twig.
 */
class PageContext
{
	private $context;

	public function __construct($context)
	{
		$this->context = $context;
	}

	public function findSnippet($name, $defaultValue, $globalFallback = false) {
		return Util::findSnippetInContext($this->context, $name, $defaultValue, $globalFallback);
	}

}