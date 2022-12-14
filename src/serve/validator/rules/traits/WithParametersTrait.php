<?php

/**
 * @copyright Joe J. Howard
 * @license   https://github.com/Serve-Framework/Framework/blob/master/LICENSE
 */

namespace serve\validator\rules\traits;

use RuntimeException;

use function property_exists;
use function vsprintf;

/**
 * With parameters trait.
 *
 * @author Joe J. Howard
 */
trait WithParametersTrait
{
	/**
	 * {@inheritDoc}
	 * @suppress PhanUndeclaredProperty
	 */
	public function setParameters(array $parameters): void
	{
		if (property_exists(self::class, 'parameters'))
		{
			$this->parameters[$this->parameters[0]] = $parameters[0];
		}
	}

	/**
	 * Returns the parameter value.
	 *
	 * @param  string $name     Parameter name
	 * @param  bool   $optional Is the parameter optional?
	 * @return mixed
	 * @suppress PhanUndeclaredProperty
	 */
	protected function getParameter($name, $optional = false)
	{
		if($optional === false && !isset($this->parameters[$name]))
		{
			throw new RuntimeException(vsprintf('Missing required parameter [ %s ] for validation rule [ %s ].', [$name, static::class]));
		}

		return $this->parameters[$name] ?? null;
	}
}
