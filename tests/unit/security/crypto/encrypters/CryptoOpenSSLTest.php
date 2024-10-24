<?php

/**
 * @copyright Joe J. Howard
 * @license   https://github.com/Serve-Framework/Framework/blob/master/LICENSE
 */

namespace serve\tests\unit\security\crypto\encrypters;

use serve\security\crypto\encrypters\OpenSSL;
use serve\tests\TestCase;

/**
 * @group unit
 */
class CryptoOpenSslTest extends TestCase
{
	/**
	 *
	 */
	public function testEncryptDecrypt(): void
	{
		$data = 'foobar!!$#$@#"$#@!$P:{';

		$encrypter = new OpenSSL('secret-code');

		$hashed = $encrypter->encrypt($data);

		$this->assertEquals($data, $encrypter->decrypt($hashed));
	}

	/**
	 *
	 */
	public function testCyphers(): void
	{
		$data = 'foobar!!$#$@#"$#@!$P:{';

		$encrypter = new OpenSSL('secret-code');

		foreach ($encrypter->cyphers() as $cypher)
		{
			$encrypter = new OpenSSL('secret-code', $cypher);

			$hashed = $encrypter->encrypt($data);

			$this->assertEquals($data, $encrypter->decrypt($hashed));
		}
	}
}
