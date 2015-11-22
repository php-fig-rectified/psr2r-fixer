<?php

namespace Psr2rFixer\Fixer;

use Symfony\CS\AbstractFixer;
use Symfony\CS\FixerInterface;
use Symfony\CS\Tokenizer\Token;
use Symfony\CS\Tokenizer\Tokens;

/**
 * Fixer ConditionalExpressionOrder
 *
 * @author Mark Scherer
 */

class ConditionalExpressionOrderFixer extends AbstractFixer {

	/**
	 * @param \SplFileInfo $file
	 * @param string $content
	 *
	 * @return string
	 */
	public function fix(\SplFileInfo $file, $content) {
		$tokens = Tokens::fromCode($content);

		$this->fixConditions($tokens);

		return $tokens->generateCode();
	}

	/**
	 * @see http://php.net/manual/en/language.operators.precedence.php
	 *
	 * @param Tokens|Token[] $tokens
	 *
	 * @return void
	 */
	protected function fixConditions(Tokens $tokens) {
		$whitelistedTokens = [T_IS_IDENTICAL, T_IS_NOT_IDENTICAL, T_IS_EQUAL, T_IS_NOT_EQUAL, T_IS_GREATER_OR_EQUAL, T_IS_SMALLER_OR_EQUAL];

		foreach ($tokens as $index => $token) {
			if (!$token->isGivenKind($whitelistedTokens) && !in_array($token->getContent(), ['<', '>'], true)
			) {
				continue;
			}

			// Only sniff for specified tokens on left side
			$prevIndex = $tokens->getPrevMeaningfulToken($index);
			if (!$tokens[$prevIndex]->isNativeConstant()
				&& !in_array($tokens[$prevIndex]->getId(), [T_LNUMBER, T_CONSTANT_ENCAPSED_STRING])
			) {
				continue;
			}
			$leftIndexEnd = $prevIndex;
			$leftIndexStart = $prevIndex;

			$prevIndex = $tokens->getPrevMeaningfulToken($leftIndexStart);
			$prevContent = $tokens[$prevIndex]->getContent();
			if (
				!$tokens[$prevIndex]->isGivenKind([T_BOOLEAN_AND, T_BOOLEAN_OR, T_RETURN])
				&& $prevContent !== '=' && $prevContent !== '('
			) {
				continue;
			}

			$rightIndexStart = $tokens->getNextMeaningfulToken($index);

			if ($tokens[$rightIndexStart]->getContent() === '(') {
				$rightIndexEnd = $tokens->findBlockEnd(Tokens::BLOCK_TYPE_PARENTHESIS_BRACE, $rightIndexStart);
			} else {
				$rightIndexEnd = $this->detectRightEnd($tokens, $rightIndexStart);
				if ($prevContent === '(') {
					$closingBraceIndex = $tokens->findBlockEnd(Tokens::BLOCK_TYPE_PARENTHESIS_BRACE, $prevIndex);
					if ($tokens[$closingBraceIndex]->getContent() !== ')') {
						continue;
					}
					$rightIndexEndLimit = $tokens->getPrevMeaningfulToken($closingBraceIndex);
					$rightIndexEnd = min($rightIndexEndLimit, $rightIndexEnd);
				}
			}

			$this->applyFix($tokens, $index, $leftIndexStart, $leftIndexEnd, $rightIndexStart, $rightIndexEnd);
		}
	}

	/**
	 * @return int
	 */
	public function getPriority() {
		return -100;
	}

	/**
	 * @return int
	 */
	public function getLevel() {
		return FixerInterface::NONE_LEVEL;
	}

	/**
	 * @return string
	 */
	public function getDescription() {
		return 'Usage of Yoda conditions is not allowed. Switch the expression order.';
	}

