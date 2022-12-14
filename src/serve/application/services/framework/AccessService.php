<?php

/**
 * @copyright Joe J. Howard
 * @license   https://github.com/Serve-Framework/Framework/blob/master/LICENSE
 */

namespace serve\application\services\framework;

use serve\access\Access;
use serve\application\services\Service;

/**
 * Access service.
 *
 * @author Joe J. Howard
 */
class AccessService extends Service
{
	/**
	 * {@inheritDoc}
	 */
	public function register(): void
	{
		$this->container->singleton('Access', function ($container)
		{
			return new Access($container->Request, $container->Response, $container->Filesystem, $container->Config->get('application.security.ip_blocked'), $container->Config->get('application.security.ip_whitelist'));
		});
	}
}
