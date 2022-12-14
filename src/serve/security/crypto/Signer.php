<?php

/**
 * @copyright Joe J. Howard
 * @license   https://github.com/Serve-Framework/Framework/blob/master/LICENSE
 */

namespace serve\security\crypto;

use function hash_hmac;

use function min;
use function ord;
use function strlen;
use function substr;

/**
 * Encryption/Decryption signer.
 *
 * @author Joe J. Howard
 */
class Signer
{
	/**
	 * MAC length.
	 *
	 * @var int
	 */
	public const MAC_LENGTH = 64;

	/**
	 * Secret used to sign and validate strings.
	 *
	 * @var string
	 */
	protected $secret;

	/**
	 * Constructor.
	 *
	 * @param string $secret Secret used to sign and validate strings
	 */
	public function __construct(string $secret)
	{
		$this->secret = $secret;
	}

	/**
	 * Returns the signature.
	 *
	 * @param  string $string The string you want to sign
	 * @return string
	 */
	protected function getSignature(string $string): string
	{
		return hash_hmac('sha256', $string, $this->secret);
	}

	/**
	 * Returns a signed string.
	 *
	 * @param  string $string The string you want to sign
	 * @return string
	 */
	public function sign(string $string): string
	{
		return $this->getSignature($string) . $string;
	}

	/**
	 * Returns the original string if the signature is valid or FALSE if not.
	 *
	 * @param  string      $string The string you want to validate
	 * @return bool|string
	 */
	public function validate(string $string)
	{
		$validated = substr($string, static::MAC_LENGTH);

		if(self::compare($this->getSignature($validated), substr($string, 0, static::MAC_LENGTH)))
		{
			return $validated;
		}

		return false;
	}

	/**
	 * Compares to strings properly.
	 *
	 * @param  string $string1 The first string
	 * @param  string $string2 The second string
	 * @return bool
	 */
	private static function compare(string $string1, string $string2): bool
	{
		$string1 = (string) $string1;
		$string2 = (string) $string2;

		$string1Length = strlen($string1);
		$string2Length = strlen($string2);

		$minLength = min($string1Length, $string2Length);

		$result = $string1Length ^ $string2Length;

		for($i = 0; $i < $minLength; $i++)
		{
			$result |= ord($string1[$i]) ^ ord($string2[$i]);
		}

		return $result === 0;
	}
}
