<?php

/**
 * @copyright Joe J. Howard
 * @license   https://github.com/Serve-Framework/Framework/blob/master/LICENSE
 */

namespace serve\http\request;

use serve\utility\Mime;
use serve\utility\Str;

use function array_merge;
use function count;
use function explode;
use function intval;
use function parse_url;
use function preg_match_all;
use function rtrim;
use function strtolower;
use function strtoupper;
use function trim;
use function urldecode;

/**
 * Request manager class.
 *
 * @author Joe J. Howard
 */
class Request
{
    /**
     * Request method constants.
     *
     * @var string
     */
    public const METHOD_HEAD     = 'HEAD';
    public const METHOD_GET      = 'GET';
    public const METHOD_POST     = 'POST';
    public const METHOD_PUT      = 'PUT';
    public const METHOD_PATCH    = 'PATCH';
    public const METHOD_DELETE   = 'DELETE';
    public const METHOD_OPTIONS  = 'OPTIONS';
    public const METHOD_OVERRIDE = '_METHOD';

    /**
     * Request headers.
     *
     * @var \serve\http\request\Headers
     */
    private $headers;

    /**
     * Http Environment.
     *
     * @var \serve\http\request\Environment
     */
    public $environment;

    /**
     * Http files.
     *
     * @var \serve\http\request\Files
     */
    private $files;

    /**
     * List of bot user agnets.
     *
     * @var array
     */
    private $bots =
    [
        'bot',
        'slurp',
        'crawler',
        'spider',
        'curl',
        'facebook',
        'fetch',
        'github',
    ];

    /**
     * Constructor.
     *
     * @param \serve\http\request\Environment $environment Environment wrapper
     * @param \serve\http\request\Headers     $headers     Headers wrapper
     * @param \serve\http\request\Files       $files       Files wrapper
     */
    public function __construct(Environment $environment, Headers $headers, Files $files)
    {
        $this->environment = $environment;

        $this->headers = $headers;

        $this->files = $files;
    }

    /**
     * Trimmed request path.
     *
     * @return string
     */
    public function path(): string
    {
        $path = parse_url(trim($this->environment->REQUEST_URI, '/'), PHP_URL_PATH);

        if (!$path)
        {
            return '';
        }

        return $path;
    }

    /**
     * Environment access.
     *
     * @return \serve\http\request\Environment
     */
    public function environment(): Environment
    {
        return $this->environment;
    }

    /**
     * Headers access.
     *
     * @return \serve\http\request\Headers
     */
    public function headers(): Headers
    {
        return $this->headers;
    }

    /**
     * Returns uploaded files wrapper.
     *
     * @return \serve\http\request\Files
     */
    public function files(): Files
    {
        return $this->files;
    }

    /**
     * Returns the HTTP request method.
     *
     * @return string
     */
    public function getMethod(): string
    {
        return strtoupper($this->environment->REQUEST_METHOD);
    }

    /**
     * Is this a secure request ?
     *
     * @return bool
     */
    public function isSecure(): bool
    {
        return strtolower($this->environment->HTTP_PROTOCOL) === 'https';
    }

    /**
     * Is this a GET request?
     *
     * @return bool
     */
    public function isGet(): bool
    {
        return $this->getMethod() === self::METHOD_GET;
    }

    /**
     * Is this a POST request?
     *
     * @return bool
     */
    public function isPost(): bool
    {
        return $this->getMethod() === self::METHOD_POST;
    }

    /**
     * Is this a PUT request?
     *
     * @return bool
     */
    public function isPut(): bool
    {
        return $this->getMethod() === self::METHOD_PUT;
    }

    /**
     * Is this a PATCH request?
     *
     * @return bool
     */
    public function isPatch(): bool
    {
        return $this->getMethod() === self::METHOD_PATCH;
    }

    /**
     * Is this a DELETE request?
     *
     * @return bool
     */
    public function isDelete(): bool
    {
        return $this->getMethod() === self::METHOD_DELETE;
    }

    /**
     * Is this a HEAD request?
     *
     * @return bool
     */
    public function isHead(): bool
    {
        return $this->getMethod() === self::METHOD_HEAD;
    }

    /**
     * Is this an OPTIONS request?
     *
     * @return bool
     */
    public function isOptions(): bool
    {
        return $this->getMethod() === self::METHOD_OPTIONS;
    }

