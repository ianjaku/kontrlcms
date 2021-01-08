<?php


namespace invacto\SimpleCMS;


class Util
{

	public static function findSnippetInContext($context, $name, $defaultValue) {
		$snippets = $context['__snippets'];

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