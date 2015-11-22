<?php

namespace Test\Fixtures;

class TestClass1Input {

	/**
	 * @return void
	 */
	public function replace() {
		if (null === $foo) {
		}
		$foo = 2/(2 === $foo);
		if (true === $foo) {
		}
		if (2 < $foo) {
		}
		if (2 > $foo) {
		}
		if (null !== $redirectData[self::FROM_URL]) {
		}
		$foo = 2 == $foo;
		$foo = 3 === $foo;
		if (null === $foo && false === $this->foo()) {
		}
		if (2 <= $this->foo()) {
		}
		if (2 >= $this->foo()) {
		}
		if (true === array_key_exists($fromXmlElementName, $toXmlElements)) {
		}
		if (false === ($results instanceof ObjectCollection)) {
		}
		if (0 === $taxSet->getTaxRates()->count()) {
		}
		if (true !== $row['product_concrete']) {
		}
		if (isset($name[0]) && '@' === $name[0]) {
		}
		return null !== $xyz;
	}

	/**
	 * @return void
	 */
	public function replaceNotYet()
	{
		if (MyClass::CONSTANT === $foo) {
		}
	}

	/**
	 * @return void
	 */
	public function doNotReplace()
	{
		$foo = $foo == 2;
		$foo = $foo === 2;
		if ($foo === true) {
		}
		if ($foo === null && $this->foo() === false) {
		}
	}

}
