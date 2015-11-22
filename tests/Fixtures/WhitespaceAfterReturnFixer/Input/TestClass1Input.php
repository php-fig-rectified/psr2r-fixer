<?php

namespace Test\Fixtures;

class TestClass1Input
{

    public function replaceFunction()
    {
        return;
    }

    public function replaceFunctionB()
    {
        return  $foo;
    }

    public function replaceFunctionC()
    {
        return   ($foo + $bar);
    }

    public function doNotReplaceFunction()
    {
        return
            ($x + $y);
    }

}