    /**
     * Is this an Ajax request?
     *
     * @return bool
     */
    public function isAjax(): bool
    {
        if (!$this->isPost())
        {
            return false;
        }

        $headers = $this->headers->asArray();

        if (isset($headers['REQUESTED_WITH']) && $headers['REQUESTED_WITH'] === 'XMLHttpRequest')
        {
            return true;
        }
        elseif (isset($headers['HTTP_REQUESTED_WITH']) &&  $headers['HTTP_REQUESTED_WITH'] === 'XMLHttpRequest')
        {
            return true;
        }
        elseif (isset($headers['HTTP_X_REQUESTED_WITH']) &&  $headers['HTTP_X_REQUESTED_WITH'] === 'XMLHttpRequest')
        {
            return true;
        }
        elseif (isset($headers['X_REQUESTED_WITH']) &&  $headers['X_REQUESTED_WITH'] === 'XMLHttpRequest')
        {
            return true;
        }

        return false;
    }

    /**
     * Is this a GET request for file ?
     *
     * @return bool
     */
    public function isFileGet(): bool
    {
        if ($this->isGet())
        {
            if ($this->mimeType())
            {
                return true;
            }
        }

        return false;
    }

    /**
     * Fetch GET and POST request data.
     *
     * This method returns a union of GET and POST data as a key-value array, or the value
     * of the array key if requested; if the array key does not exist, false is returned.
     *
     * @param  string|null $key (optional) (default null)
     * @return mixed
     */
    public function fetch(?string $key = null)
    {
        $env = $this->environment->asArray();

        $data = parse_url(rtrim($env['HTTP_HOST'] . $env['REQUEST_URI'], '/'));

        $data['page'] = 0;

        preg_match_all("/page\/(\d+)/", $env['REQUEST_URI'], $page);

        if (isset($page[1][0]) && !empty($page[1][0]))
        {
            $data['page'] = intval($page[1][0]);
        }

        if ($data['page'] === 1)
        {
            $data['page'] = 0;
        }

        if ($this->isPost())
        {
            foreach (array_merge($this->queries(), $_POST) as $k => $v)
            {
                $data[$k] = $v;
            }
        }
        else
        {
            foreach (array_merge($this->queries(), $_GET) as $k => $v)
            {
                $data[$k] = $v;
            }
        }

        if ($key)
        {
            if (isset($data[$key]))
            {
                return $data[$key];
            }

            return false;
        }

        return $data;
    }

    /**
     * Fetch and parse url queries.
     *
     * This method fetches and parses url queries
     * eg example.com?foo=bar -> ['foo' => 'bar'];
     *
     * @param  string|null $_key (optional) (default null)
     * @return mixed
     */
    public function queries(?string $_key = null)
    {
        $result   = [];

        $queryStr = trim($this->environment->QUERY_STRING, '/');

        if ($queryStr !== '')
        {
            $querySets = explode('&', $queryStr);

            if (count($querySets) > 0)
            {
                foreach ($querySets as $querySet)
                {
                    if (Str::contains($querySet, '='))
                    {
                        $querySet = explode('=', $querySet);
                        $key      = urldecode($querySet[0]);
                        $value    = urldecode($querySet[1]);

                        if (empty($value))
                        {
                            $value = null;
                        }

                        $result[$key] = $value;
                    }
                }
            }
        }
        if ($_key)
        {
            if (isset($result[$_key]))
            {
                return $result[$_key];
            }

            return null;
        }

        return $result;
    }

    /**
     * Get MIME Type (type/subtype within Content Type header).
     *
     * @return false|string
     */
    public function mimeType()
    {
        $pathinfo = $this->fetch();

        if (isset($pathinfo['path']))
        {
            return Mime::fromExt(Str::getAfterLastChar($pathinfo['path'], '.'));
        }

        return false;
    }

    /**
     * Is the user-agent a bot?
     *
     * @return bool
     */
    public function isBot(): bool
    {
        $userAgent = $this->headers->HTTP_USER_AGENT;

        if ($userAgent)
        {
            $userAgent = strtolower($userAgent);

            foreach ($this->bots as $identifier)
            {
                if (Str::contains($userAgent, $identifier))
                {
                    return true;
                }
            }
        }

        return false;
    }
}
