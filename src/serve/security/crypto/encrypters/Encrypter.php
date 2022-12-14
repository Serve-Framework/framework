<?php

/**
 * @copyright Joe J. Howard
 * @license   https://github.com/Serve-Framework/Framework/blob/master/LICENSE
 */

namespace serve\security\crypto\encrypters;

use function hash_pbkdf2;

/**
 * Encryption/Decryption base.
 *
 * @author Joe J. Howard
 */
abstract class Encrypter
{
	/**
	 * Derivation hash.
	 *
	 * @var string
	 */
	public const DERIVATION_HASH = 'sha256';

	/**
	 * Derivation iterations.
	 *
	 * @var int
	 */
	public const DERIVATION_ITERATIONS = 1024;

	/**
	 * Generate a PBKDF2 key derivation of a supplied key.
	 *
	 * @param  string $key     The key to derive
	 * @param  string $salt    The salt
	 * @param  int    $keySize The desired key size
	 * @return string
	 */
	protected function deriveKey(string $key, string $salt, int $keySize): string
	{
		return hash_pbkdf2(static::DERIVATION_HASH, $key, $salt, static::DERIVATION_ITERATIONS, $keySize, true);
	}
}
