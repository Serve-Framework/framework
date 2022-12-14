<?php

/**
 * @copyright Joe J. Howard
 * @license   https://github.com/Serve-Framework/Framework/blob/master/LICENSE
 */

namespace serve\utility;

use InvalidArgumentException;

use function bin2hex;
use function chr;
use function hexdec;
use function md5;
use function ord;
use function preg_match;
use function random_bytes;
use function sha1;
use function str_replace;
use function str_split;
use function strlen;
use function substr;
use function vsprintf;

/**
 * Class that generates and validates UUIDs.
 *
 * @author Joe J. Howard
 * @author Andrew Moore (http://www.php.net/manual/en/function.uniqid.php#94959)
 * @author Jack (http://stackoverflow.com/a/15875555)
 */
class UUID
{
	/**
	 * DNS namespace.
	 *
	 * @var string
	 */
	public const DNS = '6ba7b810-9dad-11d1-80b4-00c04fd430c8';

	/**
	 * URL namespace.
	 *
	 * @var string
	 */
	public const URL = '6ba7b811-9dad-11d1-80b4-00c04fd430c8';

	/**
	 * ISO OID namespace.
	 *
	 * @var string
	 */
	public const OID = '6ba7b812-9dad-11d1-80b4-00c04fd430c8';

	/**
	 * X.500 DN namespace.
	 *
	 * @var string
	 */
	public const X500 = '6ba7b814-9dad-11d1-80b4-00c04fd430c8';

	/**
	 * Checks if a UUID is valid.
	 *
	 * @param  string $str The UUID to validate
	 * @return bool
	 */
	public static function validate(string $str): bool
	{
		return (bool) preg_match('/^[a-f0-9]{8}-[a-f0-9]{4}-[a-f0-9]{4}-[a-f0-9]{4}-[a-f0-9]{12}$/i', $str);
	}

	/**
	 * Converts UUID to binary.
	 *
	 * @param  string $namespace UUID
	 * @return string
	 */
	protected static function toBin(string $namespace): string
	{
		if(!static::validate($namespace))
		{
			throw new InvalidArgumentException(vsprintf('%s(): Provided namespace is not a valid UUID.', [__METHOD__]));
		}

		// Get hexadecimal components of namespace

		$nhex = str_replace(['-', '{', '}'], '', $namespace);

		// Binary Value

		$nstr = '';

		// Convert Namespace UUID to bits

		$nhexLength = strlen($nhex);

		for($i = 0; $i < $nhexLength; $i+=2)
		{
			$nstr .= chr(hexdec($nhex[$i] . $nhex[$i+1]));
		}

		return $nstr;
	}

	/**
	 * Returns a V3 UUID.
	 *
	 * @param  string $namespace Namespace
	 * @param  string $name      Name
	 * @return string
	 */
	public static function v3(string $namespace, string $name): string
	{
		// Calculate hash value

		$hash = md5(self::toBin($namespace) . $name);

		return sprintf
		(
		    '%08s-%04s-%04x-%04x-%12s',

			// 32 bits for "time_low"

			substr($hash, 0, 8),

			// 16 bits for "time_mid"

			substr($hash, 8, 4),

			// 16 bits for "time_hi_and_version",
			// four most significant bits holds version number 3

			(hexdec(substr($hash, 12, 4)) & 0x0fff) | 0x3000,

			// 16 bits, 8 bits for "clk_seq_hi_res",
			// 8 bits for "clk_seq_low",
			// two most significant bits holds zero and one for variant DCE1.1

			(hexdec(substr($hash, 16, 4)) & 0x3fff) | 0x8000,

			// 48 bits for "node"

			substr($hash, 20, 12)
		);
	}

	/**
	 * Returns a V4 UUID.
	 *
	 * @return string
	 */
	public static function v4(): string
	{
		$random = random_bytes(16);

		$random[6] = chr(ord($random[6]) & 0x0f | 0x40);

		$random[8] = chr(ord($random[8]) & 0x3f | 0x80);

		return vsprintf('%s%s-%s-%s-%s-%s%s%s', str_split(bin2hex($random), 4));
	}

	/**
	 * Returns a V5 UUID.
	 *
	 * @param  string $namespace Namespace
	 * @param  string $name      Name
	 * @return string
	 */
	public static function v5(string $namespace, string $name): string
	{
		// Calculate hash value

		$hash = sha1(static::toBin($namespace) . $name);

		return sprintf
		(
		    '%08s-%04s-%04x-%04x-%12s',

			// 32 bits for "time_low"

			substr($hash, 0, 8),

			// 16 bits for "time_mid"

			substr($hash, 8, 4),

			// 16 bits for "time_hi_and_version",
			// four most significant bits holds version number 5

			(hexdec(substr($hash, 12, 4)) & 0x0fff) | 0x5000,

			// 16 bits, 8 bits for "clk_seq_hi_res",
			// 8 bits for "clk_seq_low",
			// two most significant bits holds zero and one for variant DCE1.1

			(hexdec(substr($hash, 16, 4)) & 0x3fff) | 0x8000,

			// 48 bits for "node"

			substr($hash, 20, 12)
		);
	}
}
