<?php

/**
 * @copyright Joe J. Howard
 * @license   https://github.com/Serve-Framework/Framework/blob/master/LICENSE
 */

namespace serve\utility;

use function array_keys;

use function array_map;
use function array_merge;
use function curl_close;
use function curl_exec;
use function curl_init;
use function curl_setopt_array;
use function implode;
use function json_decode;
use function json_encode;
use function json_last_error;
use function urlencode;

/**
 * curl Helper.
 *
 * @author Joe J. Howard
 */
class Curl
{
	/**
	 * Send a CURL request to a url.
	 *
	 * @param  string      $url         The url to request to
	 * @param  array       $headers     Any additional headers to send (optional) (default [])
	 * @param  array       $postData    POST fields (optional) (default [])
	 * @param  string      $auth        HTTP 'Authorization' header (optional) (default '')
	 * @param  string      $contentType HTTP content type header (optional) (default 'application/json')
	 * @param  string      $accept      HTTP accept header (optional) (default 'application/json')
	 * @return array|false
	 */
	public static function post(string $url, array $headers = [], array $postData = [], $auth = '', string $contentType = 'application/json', $accept = 'application/json')
	{
		// Headers
		$_headers =
		[
			'Content-Type : ' . $contentType,
			'Accept : ' . $accept,
		];

		if ($auth)
		{
			$_headers[]= 'Authorization : ' . $auth;
		}

		$headers = array_merge($_headers, $headers);

		// Options
		$options =
		[
			CURLOPT_POST           => true,
		   	CURLOPT_RETURNTRANSFER => true,
		   	CURLOPT_HTTPHEADER     => $headers,
		];
		if ($postData)
		{
			$options[CURLOPT_POSTFIELDS] = json_encode($postData);
		}

		// Curl init
		$handle = curl_init($url);

		// Add options
		curl_setopt_array($handle, $options);

		// Send the request
		$response = curl_exec($handle);

		// Check for errors
		if ($response === false)
		{
		    return false;
		}

		// Decode the response
		$response = json_decode($response, true);

		if (json_last_error() == JSON_ERROR_NONE)
		{
			return $response;
		}

		return false;
	}

	/**
	 * Send a CURL request to a url.
	 *
	 * @param  string       $url         The url to request to
	 * @param  array        $_options    Any additional options to use (optional) (default [])
	 * @param  array        $headers     Any additional headers to send (optional) (default [])
	 * @param  array        $queries     Query string as array (optional) (default [])
	 * @param  string       $auth        HTTP 'Authorization' header (optional) (default '')
	 * @param  string       $contentType HTTP content type header (optional) (default 'application/json')
	 * @param  string       $accept      HTTP accept header (optional) (default 'application/json')
	 * @return false|string
	 */
	public static function get(string $url, array $_options = [], array $headers = [], array $queries = [], $auth = '', string $contentType = 'text/html', $accept = 'text/html,application/xhtml+xml,application/xml;q=0.9,image/webp,image/apng,*/*;q=0.8,application/signed-exchange;v=b3')
	{
		// Headers
		$_headers =
		[
			'Content-Type : ' . $contentType,
			'Accept : ' . $accept,
		];

		if ($auth)
		{
			$_headers[]= 'Authorization : ' . $auth;
		}

		$headers = array_merge($_headers, $headers);

		// Options
		$options =
		[
			CURLOPT_POST           => false,
		   	CURLOPT_RETURNTRANSFER => true,
		   	CURLOPT_FOLLOWLOCATION => true,
		   	CURLOPT_HTTPHEADER     => $headers,
		];

		foreach ($_options as $k => $v)
		{
			$options[$k] = $v;
		}

		if ($queries)
		{
			$url = $url . '?' . implode('&', array_map(
			    function ($v, $k)
			    {
			    	return urlencode($k) . '=' . urlencode($v);
				},
			    $queries,
			    array_keys($queries)
			));
		}

		// Curl init
		$handle = curl_init($url);

		// Add options
		curl_setopt_array($handle, $options);

		// Send the request
		$response = curl_exec($handle);

		curl_close($handle);

		return $response;
	}
}
