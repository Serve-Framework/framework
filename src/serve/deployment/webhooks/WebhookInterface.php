<?php

/**
 * @copyright Joe J. Howard
 * @license   https://github.com/Serve-Framework/Framework/blob/master/LICENSE
 */

namespace serve\deployment\webhooks;

/**
 * Frameowrk deployment interface.
 *
 * @author Joe J. Howard
 */
interface WebhookInterface
{
	/**
	 * Validate the incoming webhook.
	 *
	 * @return bool
	 */
	public function validate(): bool;

    /**
     * Update the framework.
     */
    public function deploy();

	/**
	 * Returns the incoming webhook event.
	 *
	 * @return string
	 */
	public function event(): string;

	/**
	 * Returns the incoming webhook event.
	 *
	 * @return array
	 */
	public function payload(): array;
}
