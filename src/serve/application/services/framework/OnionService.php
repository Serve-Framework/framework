<?php

/**
 * @copyright Joe J. Howard
 * @license   https://github.com/Serve-Framework/Framework/blob/master/LICENSE
 */

namespace serve\application\services\framework;

use serve\application\services\Service;
use serve\onion\Onion;

/**
 * Onion/Middleware service.
 *
 * @author Joe J. Howard
 */
class OnionService extends Service
{
	/**
	 * {@inheritDoc}
	 */
	public function register(): void
	{
		$this->container->singleton('Onion', function ($container)
		{
			return new Onion($container->Request, $container->Response);
		});
	}
}
