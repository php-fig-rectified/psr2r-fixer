<?php

class TestClass1Input {
    /**
     * @return void
     */
    public function replaceFunction() {
        $foo = (int)2;
        if ($foo) {
        }
    }

    /**
     * @return void
     */
    public function doNotReplaceFunction() {
        $foo = (int)2;
        $foo = 2/(bool)$foo;
    }
}
