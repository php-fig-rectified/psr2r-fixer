<?php

namespace Psr2rFixer\Test\Fixer;

if (!defined('DS')) {
	define('DS', DIRECTORY_SEPARATOR);
}

/**
 * Test case
 */
class CompleteTest extends \PHPUnit_Framework_TestCase {

	protected function setUp() {
		parent::setUp();
	}

	/**
	 * @return void
	 */
	public function testFix() {
		$fixturePath = dirname(__DIR__) . '/Fixtures/' . 'Complete' . '/';

		$testFile = $fixturePath . 'Input/TestClass1Input.php';

		$testFolder = $fixturePath . 'Test' . DS;
		if (!is_dir($testFolder)) {
			mkdir($testFolder, 0775);
		}

		$configFile = dirname(dirname(__DIR__)) . DS . '.php_cs';
		$newConfigFile = $testFolder . '.php_cs';
		copy($configFile, $newConfigFile);

		$newTestFile = $testFolder . 'TestClass1Input.php';
		copy($testFile, $newTestFile);

		$before = file_get_contents($newTestFile);

		exec('vendor' . DS . 'bin' . DS . 'php-cs-fixer fix --diff --config-file=' . $newConfigFile . ' ' . $testFolder, $output, $ret);

		//var_dump($ret);
		//$this->assertSame(0, $ret);

		$after = file_get_contents($newTestFile);

		//var_dump($output);

		$expected = file_get_contents($fixturePath . 'Expected/TestClass1Expected.php');

		$this->assertSame($expected, $after);
	}

}
