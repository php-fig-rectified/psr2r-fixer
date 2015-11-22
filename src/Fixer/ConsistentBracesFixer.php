<?php

namespace Psr2rFixer\Fixer;

use Symfony\CS\Tokenizer\Tokens;
use Symfony\CS\Fixer\PSR2\BracesFixer;

/**
 * Fixer ConsistentBraces
 *
 * //TODO: Make it also work with spaces (currently the UseTabs fixer has to run prior to this one!)
 *
 * @author Mark Scherer
 */

class ConsistentBracesFixer extends BracesFixer {

	/**
	 * {@inheritdoc}
	 */
	public function fix(\SplFileInfo $file, $content) {
		$tokens = Tokens::fromCode($content);

		$this->fixCommentBeforeBrace($tokens);
		$this->fixMissingControlBraces($tokens);
		$this->fixIndents($tokens);
		$this->fixControlContinuationBraces($tokens);
		$this->fixSpaceAroundToken($tokens);
		$this->fixDoWhile($tokens);
		$this->fixLambdas($tokens);

		return $tokens->generateCode();
	}

	protected function fixCommentBeforeBrace(Tokens $tokens) {
		$controlTokens = $this->getControlTokens();

		for ($index = $tokens->count() - 1; 0 <= $index; --$index) {
			$token = $tokens[$index];

			if (!$token->isGivenKind($controlTokens)) {
				continue;
			}

			$parenthesisEndIndex = $this->findParenthesisEnd($tokens, $index);
			$afterParenthesisIndex = $tokens->getNextNonWhitespace($parenthesisEndIndex);
			$afterParenthesisToken = $tokens[$afterParenthesisIndex];

			if (!$afterParenthesisToken->isComment()) {
				continue;
			}

			$afterCommentIndex = $tokens->getNextMeaningfulToken($afterParenthesisIndex);
			$afterCommentToken = $tokens[$afterCommentIndex];

			if (!$afterCommentToken->equals('{')) {
				continue;
			}

			$tokenTmp = $tokens[$afterCommentIndex];
			$tokens[$afterCommentIndex - 1]->setContent(rtrim($tokens[$afterCommentIndex - 1]->getContent()));

			for ($i = $afterCommentIndex; $i > $afterParenthesisIndex; --$i) {
				$tokens[$i] = $tokens[$i - 1];
			}

			$tokens[$afterParenthesisIndex] = $tokenTmp;
		}
	}

	protected function fixControlContinuationBraces(Tokens $tokens) {
		$controlContinuationTokens = $this->getControlContinuationTokens();

		for ($index = count($tokens) - 1; 0 <= $index; --$index) {
			$token = $tokens[$index];

			if (!$token->isGivenKind($controlContinuationTokens)) {
				continue;
			}

			$prevIndex = $tokens->getPrevNonWhitespace($index);
			$prevToken = $tokens[$prevIndex];

			if (!$prevToken->equals('}')) {
				continue;
			}

			$tokens->ensureWhitespaceAtIndex($index - 1, 1, ' ');
		}
	}

	protected function fixDoWhile(Tokens $tokens) {
		for ($index = count($tokens) - 1; 0 <= $index; --$index) {
			$token = $tokens[$index];

			if (!$token->isGivenKind(T_DO)) {
				continue;
			}

			$parenthesisEndIndex = $this->findParenthesisEnd($tokens, $index);
			$startBraceIndex = $tokens->getNextNonWhitespace($parenthesisEndIndex);
			$endBraceIndex = $tokens->findBlockEnd(Tokens::BLOCK_TYPE_CURLY_BRACE, $startBraceIndex);
			$nextNonWhitespaceIndex = $tokens->getNextNonWhitespace($endBraceIndex);
			$nextNonWhitespaceToken = $tokens[$nextNonWhitespaceIndex];

			if (!$nextNonWhitespaceToken->isGivenKind(T_WHILE)) {
				continue;
			}

			$tokens->ensureWhitespaceAtIndex($nextNonWhitespaceIndex - 1, 1, ' ');
		}
	}

