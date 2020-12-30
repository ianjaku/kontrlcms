<?php


namespace invacto\SimpleCMS;

use Twig\Markup;
use Twig\Node\Expression\TempNameExpression;
use Twig\Node\Node;
use Twig\Node\PrintNode;
use Twig\Node\SetNode;
use Twig\Token;
use Twig\TokenParser\AbstractTokenParser;


class MyApplyParser extends AbstractTokenParser
{
	public function parse(Token $token): Node
	{
		$lineno = $token->getLine();
		$name = $this->parser->getVarName();

		$ref = new TempNameExpression($name, $lineno);
		$ref->setAttribute('always_defined', true);

		$filter = $this->parser->getExpressionParser()->parseFilterExpressionRaw($ref, $this->getTag());

		$this->parser->getStream()->expect(Token::BLOCK_END_TYPE);
		$body = $this->parser->subparse([$this, 'decideApplyEnd'], true);
		$this->parser->getStream()->expect(Token::BLOCK_END_TYPE);

		return new Node([
			new SetNode(true, $ref, $body, $lineno, $this->getTag()),
			new PrintNode($filter, $lineno, $this->getTag()),
		]);
	}

	public function decideApplyEnd(Token $token): bool
	{
		return $token->test('endk');
	}

	public function getTag(): string
	{
		return 'k';
	}
}