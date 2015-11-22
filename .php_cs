<?php

$finder = Symfony\CS\Finder\DefaultFinder::create()
	->in(__DIR__)
	->exclude('vendor')
	->exclude('tests/Fixtures')
;

return require_once('.php_cs_psr2r');
