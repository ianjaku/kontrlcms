<?php


namespace invacto\KontrlCMS;


class Util
{

	/**
	 * Given the TwigContext and the name of a snippet will find and return the snippet value
	 *
	 * @param $context
	 * @param $name
	 * @param $defaultValue
	 * @param bool $globalFallback If the snippet is not found, will look whether it exists as a global snippet
	 * 							   (Global snippets act for all pages)
	 * @return string or defaultValue
	 */
	public static function findSnippetInContext($context, $name, $defaultValue = null, $globalFallback = false) {
		$snippets = $context['__snippets'];

		$value = $defaultValue;
		$globalValue = null;
		foreach ($snippets as $snippet) {
			if ($snippet->name === $name) {
				if ($snippet->page === "__global__") {
					$globalValue = $snippet->value;
				} else {
					return $snippet->value;
//					$value = $snippet->value;
				}
				break;
			}
		}
		if ($globalValue !== null && $globalFallback) {
			return $globalValue;
		}
		return $value;
	}

	public static function findGlobalSnippetInContext($context, $name, $defaultValue = null) {
		$snippets = $context["__snippets"];
		foreach ($snippets as $snippet) {
			if ($snippet->page === "__global__" && $snippet->name === $name) {
				return $snippet->value;
			}
		}
		return $defaultValue;
	}

}