	protected function fixIndents(Tokens $tokens) {
		$classyTokens = $this->getClassyTokens();
		$classyAndFunctionTokens = array_merge([T_FUNCTION], $classyTokens);
		$controlTokens = $this->getControlTokens();
		$indentTokens = array_filter(
			array_merge($classyAndFunctionTokens, $controlTokens),
			function ($item) {
				return T_SWITCH !== $item;
			}

		);

		for ($index = 0, $limit = count($tokens); $index < $limit; ++$index) {
			$token = $tokens[$index];

			// if token is not a structure element - continue
			if (!$token->isGivenKind($indentTokens)) {
				continue;
			}

			// do not change indent for lambda functions
			if ($token->isGivenKind(T_FUNCTION) && $tokens->isLambda($index)) {
				continue;
			}

			if ($token->isGivenKind($classyAndFunctionTokens)) {
				$startBraceIndex = $tokens->getNextTokenOfKind($index, [';', '{']);
				$startBraceToken = $tokens[$startBraceIndex];
			} else {
				$parenthesisEndIndex = $this->findParenthesisEnd($tokens, $index);
				$startBraceIndex = $tokens->getNextNonWhitespace($parenthesisEndIndex);
				$startBraceToken = $tokens[$startBraceIndex];
			}

			// structure without braces block - nothing to do, e.g. do { } while (true);
			if (!$startBraceToken->equals('{')) {
				continue;
			}

			$endBraceIndex = $tokens->findBlockEnd(Tokens::BLOCK_TYPE_CURLY_BRACE, $startBraceIndex);

			$indent = $this->detectIndent($tokens, $index);

			// fix indent near closing brace
			//FIX but only for non-class?
			$tokens->ensureWhitespaceAtIndex($endBraceIndex - 1, 1, "\n");

			// fix indent between braces
			$lastCommaIndex = $tokens->getPrevTokenOfKind($endBraceIndex - 1, [';', '}']);

			$nestLevel = 1;
			for ($nestIndex = $lastCommaIndex; $nestIndex >= $startBraceIndex; --$nestIndex) {
				$nestToken = $tokens[$nestIndex];

				if ($nestToken->equals(')')) {
					$nestIndex = $tokens->findBlockEnd(Tokens::BLOCK_TYPE_PARENTHESIS_BRACE, $nestIndex, false);
					continue;
				}

				if ($nestLevel === 1 && $nestToken->equalsAny([';', '}'])) {
					$nextNonWhitespaceNestToken = $tokens[$tokens->getNextNonWhitespace($nestIndex)];

					if (
						// next Token is not a comment
						!$nextNonWhitespaceNestToken->isComment() &&
						// and it is not a `$foo = function () {};` situation
						!($nestToken->equals('}') && $nextNonWhitespaceNestToken->equalsAny([';', ',', ']'])) &&
						// and it is not a `${"a"}->...` and `${"b{$foo}"}->...` situation
						!($nestToken->equals('}') && $tokens[$nestIndex - 1]->equalsAny(['"', "'", [T_CONSTANT_ENCAPSED_STRING]]))
					) {
						if ($nextNonWhitespaceNestToken->isGivenKind($this->getControlContinuationTokens())) {
							$whitespace = ' ';
						} else {
							$nextToken = $tokens[$nestIndex + 1];
							$nextWhitespace = '';

							if ($nextToken->isWhitespace()) {
								$nextWhitespace = rtrim($nextToken->getContent(), " \t");

								if (strlen($nextWhitespace) && $nextWhitespace[strlen($nextWhitespace) - 1] === "\n") {
									$nextWhitespace = substr($nextWhitespace, 0, -1);
								}
							}

							$whitespace = $nextWhitespace . "\n" . $indent;

							if (!$nextNonWhitespaceNestToken->equals('}')) {
								$whitespace .= '	';
							}
						}

						$tokens->ensureWhitespaceAtIndex($nestIndex + 1, 0, $whitespace);
					}
				}

				if ($nestToken->equals('}')) {
					++$nestLevel;
					continue;
				}

				if ($nestToken->equals('{')) {
					--$nestLevel;
					continue;
				}
			}

			// fix indent near opening brace
			if (isset($tokens[$startBraceIndex + 2]) && $tokens[$startBraceIndex + 2]->equals('}')) {
				$tokens->ensureWhitespaceAtIndex($startBraceIndex + 1, 0, "\n" . $indent);
			} else {
				$nextToken = $tokens[$startBraceIndex + 1];
				$nextNonWhitespaceToken = $tokens[$tokens->getNextNonWhitespace($startBraceIndex)];

				// set indent only if it is not a case, when comment is following { in same line
				if (
					!$nextNonWhitespaceToken->isComment()
					|| !($nextToken->isWhitespace() && $nextToken->isWhitespace(['whitespaces' => " \t"]))
				) {
					$tokens->ensureWhitespaceAtIndex($startBraceIndex + 1, 0, "\n" . $indent . '	');
				}
			}

			if ($token->isGivenKind($classyTokens)) {
				//FIX
				$prevIndex = $tokens->getPrevNonWhitespace($startBraceIndex);
				$tokens->removeTrailingWhitespace($prevIndex);
				$tokens->ensureWhitespaceAtIndex($startBraceIndex - 1, 1, ' ');
				//$tokens->ensureWhitespaceAtIndex($startBraceIndex - 1, 1, "\n".$indent);
				//$tokens->ensureWhitespaceAtIndex($startBraceIndex, 1, "\n");

				//$tokens->ensureWhitespaceAtIndex($endBraceIndex - 1, 1, "\n");
} elseif ($token->isGivenKind(T_FUNCTION)) {
	$closingParenthesisIndex = $tokens->getPrevTokenOfKind($startBraceIndex, [')']);
	$prevToken = $tokens[$closingParenthesisIndex - 1];

	if ($prevToken->isWhitespace() && strpos($prevToken->getContent(), "\n") !== false) {
		$tokens->ensureWhitespaceAtIndex($startBraceIndex - 1, 1, ' ');
	} else {
		//FIX
					$tokens->ensureWhitespaceAtIndex($startBraceIndex - 1, 1, ' ');
					//$tokens->ensureWhitespaceAtIndex($startBraceIndex - 1, 1, "\n".$indent);
}
} else {
	$tokens->ensureWhitespaceAtIndex($startBraceIndex - 1, 1, ' ');
}

			// reset loop limit due to collection change
			$limit = count($tokens);
		}
	}

