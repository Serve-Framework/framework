<?php

/**
 * @copyright Joe J. Howard
 * @license   https://github.com/Serve-Framework/Framework/blob/master/LICENSE
 */

namespace serve\tests\unit\http\request;

use serve\http\request\Environment;
use serve\tests\TestCase;

/**
 * @group unit
 */
class RequestEnvironmentTest extends TestCase
{
	/**
	 *
	 */
	public function getServerData()
	{
		return
		[
			'REQUEST_METHOD'  => 'GET',
			'SCRIPT_NAME'     => '/foobar/index.php',
			'SERVER_NAME'     => 'localhost',
			'SERVER_PORT'     => '8888',
			'HTTP_PROTOCOL'   => 'http',
			'DOCUMENT_ROOT'   => '/usr/name/httpdocs',
			'HTTP_HOST'       => 'http://localhost:8888',
			'DOMAIN_NAME'     => 'localhost:8888',
			'REQUEST_URI'     => '/foobar?foo=bar',
			'REMOTE_ADDR'     => '192.168.1.1',
			'HTTP_USER_AGENT' => 'Mozilla/5.0 (Windows NT 6.1; WOW64) AppleWebKit/537.17 (KHTML, like Gecko) Chrome/24.0.1312.57 Safari/537.17',
		];
	}

	/**
	 *
	 */
	public function testEnvironment(): void
	{
		$server = $this->getServerData();

		$env = new Environment($server);

		$this->assertEquals('/index.php', $env->SCRIPT_NAME);

		$server['SCRIPT_NAME'] = '/var/www/app.php';

		$env->reload($server);

		$this->assertEquals('/app.php', $env->SCRIPT_NAME);
	}

	/**
	 *
	 */
	public function testRequestMethod(): void
	{
		$env = new Environment($this->getServerData());

		$this->assertEquals('GET', $env->REQUEST_METHOD);
	}

	/**
	 *
	 */
	public function testScriptName(): void
	{
		$env = new Environment($this->getServerData());

		$this->assertEquals('/index.php', $env->SCRIPT_NAME);
	}

	/**
	 *
	 */
	public function testServerName(): void
	{
		$env = new Environment($this->getServerData());

		$this->assertEquals('localhost', $env->SERVER_NAME);

	}

	/**
	 *
	 */
	public function testServerPort(): void
	{
		$env = new Environment($this->getServerData());

		$this->assertEquals('8888', $env->SERVER_PORT);

	}

	/**
	 *
	 */
	public function testProtocol(): void
	{
		$env = new Environment($this->getServerData());

		$this->assertEquals('http', $env->HTTP_PROTOCOL);

		$server = $this->getServerData();
		$server['SERVER_PORT'] = 443;
		$server['HTTPS'] = 'on';

		$env->reload($server);

		$this->assertEquals('https', $env->HTTP_PROTOCOL);
	}

	/**
	 *
	 */
	public function testDocRoot(): void
	{
		$env = new Environment($this->getServerData());

		$this->assertEquals('/usr/name/httpdocs', $env->DOCUMENT_ROOT);
	}

	/**
	 *
	 */
	public function testHttpHost(): void
	{
		$env = new Environment($this->getServerData());

		$this->assertEquals('http://localhost:8888', $env->HTTP_HOST);
	}

	/**
	 *
	 */
	public function testDomainName(): void
	{
		$env = new Environment($this->getServerData());

		$this->assertEquals('localhost:8888', $env->DOMAIN_NAME);
	}

	/**
	 *
	 */
	public function testRequestUri(): void
	{
		$env = new Environment($this->getServerData());

		$this->assertEquals('/foobar?foo=bar', $env->REQUEST_URI);
	}

	/**
	 *
	 */
	public function testRequestUrl(): void
	{
		$env = new Environment($this->getServerData());

		$this->assertEquals('http://localhost:8888/foobar?foo=bar', $env->REQUEST_URL);
	}

	/**
	 *
	 */
	public function testRequestPath(): void
	{
		$data = $this->getServerData();

		$data['REQUEST_URI'] = '/foo/bar/?foo=bar';

		$env = new Environment($data);

		$this->assertEquals('foo/bar', $env->REQUEST_PATH);
	}

	/**
	 *
	 */
	public function testQueryStr(): void
	{
		$env = new Environment($this->getServerData());

		$this->assertEquals('foo=bar', $env->QUERY_STRING);
	}

	/**
	 *
	 */
	public function testRemoteAdd(): void
	{
		$env = new Environment($this->getServerData());

		$this->assertEquals('192.168.1.1', $env->REMOTE_ADDR);
	}

	/**
	 *
	 */
	public function testUserAgent(): void
	{
		$env = new Environment($this->getServerData());

		$this->assertEquals('Mozilla/5.0 (Windows NT 6.1; WOW64) AppleWebKit/537.17 (KHTML, like Gecko) Chrome/24.0.1312.57 Safari/537.17', $env->HTTP_USER_AGENT);
	}
}
