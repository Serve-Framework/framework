<?php

/**
 * @copyright Joe J. Howard
 * @license   https://github.com/Serve-Framework/Framework/blob/master/LICENSE
 */

namespace serve\validator\rules;

use serve\validator\rules\traits\WithParametersTrait;

use function sprintf;

/**
 * Greater than or equal to rule.
 *
 * @author Joe J. Howard
 */
class GreaterThanOrEqualTo extends Rule implements RuleInterface, WithParametersInterface
{
	use WithParametersTrait;

	/**
	 * Parameters.
	 *
	 * @var array
	 */
	protected $parameters = ['greaterThanOrEqualTo'];

	/**
	 * {@inheritDoc}
	 */
	public function validate($value, array $input): bool
	{
		return (int) $value >= $this->getParameter('greaterThanOrEqualTo');
	}

	/**
	 * {@inheritDoc}
	 */
	public function getErrorMessage(string $field): string
	{
		return sprintf('The value of The "%1$s" field must be greater than or equal to %2$s.', $field, $this->getParameter('greaterThanOrEqualTo'));
	}
}
