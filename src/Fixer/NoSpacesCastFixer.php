<?php

namespace Psr2rFixer\Fixer;

use Symfony\CS\Tokenizer\Token;
use Symfony\CS\Tokenizer\Tokens;
use Symfony\CS\Fixer\Symfony\SpacesCastFixer;

/**
 * Fixer NoSpacesCast
 *
 * @author Mark Scherer
 */
class NoSpacesCastFixer extends SpacesCastFixer {

	/**
	 * @param \SplFileInfo $file
	 * @param string $content
	 * @return string
	 */
	public function fix(\SplFileInfo $file, $content) {
		static $insideCastSpaceReplaceMap = [
			' ' => '',
			"\t" => '',
			"\n" => '',
			"\r" => '',
			"\0" => '',
			"\x0B" => '',
		];
		$tokens = Tokens::fromCode($content);
		foreach ($tokens as $index => $token) {
			if ($token->isCast()) {
				$token->setContent(strtr($token->getContent(), $insideCastSpaceReplaceMap));
				// force single whitespace after cast token:
				if ($tokens[$index + 1]->isWhitespace(['whitespaces' => " \t"])) {
					// - if next token is whitespaces that contains only spaces and tabs - override next token with single space
					$tokens[$index + 1]->setContent('');
				} elseif (!$tokens[$index + 1]->isWhitespace()) {
					// - if next token is not whitespaces that contains spaces, tabs and new lines - append single space to current token
					$tokens->insertAt($index + 1, new Token(''));
				}
			}
		}

		return $tokens->generateCode();
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
		return 'No whitespace should be between cast and variable.';
	}

}
