<?php

namespace Test\Fixtures;

class TestClass1Input
{

    /**
     * @return void
     */
    public function replaceFunction()
    {
        $foo = php_sapi_name();
        $foo = substr(php_SaPi_name(), 0, 3);
    }

    /**
     * Do not replace
     *
     * @return void
     */
    public function php_sapi_name()
    {
        $foo = $this->php_sapi_name();
        $foo = php_sapi_name($foo);
    }

}
