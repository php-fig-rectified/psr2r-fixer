<?php

class TestClass1Input
{

    /**
     * @return void
     */
    public function replace() {
        if ($foo = true) {
        }
        if ($foo = $this->foo($x)) {
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
