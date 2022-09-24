<?php

/**
 * @copyright Joe J. Howard
 * @license   https://github.com/Serve-Framework/Framework/blob/master/LICENSE
 */

namespace serve\exception\handlers;

use Exception;
use serve\exception\ExceptionLogicTrait;
use serve\http\request\Request;
use serve\http\response\exceptions\MethodNotAllowedException;
use serve\http\response\exceptions\RequestException;
use serve\http\response\Response;
use serve\mvc\view\View;
use Throwable;

/**
 * Error web handler.
 *
 * @author Joe J. Howard
 */
class WebHandler
{
	use ExceptionLogicTrait;

	/**
	 * Request instance.
	 *
	 * @var \serve\http\request\Request
	 */
	protected $request;

	/**
	 * Response instance.
	 *
	 * @var \serve\http\response\Response
	 */
	protected $response;

	/**
	 * View instance.
	 *
	 * @var \serve\mvc\view\View
	 */
	protected $view;

	/**
	 * Error.
	 *
	 * @var Exception|\serve\http\response\exceptions\ForbiddenException|\serve\http\response\exceptions\InvalidTokenException|\serve\http\response\exceptions\MethodNotAllowedException|\serve\http\response\exceptions\NotFoundException|\serve\http\response\exceptions\RequestException|\serve\http\response\exceptions\Stop|Throwable
	 */
	protected $exception;

	/**
	 * Constructor.
	 *
	 * @param Throwable                     $exception Throwable
	 * @param \serve\http\request\Request   $request   Request instance
	 * @param \serve\http\response\Response $response  Response instance
	 * @param \serve\mvc\view\View          $view      View instance
	 */
	public function __construct(Throwable $exception, Request $request, Response $response, View $view)
	{
		$this->request = $request;

		$this->response = $response;

		$this->view = $view;

		$this->exception = $exception;
	}

	/**
	 * Should we return the error as JSON?
	 *
	 * @return bool
	 */
	protected function returnAsJson(): bool
	{
		$jsonMimeTypes = ['application/json', 'text/json'];

		if($this->request->isAjax() || in_array($this->response->format()->get(), $jsonMimeTypes))
		{
			return true;
		}

		return false;
	}

	/**
	 * Returns a detailed error page.
	 *
	 * @param  bool   $returnAsJson Should we return JSON?
	 * @param  bool   $isBot        Is the user-agent a bot?
	 * @return string
	 */
	protected function getDetailedError(bool $returnAsJson, bool $isBot): string
	{
		$vars =
		[
    		'errcode'      => $this->exception->getCode(),
    		'errName'      => $this->errName(),
    		'errtype'      => $this->errtype(),
    		'errtime'      => time(),
    		'errmsg'       => $this->exception->getMessage(),
    		'errfile'      => $this->exception->getFile(),
    		'errline'      => intval($this->exception->getLine()),
    		'errClass'     => $this->errClass(),
    		'errTrace'     => $this->errTrace(),
    		'errUrl'       => $this->request->environment()->REQUEST_URL,
    		'clientIP'     => $this->request->environment()->REMOTE_ADDR,
    		'logFiles'     => [],
    		'errFileLines' => $this->errSource(),
    	];

    	// Bots get a plain error message
    	if ($isBot)
    	{
    		return $vars['errmsg'];
    	}

		if ($returnAsJson)
		{
			return json_encode($vars);
		}
		else
		{
			// Return detailed error view
			return $this->view->display(dirname(__FILE__) . '/views/debug.php', $vars);
		}
	}

	/**
	 * Returns a generic error page.
	 *
	 * @param  bool   $returnAsJson Should we return JSON?
	 * @param  bool   $isBot        Is the user-agent a bot?
	 * @return string
	 */
	protected function getGenericError(bool $returnAsJson, bool $isBot): string
	{
		$code = $this->exception->getCode();

		if ($isBot)
		{
			switch($code)
			{
				case 403:
					$message = 'You don\'t have permission to access the requested resource.';
					break;
				case 404:
					$message = 'The resource you requested could not be found. It may have been moved or deleted.';
					break;
				case 405:
					$message = 'The request method that was used is not supported by this resource.';
					break;
				default:
					$message = 'An error has occurred while processing your request.';
			}

			return $message;
		}
		elseif ($returnAsJson)
		{
			switch($code)
			{
				case 403:
					$message = 'You don\'t have permission to access the requested resource.';
					break;
				case 404:
					$message = 'The resource you requested could not be found. It may have been moved or deleted.';
					break;
				case 405:
					$message = 'The request method that was used is not supported by this resource.';
					break;
				default:
					$message = 'An error has occurred while processing your request.';
			}

			return json_encode(['message' => $message]);
		}
		else
		{
			$dir = dirname(__FILE__) . '/views';

			$view = $dir . '/500.php';

			if($this->exception instanceof RequestException)
			{
				if (file_exists($dir . '/' . $code . '.php'))
				{
					$view = $dir . '/' . $code . '.php';
				}
			}

			return $this->view->display($view);
		}
	}

	/**
	 * Display an error page to end user.
	 *
	 * @param  bool  $showDetails Should we show a detailed error page
	 * @return false
	 */
	public function handle(bool $showDetails = true): bool
	{
		// Set appropriate content type header
		if (($returnAsJson = $this->returnAsJson()) === true)
		{
			$this->response->format()->set('application/json');
		}
		else
		{
			$this->response->format()->set('text/html');
		}

		// Set the response body
		if ($showDetails)
		{
			$this->response->body()->set($this->getDetailedError($returnAsJson, $this->request->isBot()));
		}
		else
		{
			$this->response->body()->set($this->getGenericError($returnAsJson, $this->request->isBot()));
		}

		// Send the response along with appropriate headers
		if ($this->exception instanceof RequestException)
		{
			$status = $this->exception->getCode();

			if ($this->exception instanceof MethodNotAllowedException)
			{
				$this->response->headers()->set('allows', implode(',', $this->exception->getAllowedMethods()));
			}
		}
		else
		{
			$status = 500;
		}

		$this->response->status()->set($status);

		$this->response->send();

		// Return false to stop further error handling
		return false;
	}
}
