<?php

/**
 * @copyright Joe J. Howard
 * @license   https://github.com/Serve-Framework/Framework/blob/master/LICENSE
 */

namespace serve\tests\unit\onion;

use Closure;
use serve\http\request\Request;
use serve\http\response\Response;
use serve\onion\Middleware;
use serve\tests\TestCase;

use function ob_get_clean;
use function ob_start;

// --------------------------------------------------------------------------
// START CLASSES
// --------------------------------------------------------------------------

class MiddleWareCallbackTest
{
	protected $value;

	public function __construct(Request $request, Response $response, Closure $next, $arg1, $arg2)
    {
    	$this->value = $arg1 . $arg2;
    }

    public function normalMethod(): void
    {
    	echo $this->value;
    }

	public static function staticFunc(Request $request, Response $response, Closure $next, $arg1, $arg2): void
	{
		echo $arg1 . $arg2;
	}
}

// --------------------------------------------------------------------------
// END CLASSES
// --------------------------------------------------------------------------

/**
 * @group unit
 */
class MiddlewareTest extends TestCase
{
	/**
	 *
	 */
	public function testNonStaticClassMethod(): void
	{
		ob_start();

		$request = $this->mock(Request::class);

		$response = $this->mock(Response::class);

		$layer = new Middleware(MiddleWareCallbackTest::class . '@normalMethod', ['foo', 'bar']);

		$next = function (): void
		{

		};

		$layer->execute($request, $response, $next);

		$this->assertEquals('foobar', ob_get_clean());
	}

	/**
	 *
	 */
	public function testStaticClassMethod(): void
	{
		ob_start();

		$request = $this->mock(Request::class);

		$response = $this->mock(Response::class);

		$layer = new Middleware(MiddleWareCallbackTest::class . '::staticFunc', ['foo', 'bar']);

		$next = function (): void
		{

		};

		$layer->execute($request, $response, $next);

		$this->assertEquals('foobar', ob_get_clean());
	}

	/**
	 *
	 */
	public function testColsure(): void
	{
		ob_start();

		$callback = function (Request $request, Response $response, $next, $foo): void
		{
			echo $foo;
		};

		$request = $this->mock(Request::class);

		$response = $this->mock(Response::class);

		$layer = new Middleware($callback, ['foo', 'bar']);

		$layer->execute($request, $response, $callback);

		$this->assertEquals('foo', ob_get_clean());
	}

	/**
	 *
	 */
	public function testGetCallback(): void
	{
		$layer = new Middleware(MiddleWareCallbackTest::class . '::staticFunc', ['foo', 'bar']);

		$this->assertEquals(MiddleWareCallbackTest::class . '::staticFunc', $layer->getCallback());
	}

	/**
	 *
	 */
	public function testGetArgs(): void
	{
		$layer = new Middleware(MiddleWareCallbackTest::class . '::staticFunc', ['foo', 'bar']);

		$this->assertEquals(['foo', 'bar'], $layer->getArgs());

		$layer = new Middleware(MiddleWareCallbackTest::class . '::staticFunc', 'foo');

		$this->assertEquals(['foo'], $layer->getArgs());
	}
}
