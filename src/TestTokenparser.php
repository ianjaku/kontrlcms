<?php


namespace invacto\SimpleCMS;


use Twig\Token;
use Twig\TokenParser\AbstractTokenParser;

class TestTokenparser extends AbstractTokenParser
{
	public function parse(Token $token)
	{
		$parser = $this->parser;
		$stream = $parser->getStream();

		$name = $stream->expect(Token::NAME_TYPE)->getValue();
		$stream->expect(Token::OPERATOR_TYPE, '=');
		$value = $parser->getExpressionParser()->parseExpression();
//		$this->parser->subparse()
//		$value = $stream->expect(TOKEN::NUMBER_TYPE)->getValue();
		$stream->expect(Token::BLOCK_END_TYPE);

		return new TestNode($name, $value, $token->getLine(), $this->getTag());
//		return new TestNode($name);
	}

	public function getTag()
	{
		return 'set';
	}
}