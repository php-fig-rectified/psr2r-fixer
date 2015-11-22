<?php

namespace Test\Fixtures;

class TestClass1Input {

	/**
	 * @param int $foo Foo
	 *
	 * @return self
	 */
	public function replace($foo) {
		if ($foo === null) {
		}
		$foo = 2 / ($foo === 2);
		if ($foo === true) {
		}
		if ($foo > 2) {
		}
		if (is_int($foo) < 2) {
			$foo = (int)$foo;
		}
		$foo = $foo == 2;
		$foo = $foo === 2;
		if ((int)$foo === null && $this->foo() === false) {
		}
		if ($this->foo() >= 2) {
		}
		if ($this->foo() <= 2) {
		}
	}

	/**
	 * @return int|bool Xyz
	 */
	public function doNotReplace() {
		$foo = false;
		$foo = $foo == 2;
		$foo = $foo === 2;
		if ($foo === true) {
		}
		if ($foo === null && $this->foo() === false) {
			$x = [[1, 2, 3]];
		}
	}

}
