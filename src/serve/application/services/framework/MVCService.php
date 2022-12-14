<?php

/**
 * @copyright Joe J. Howard
 * @license   https://github.com/Serve-Framework/Framework/blob/master/LICENSE
 */

namespace serve\application\services\framework;

use serve\application\services\Service;
use serve\mvc\view\View;

/**
 * MVC Service.
 *
 * @author Joe J. Howard
 */
class MVCService extends Service
{
	/**
	 * {@inheritDoc}
	 */
	public function register(): void
	{
		$this->container->singleton('View', function ()
		{
			return new View;
		});
	}
}
