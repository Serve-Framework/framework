<?php

/**
 * @copyright Joe J. Howard
 * @license   https://github.com/Serve-Framework/Framework/blob/master/LICENSE
 */

namespace serve\validator\rules;

use serve\validator\rules\traits\WithParametersTrait;

use function mb_strlen;

use function sprintf;

/**
 * Max length rule.
 *
 * @author Joe J. Howard
 */
class MaxLength extends Rule implements RuleInterface, WithParametersInterface
{
	use WithParametersTrait;

	/**
	 * Parameters.
	 *
	 * @var array
	 */
	protected $parameters = ['maxLength'];

	/**
	 * {@inheritDoc}
	 */
	public function validate($value, array $input): bool
	{
		return mb_strlen($value) <= $this->getParameter('maxLength');
	}

	/**
	 * {@inheritDoc}
	 */
	public function getErrorMessage(string $field): string
	{
		return sprintf('The value of The "%1$s" field must be at most %2$s characters long.', $field, $this->getParameter('maxLength'));
	}
}
