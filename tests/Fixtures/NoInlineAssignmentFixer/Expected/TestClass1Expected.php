<?php

class TestClass1Input
{

    /**
     * @return void
     */
    public function replace() {
        $foo = true;
        if ($foo) {
        }
        $foo = $this->foo($x);
        if ($foo) {
        }
    }

    /**
     * @return void
     */
    public function replaceNotYet()
    {
        $foo = 2/($foo = 2);

        if (!($stats = $this->getResource()->getStats())) {
        }

        if (null !== ($obj = Map::getInstanceFromPool($key))) {
        }
    }

    /**
     * @return void
     */
    public function doNotReplace()
    {
        if ($foo = false || $foo = $this->foo()) {
        }
    }

}
