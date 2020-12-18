<?php


namespace invacto\SimpleCMS;


use Twig\Node\Node;

class TestNode extends Node
{
//	public function __construct($name, \Twig\Node\Expression\AbstractExpression $value, $line, $tag = null)
	public function __construct($name, Node $value, int $lineno, $tag = null)
	{
		parent::__construct(['value' => $value], ['name' => $name], $lineno, $tag);
	}

	public function compile(\Twig\Compiler $compiler)
	{
		$compiler
			->addDebugInfo($this)
			->write('$context[\''.$this->getAttribute('name').'\'] = ')
			->subcompile($this->getNode('value'))
			->raw(";\n")
		;
	}
}