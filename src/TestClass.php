<?php


namespace invacto\SimpleCMS;


class TestClass
{
	private $value = "";

	public function setValue($value) {
		$this->value = $value;
	}

	public function getValue() {
		return $this->value;
	}
}