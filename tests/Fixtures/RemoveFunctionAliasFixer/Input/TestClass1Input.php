<?php

class TestClass1Input
{

    /**
     * @return void
     */
    public function replaceFunction()
    {
        $foo = is_integer(2);
        $foo = 2/is_writeable($foo);
        $foo = join('', array());
        $foo = FPUTS($foo, 'xyz');
        $foo = sizeof(array());
    }

    /**
     * @return void
     */
    public function doNotReplaceFunction()
    {
        $foo = is_int(2);
        $foo = is_writable($foo);
        $foo = new Join();
        $foo = Join::foo();
    }

}
