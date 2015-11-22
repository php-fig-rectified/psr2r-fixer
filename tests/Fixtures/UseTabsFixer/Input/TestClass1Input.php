<?php

class TestClass1Input
{

    /**
     * @return void
     */
    public function replaceFunction()
    {
        $foo = (int)2;
		$foo = 2 / (bool)$foo;
        if ($foo) {
            // Comment
            $someCode = 1;
		}
    	$mixedLine = 1;
	    $anotherMixedLine = 1;
    }

    /**
     * @return void
     */
    public function doNotReplaceFunction()
    {
        $foo = (int)2;
        $foo = 2/(bool)$foo;
    }

}