	protected function fixLambdas(Tokens $tokens) {
		for ($index = $tokens->count() - 1; 0 <= $index; --$index) {
			$token = $tokens[$index];

			if (!$token->isGivenKind(T_FUNCTION) || !$tokens->isLambda($index)) {
				continue;
			}

			$nextIndex = $tokens->getNextTokenOfKind($index, ['{']);

			$tokens->ensureWhitespaceAtIndex($nextIndex - 1, 1, ' ');
		}
	}

	protected function fixMissingControlBraces(Tokens $tokens) {
		$controlTokens = $this->getControlTokens();

		for ($index = $tokens->count() - 1; 0 <= $index; --$index) {
			$token = $tokens[$index];

			if (!$token->isGivenKind($controlTokens)) {
				continue;
			}

			$parenthesisEndIndex = $this->findParenthesisEnd($tokens, $index);
			$tokenAfterParenthesis = $tokens[$tokens->getNextMeaningfulToken($parenthesisEndIndex)];

			// if Token after parenthesis is { then we do not need to insert brace, but to fix whitespace before it
			if ($tokenAfterParenthesis->equals('{')) {
				$tokens->ensureWhitespaceAtIndex($parenthesisEndIndex + 1, 0, ' ');
				continue;
			}

			// do not add braces for cases:
			// - structure without block, e.g. while ($iter->next());
			// - structure with block, e.g. while ($i) {...}, while ($i) : {...} endwhile;
			if ($tokenAfterParenthesis->equalsAny([';', '{', ':'])) {
				continue;
			}

			$statementEndIndex = $this->findStatementEnd($tokens, $parenthesisEndIndex);

			// insert closing brace
			$tokens->insertAt($statementEndIndex + 1, [new Token([T_WHITESPACE, ' ']), new Token('}')]);

			// insert opening brace
			$tokens->removeTrailingWhitespace($parenthesisEndIndex);
			$tokens->insertAt($parenthesisEndIndex + 1, new Token('{'));
			$tokens->ensureWhitespaceAtIndex($parenthesisEndIndex + 1, 0, ' ');
		}
	}

	protected function fixSpaceAroundToken(Tokens $tokens) {
		$controlTokens = $this->getControlTokens();

		for ($index = $tokens->count() - 1; 0 <= $index; --$index) {
			$token = $tokens[$index];

			if ($token->isGivenKind($controlTokens) || $token->isGivenKind(T_USE)) {
				$nextNonWhitespaceIndex = $tokens->getNextNonWhitespace($index);

				if (!$tokens[$nextNonWhitespaceIndex]->equals(':')) {
					$tokens->ensureWhitespaceAtIndex($index + 1, 0, ' ');
				}

				$prevToken = $tokens[$index - 1];

				if (!$prevToken->isWhitespace() && !$prevToken->isComment() && !$prevToken->isGivenKind(T_OPEN_TAG)) {
					$tokens->ensureWhitespaceAtIndex($index - 1, 1, ' ');
				}
			}
		}
	}

