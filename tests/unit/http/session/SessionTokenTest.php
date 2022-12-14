<?php

/**
 * @copyright Joe J. Howard
 * @license   https://github.com/Serve-Framework/Framework/blob/master/LICENSE
 */

namespace serve\tests\unit\http\session;

use serve\http\session\Token;
use serve\tests\TestCase;

/**
 * @group unit
 */
class SessionTokenTest extends TestCase
{
	/**
	 *
	 */
	public function testDefault(): void
	{
		$token = new Token;

		$this->assertEquals('', $token->get());
	}

	/**
	 *
	 */
	public function testSet(): void
	{
		$token = new Token;

		$token->set('fobar');

		$this->assertEquals('fobar', $token->get());
	}

	/**
	 *
	 */
	public function testRegenerate(): void
	{
		$token = new Token;

		$token->set('fobar');

		$this->assertEquals('fobar', $token->get());

		$token->regenerate();

		$this->assertFalse($token->get() === 'foobar');
	}

	/**
	 *
	 */
	public function testVerify(): void
	{
		$token = new Token;

		$token->set('fobar');

		$this->assertEquals('fobar', $token->get());

		$this->assertTrue($token->verify('fobar'));
	}
}
