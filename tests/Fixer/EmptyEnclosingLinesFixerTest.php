<?php

namespace Psr2rFixer\Test\Fixer;

use Psr2rFixer\Fixer\EmptyEnclosingLinesFixer;

class EmptyEnclosingLinesFixerTest extends \PHPUnit_Framework_TestCase {

	const FIXER_NAME = 'EmptyEnclosingLinesFixer';

	/**
	 * @var EmptyEnclosingLinesFixer
	 */
	protected $fixer;

	protected function setUp() {
		parent::setUp();
		$this->fixer = new EmptyEnclosingLinesFixer();
	}

	/**
	 * @dataProvider provideFixCases
	 *
	 * @param string $expected
	 * @param string $input
	 */
	public function testFix($expected, $input = null) {
		$fileInfo = new \SplFileInfo(__FILE__);
		$this->assertSame($expected, $this->fixer->fix($fileInfo, $input));
	}

	public function provideFixCases() {
		$fixturePath = dirname(__DIR__) . '/Fixtures/' . self::FIXER_NAME . '/';

		return [
			[
				file_get_contents($fixturePath . 'Expected/TestClass1Expected.php'),
				file_get_contents($fixturePath . 'Input/TestClass1Input.php'),
			],
			[
				file_get_contents($fixturePath . 'Expected/TestClass2Expected.php'),
				file_get_contents($fixturePath . 'Input/TestClass2Input.php'),
			],
			[
				file_get_contents($fixturePath . 'Expected/TestClass3Expected.php'),
				file_get_contents($fixturePath . 'Input/TestClass3Input.php'),
			],
			[
				file_get_contents($fixturePath . 'Expected/TestClass4Expected.php'),
				file_get_contents($fixturePath . 'Input/TestClass4Input.php'),
			],
			[
				file_get_contents($fixturePath . 'Expected/TestClass5Expected.php'),
				file_get_contents($fixturePath . 'Input/TestClass5Input.php'),
			],
		];
	}

}