	protected function detectIndent(Tokens $tokens, $index) {
		static $goBackTokens = [T_ABSTRACT, T_FINAL, T_PUBLIC, T_PROTECTED, T_PRIVATE, T_STATIC];

		$token = $tokens[$index];

		if ($token->isGivenKind($goBackTokens) || $token->isClassy() || $token->isGivenKind(T_FUNCTION)) {
			$prevIndex = $tokens->getPrevNonWhitespace($index);
			$prevToken = $tokens[$prevIndex];

			if ($prevToken->isGivenKind($goBackTokens)) {
				return $this->detectIndent($tokens, $prevIndex);
			}
		}

		$prevIndex = $index - 1;
		$prevToken = $tokens[$prevIndex];

		if ($prevToken->equals('}')) {
			return $this->detectIndent($tokens, $prevIndex);
		}

		// if can not detect indent:
		if (!$prevToken->isWhitespace()) {
			return '';
		}

		$explodedContent = explode("\n", $prevToken->getContent());

		// proper decect indent for code: `	} else {`
		if (count($explodedContent) === 1) {
			if ($tokens[$index - 2]->equals('}')) {
				return $this->detectIndent($tokens, $index - 2);
			}
		}

		return end($explodedContent);
	}

	protected function findParenthesisEnd(Tokens $tokens, $structureTokenIndex) {
		$nextIndex = $tokens->getNextNonWhitespace($structureTokenIndex);
		$nextToken = $tokens[$nextIndex];

		// return if next token is not opening parenthesis
		if (!$nextToken->equals('(')) {
			return $structureTokenIndex;
		}

		return $tokens->findBlockEnd(Tokens::BLOCK_TYPE_PARENTHESIS_BRACE, $nextIndex);
	}

	protected function findStatementEnd(Tokens $tokens, $parenthesisEndIndex) {
		$nextIndex = $tokens->getNextNonWhitespace($parenthesisEndIndex);
		$nextToken = $tokens[$nextIndex];

		if (!$nextToken) {
			return $parenthesisEndIndex;
		}

		if ($nextToken->equals('{')) {
			return $tokens->findBlockEnd(Tokens::BLOCK_TYPE_CURLY_BRACE, $nextIndex);
		}

		if ($nextToken->isGivenKind($this->getControlTokens())) {
			$parenthesisEndIndex = $this->findParenthesisEnd($tokens, $nextIndex);

			$endIndex = $this->findStatementEnd($tokens, $parenthesisEndIndex);

			if ($nextToken->isGivenKind(T_IF)) {
				$nextIndex = $tokens->getNextNonWhitespace($endIndex);
				$nextToken = $tokens[$nextIndex];

				if ($nextToken && $nextToken->isGivenKind($this->getControlContinuationTokens())) {
					$parenthesisEndIndex = $this->findParenthesisEnd($tokens, $nextIndex);

					return $this->findStatementEnd($tokens, $parenthesisEndIndex);
				}
			}

			return $endIndex;
		}

		$index = $parenthesisEndIndex;

		while (true) {
			$token = $tokens[++$index];

			if ($token->equals(';')) {
				break;
			}
		}

		return $index;
	}

	protected function getClassyTokens() {
		static $tokens = null;

		if ($tokens === null) {
			$tokens = [T_CLASS, T_INTERFACE];

			if (defined('T_TRAIT')) {
				$tokens[] = T_TRAIT;
			}
		}

		return $tokens;
	}

	/**
	 * @return array
	 */
	protected function getControlTokens() {
		static $tokens = null;

		if ($tokens === null) {
			$tokens = [
				T_DECLARE,
				T_DO,
				T_ELSE,
				T_ELSEIF,
				T_FOR,
				T_FOREACH,
				T_IF,
				T_WHILE,
				T_TRY,
				T_CATCH,
				T_SWITCH,
			];

			if (defined('T_FINALLY')) {
				$tokens[] = T_FINALLY;
			}
		}

		return $tokens;
	}

	/**
	 * @return array
	 */
	protected function getControlContinuationTokens() {
		static $tokens = null;

		if ($tokens === null) {
			$tokens = [
				T_ELSE,
				T_ELSEIF,
				T_CATCH,
			];

			if (defined('T_FINALLY')) {
				$tokens[] = T_FINALLY;
			}
		}

		return $tokens;
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
		return 'Code must use consistent brace style (opening parantheses always on the same line).';
	}

	/**
	 * @return int
	 */
	public function getPriority() {
		// Should be run after the ElseIfFixer and DuplicateSemicolonFixer
		return -98;
	}

}
