<?php

/**
 * @copyright Joe J. Howard
 * @license   https://github.com/Serve-Framework/Framework/blob/master/LICENSE
 */

namespace serve\validator\rules;

use function preg_match;

use function sprintf;

/**
 * Alphanumeric dash rule.
 *
 * @author Joe J. Howard
 */
class AlphanumericDash extends Rule implements RuleInterface
{
	/**
	 * {@inheritDoc}
	 */
	public function validate($value, array $input): bool
	{
		return preg_match('/^[a-z0-9_-]+$/i', $value) === 1;
	}

	/**
	 * {@inheritDoc}
	 */
	public function getErrorMessage(string $field): string
	{
		return sprintf('The "%1$s" field must contain only numbers, letters and dashes.', $field);
	}
}