	/**
	 * @param Tokens|Token[] $tokens
	 * @param int $comparisonIndex
	 *
	 * @return string
	 */
	protected function getComparisonValue(Tokens $tokens, $comparisonIndex) {
		$comparisonIndexValue = $tokens[$comparisonIndex]->getContent();
		$operatorsToMap = [T_IS_GREATER_OR_EQUAL, T_IS_SMALLER_OR_EQUAL];
		$operatorStringsToMap = ['>', '<'];

		if (in_array($tokens[$comparisonIndex]->getId(), $operatorsToMap, true)) {
			$mapping = [
				T_IS_GREATER_OR_EQUAL => '<=',
				T_IS_SMALLER_OR_EQUAL => '>=',
			];
			$comparisonIndexValue = $mapping[$tokens[$comparisonIndex]->getId()];

			return $comparisonIndexValue;
		}

		if (in_array($tokens[$comparisonIndex]->getContent(), $operatorStringsToMap, true)) {
			$mapping = [
				'>' => '<',
				'<' => '>',
			];
			$comparisonIndexValue = $mapping[$tokens[$comparisonIndex]->getContent()];

			return $comparisonIndexValue;
		}

		return $comparisonIndexValue;
	}

	/**
	 * @param Tokens|Token[] $tokens
	 * @param int $index
	 *
	 * @return int
	 */
	protected function detectRightEnd(Tokens $tokens, $index) {
		$rightEndIndex = $index;
		$nextIndex = $index;
		$max = null;
		$braceCounter = 0;
		if ($tokens[$index]->getContent() === '(') {
			++$braceCounter;
			$braceEndIndex = $tokens->findBlockEnd(Tokens::BLOCK_TYPE_PARENTHESIS_BRACE, $index);

			return $braceEndIndex;
		}

		while (true) {
			$nextIndex = $tokens->getNextMeaningfulToken($nextIndex);
			if ($nextIndex === null) {
				return $rightEndIndex;
			}

			$token = $tokens[$nextIndex];
			$content = $token->getContent();

			if (
				!$token->isCast()
				&& !$token->isGivenKind([T_VARIABLE, T_OBJECT_OPERATOR, T_STRING, T_CONST, T_DOUBLE_COLON, T_CONSTANT_ENCAPSED_STRING, T_LNUMBER])
				&& !in_array($content, ['(', ')', '[', ']'], true)
			) {
				return $rightEndIndex;
			}

			if ($content === ')') {
				--$braceCounter;
			}
			if ($braceCounter < 0) {
				return $rightEndIndex;
			}

			if ($content === '(') {
				$nextIndex = $tokens->findBlockEnd(Tokens::BLOCK_TYPE_PARENTHESIS_BRACE, $nextIndex);
			}

			if ($max !== null && $nextIndex > $max) {
				return $rightEndIndex;
			}

			$rightEndIndex = $nextIndex;
		}

		return $rightEndIndex;
	}

	/**
	 * @param Tokens|Token[] $tokens
	 * @param int $index
	 * @param int $leftIndexStart
	 * @param int int $leftIndexEnd
	 * @param int $rightIndexStart
	 * @param int $rightIndexEnd
	 *
	 * @return void
	 */
	protected function applyFix(Tokens $tokens, $index, $leftIndexStart, $leftIndexEnd, $rightIndexStart, $rightIndexEnd) {
		// Check if we need to inverse comparison operator
		$comparisonValue = $this->getComparisonValue($tokens, $index);

		$leftValue = '';
		for ($i = $leftIndexStart; $i <= $leftIndexEnd; ++$i) {
			$leftValue .= $tokens[$i]->getContent();
			$tokens[$i]->setContent('');
		}
		$rightValue = '';
		for ($i = $rightIndexStart; $i <= $rightIndexEnd; ++$i) {
			$rightValue .= $tokens[$i]->getContent();
			$tokens[$i]->setContent('');
		}

		$tokens[$index]->setContent($comparisonValue);
		$tokens[$leftIndexEnd]->setContent($rightValue);
		$tokens[$rightIndexStart]->setContent($leftValue);
	}

}
