<?php

namespace Psr2rFixer\Fixer;

use Symfony\CS\AbstractFixer;
use Symfony\CS\FixerInterface;
use Symfony\CS\Tokenizer\Token;
use Symfony\CS\Tokenizer\Tokens;
use Symfony\CS\Utils;

/**
 * @author Ceeram <ceeram@cakephp.org>
 */
class PhpdocIndentFixer extends AbstractFixer {

	/**
	 * {@inheritdoc}
	 */
	public function fix(\SplFileInfo $file, $content)
	{
		$tokens = Tokens::fromCode($content);

		foreach ($tokens->findGivenKind(T_DOC_COMMENT) as $index => $token) {
			$next = $tokens->getNextMeaningfulToken($index);
			if (null === $next) {
				continue;
			}

			$indent = $this->calculateIndent($tokens[$next - 1]->getContent());

			$prevToken = $tokens[$index - 1];

			$prevToken->setContent($this->fixWhitespaceBefore($prevToken->getContent(), $indent));

			$token->setContent($this->fixDocBlock($token->getContent(), $indent));
		}

		return $tokens->generateCode();
	}

	/**
	 * {@inheritdoc}
	 */
	public function getDescription() {
		return 'Docblocks should have the same indentation as the documented subject.';
	}

	/**
	 * Fix indentation of Docblock.
	 *
	 * @param string $content Docblock contents
	 * @param string $indent  Indentation to apply
	 *
	 * @return string Dockblock contents including correct indentation
	 */
	protected function fixDocBlock($content, $indent)
	{
		return ltrim(preg_replace('/^[ \t]*/m', $indent.' ', $content));
	}

	/**
	 * Fix whitespace before the Docblock.
	 *
	 * @param string $content Whitespace before Docblock
	 * @param string $indent  Indentation of the documented subject
	 *
	 * @return string Whitespace including correct indentation for Dockblock after this whitespace
	 */
	protected function fixWhitespaceBefore($content, $indent)
	{
		return rtrim($content, " \t").$indent;
	}

	/**
	 * Calculate used indentation from the whitespace before documented subject.
	 *
	 * @param string $content Whitespace before documented subject
	 *
	 * @return string
	 */
	protected function calculateIndent($content)
	{
		return ltrim(strrchr(str_replace(array("\r\n", "\r"), "\n", $content), 10), "\n");
	}

	public function getLevel() {
		return 0;
	}

}
