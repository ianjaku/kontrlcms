<?php


namespace invacto\SimpleCMS\nodes;


use Twig\Compiler;
use Twig\Node\Node;

class AddGroupNode extends Node
{
	public function __construct(Node $names, Node $values, int $lineno, string $tag = null)
	{
		parent::__construct(['names' => $names, 'values' => $values], ['safe' => false], $lineno, $tag);
	}

	public function compile(Compiler $compiler)
	{
		$compiler->addDebugInfo($this);

		// ('' === $tmp = [VAL])
		$compiler
			->raw("('' === \$tmp = ")
			->subcompile($this->getNode('values'))
			->raw(") ? '' : new Markup(\$tmp, \$this->env->getCharset())")
		;


		$compiler->raw("new Markup(\$tmp");

		$compiler->raw(";\n");
	}


}