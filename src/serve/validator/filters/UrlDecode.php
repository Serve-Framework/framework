<?php

/**
 * @copyright Joe J. Howard
 * @license   https://github.com/Serve-Framework/Framework/blob/master/LICENSE
 */

namespace serve\validator\filters;

use function urldecode;

/**
 * Urldecode.
 *
 * @author Joe J. Howard
 */
class UrlDecode extends FilterBase implements FilterInterface
{
	/**
	 * {@inheritDoc}
	 */
	public function filter(string $value)
	{
		return urldecode($value);
	}
}
