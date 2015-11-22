<?php

namespace Psr2rFixer\Fixer;

use Symfony\CS\Fixer\PSR2\IndentationFixer;

/**
 * Fixer ShortCast
 *
 * @author Mark Scherer
 */
class UseTabsFixer extends IndentationFixer {

	/**
	 * @param \SplFileInfo $file
	 * @param string $content
	 * @return string
	 */
	public function fix(\SplFileInfo $file, $content) {
		return preg_replace_callback('/^([\t| ]{4,})/m', function ($matches) use ($content) {
			$result = str_replace(str_repeat(' ', 4), "\t", $matches[0]);
			$result = str_replace(str_repeat(' ', 2), '', $result);

			return $result;
		}, $content);
	}

	/**
	 * @return int
	 */
	public function getLevel() {
		return 0;
	}

	/**
	 * @return string
	 */
	public function getDescription() {
		return 'Code must use tabs for indenting.';
	}

	/**
	 * @return int
	 */
	public function getPriority() {
		// Should be run after the ElseIfFixer and DuplicateSemicolonFixer
		return -97;
	}

}
