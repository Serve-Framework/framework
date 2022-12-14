<?php

/**
 * @copyright Joe J. Howard
 * @license   https://github.com/Serve-Framework/Framework/blob/master/LICENSE
 */

namespace serve\tests\unit\http\response;

use serve\http\request\Files;
use serve\tests\TestCase;

use function count;
use function is_array;

/**
 * @group unit
 */
class RequestFilesTest extends TestCase
{
	/**
	 *
	 */
	protected function getSingleUpload(): array
	{
		return
		[
			'upload' =>
			[
				'name'     => 'foo',
				'tmp_name' => '/tmp/qwerty',
				'type'     => 'foo/bar',
				'size'     => 123,
				'error'    => 0,
			],
		];
	}
	/**
	 *
	 */
	protected function getMultiUpload()
	{
		return
		[
			'upload' =>
			[
				'name'     => ['foo', 'bar'],
				'tmp_name' => ['/tmp/qwerty', '/tmp/azerty'],
				'type'     => ['foo/bar', 'foo/bar'],
				'size'     => [123, 456],
				'error'    => [0, 0],
			],
		];
	}
	/**
	 *
	 */
	public function testCountSet(): void
	{
		$files = new Files($this->getSingleUpload());

		$this->assertSame(1, count($files->asArray()));

		$files = new Files($this->getMultiUpload());

		$this->assertSame(1, count($files->asArray()));
	}
	/**
	 *
	 */
	public function testAdd(): void
	{
		$files = new Files;

		$files->put('upload', $this->getSingleUpload()['upload']);

		$this->assertTrue(is_array($files->get('upload')) && !empty($files->get('upload')));
	}
	/**
	 *
	 */
	public function testGet(): void
	{
		$files = new Files($this->getSingleUpload());

		$this->assertTrue(is_array($files->get('upload')) && !empty($files->get('upload')));

		$files = new Files($this->getMultiUpload());

		$this->assertTrue(is_array($files->get('upload.0')) && !empty($files->get('upload.0')));

		$this->assertTrue(is_array($files->get('upload.1')) && !empty($files->get('upload.1')));
	}

}
