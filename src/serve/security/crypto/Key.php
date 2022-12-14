<?php

/**
 * @copyright Joe J. Howard
 * @license   https://github.com/Serve-Framework/Framework/blob/master/LICENSE
 */

namespace serve\security\crypto;

use function bin2hex;

use function hex2bin;
use function mb_substr;
use function random_bytes;
use function strpos;

/**
 * Encryption key converter.
 *
 * @author Joe J. Howard
 */
class Key
{
	/**
	 * Converts a binary key into its hexadecimal representation.
	 *
	 * @param  string $key Binary key
	 * @return string
	 */
	public static function encode(string $key): string
	{
		return 'hex:' . bin2hex($key);
	}

	/**
	 * Converts a hexadecimal key into its binary representation.
	 *
	 * @param  string $key Encoded key
	 * @return string
	 */
	public static function decode(string $key): string
	{
		if(strpos($key, 'hex:') === 0)
		{
			return hex2bin(mb_substr($key, 4, null, '8bit'));
		}

		return $key;
	}

	/**
	 * Generates a key.
	 *
	 * @param  int    $length Key length
	 * @return string
	 */
	public static function generate(int $length = 32): string
	{
		return random_bytes($length);
	}

	/**
	 * Generates a hex encoded key.
	 *
	 * @param  int    $length Key length
	 * @return string
	 */
	public static function generateEncoded(int $length = 32): string
	{
		return static::encode(static::generate($length));
	}
}
