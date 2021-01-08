<?php

namespace invacto\SimpleCMS;


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

	public function findSnippet($name, $defaultValue) {
		$snippets = $this->context['__snippets'];

		$value = $defaultValue;
		foreach ($snippets as $snippet) {
			if ($snippet->name === $name) {
				$value = $snippet->value;
				break;
			}
		}
		return $value;
	}

}