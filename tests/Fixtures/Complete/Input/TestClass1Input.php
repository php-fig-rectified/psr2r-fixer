<?php

namespace Test\Fixtures;

class TestClass1Input
{
    /**
     * @param int $foo Foo
	 *
	 * @return $this
     */
    public function replace($foo)
    {
        if (null === $foo) {
        }
        $foo = 2 / (2 === $foo);
        if (true === $foo) {
        }
        if (2 < $foo) {
        }
        if (2 > is_integer($foo)) {
			$foo = (integer) $foo;
        }
		$foo = 2 == $foo;
		$foo = 2 === $foo;
		if (null === intval($foo) && false === $this->foo()) {
		}
		if (2 <= $this->foo()) {
		}
		if (2 >= $this->foo()) {
		}
    }

    /**
     * @return int | bool Xyz
     */
    public function doNotReplace()
    {
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
