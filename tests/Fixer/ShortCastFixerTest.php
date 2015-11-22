<?php

namespace Psr2rFixer\Test\Fixer;

use Psr2rFixer\Fixer\ShortCastFixer;

class ShortCastFixerTest extends \PHPUnit_Framework_TestCase {

	const FIXER_NAME = 'ShortCastFixer';

	/**
	 * @var ShortCastFixer
	 */
	protected $fixer;

	protected function setUp() {
		parent::setUp();
		$this->fixer = new ShortCastFixer();
	}

	/**
	 * @dataProvider provideFixCases
	 *
	 * @param string $expected
	 * @param string $input
	 *
	 * @return void
	 */
	public function testFix($expected, $input = null) {
		$fileInfo = new \SplFileInfo(__FILE__);
		$this->assertSame($expected, $this->fixer->fix($fileInfo, $input));
	}

	/**
	 * @return array
	 */
	public function provideFixCases() {
		$fixturePath = dirname(__DIR__) . '/Fixtures/' . self::FIXER_NAME . '/';

		return [
			[
				file_get_contents($fixturePath . 'Expected/TestClass1Expected.php'),
				file_get_contents($fixturePath . 'Input/TestClass1Input.php'),
			],
		];
	}

}
