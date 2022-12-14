<?php

/**
 * @copyright Joe J. Howard
 * @license   https://github.com/Serve-Framework/Framework/blob/master/LICENSE
 */

namespace serve\http\request;

use function array_merge;

use function array_values;
use function explode;
use function krsort;
use function str_replace;
use function strpos;
use function strtoupper;
use function substr;
use function trim;

/**
 * Request headers class.
 *
 * @author   Joe J. Howard
 * @property string $SERVER_ADDR
 * @property string $SERVER_ADDR
 * @property string $SERVER_NAME
 * @property string $SERVER_SOFTWARE
 * @property string $SERVER_PROTOCOL
 * @property string $REQUEST_METHOD
 * @property string $REQUEST_TIME
 * @property string $REQUEST_TIME_FLOAT
 * @property string $QUERY_STRING
 * @property string $DOCUMENT_ROOT
 * @property string $HTTP_ACCEPT
 * @property string $HTTP_ACCEPT_CHARSET
 * @property string $HTTP_ACCEPT_ENCODING
 * @property string $HTTP_ACCEPT_LANGUAGE
 * @property string $HTTP_CONNECTION
 * @property string $HTTP_HOST
 * @property string $HTTP_REFERER
 * @property string $HTTP_USER_AGENT
 * @property string $HTTPS
 * @property string $REMOTE_ADDR
 * @property string $REMOTE_HOST
 * @property string $REMOTE_PORT
 * @property string $REMOTE_USER
 * @property string $REDIRECT_REMOTE_USER
 * @property string $SCRIPT_FILENAME
 * @property string $SERVER_ADMIN
 * @property string $SERVER_PORT
 * @property string $SERVER_SIGNATURE
 * @property string $PATH_TRANSLATED
 * @property string $SCRIPT_NAME
 * @property string $REQUEST_URI
 * @property string $PHP_AUTH_DIGEST
 * @property string $PHP_AUTH_USER
 * @property string $PHP_AUTH_PW
 * @property string $AUTH_TYPE
 * @property string $PATH_INFO
 * @property string $ORIG_PATH_INFO
 * @property string $HTTP_X_HUB_SIGNATURE
 * @property string $HTTP_X_GITHUB_EVENT
 * @property string $HTTP_IF_NONE_MATCH
 *
 * @property string $X_CONTENT_TYPE
 * @property string $X_CONTENT_LENGTH
 * @property string $X_PHP_AUTH_USER
 * @property string $X_PHP_AUTH_PW
 * @property string $X_PHP_AUTH_DIGEST
 * @property string $X_AUTH_TYPE
 * @property string $X_X-PJAX
 *
 * @property string $HTTP_CONTENT_TYPE
 * @property string $HTTP_CONTENT_LENGTH
 * @property string $HTTP_PHP_AUTH_USER
 * @property string $HTTP_PHP_AUTH_PW
 * @property string $HTTP_PHP_AUTH_DIGEST
 * @property string $HTTP_AUTH_TYPE
 * @property string $HTTP_X-PJAX
 */
class Headers
{
    /**
     * Array access.
     *
     * @var array
     */
    protected $data = [];

    /**
     * Acceptable content types.
     *
     * @var array
     */
    protected $acceptableContentTypes;

    /**
     * Acceptable languages.
     *
     * @var array
     */
    protected $acceptableLanguages;

    /**
     * Acceptable character sets.
     *
     * @var array
     */
    protected $acceptableCharsets;

    /**
     * Acceptable encodings.
     *
     * @var array
     */
    protected $acceptableEncodings;

    /**
     * Special-case HTTP headers that are otherwise unidentifiable as HTTP headers.
     * Typically, HTTP headers in the $_SERVER array will be prefixed with
     * `HTTP_` or `X_`. These are not so we list them here for later reference.
     *
     * @var array
     */
    private $special =
    [
        'CONTENT_TYPE',
        'CONTENT_LENGTH',
        'PHP_AUTH_USER',
        'PHP_AUTH_PW',
        'PHP_AUTH_DIGEST',
        'AUTH_TYPE',
        'X-PJAX',
    ];

    /**
     * Constructor. Loads the properties internally.
     *
     * @param array $server Optional server overrides (optional) (default [])
     */
    public function __construct(array $server = [])
    {
        $this->data = $this->extract($server);
    }

    /**
     * Reload the headers.
     *
     * @param array $server Optional server overrides (optional) (default [])
     */
    public function reload(array $server = []): void
    {
        $this->data = $this->extract($server);
    }

    /**
     * Returns a fresh copy of the headers.
     *
     * @param  array $server Optional server overrides (optional) (default [])
     * @return array
     */
    private function extract($server): array
    {
        $results = [];

        $server = empty($server) ? $_SERVER : $server;

        // Loop through the $_SERVER superglobal and save result consistently
        foreach ($server as $key => $value)
        {
            $key = strtoupper($key);

            if (strpos($key, 'X_') === 0 || strpos($key, 'HTTP_') === 0 || [$key, $this->special])
            {
                if ($key === 'HTTP_CONTENT_LENGTH')
                {
                    continue;
                }

                $results[$this->normalizeKey($key)] = $value;
            }
        }
        return $results;
    }

    /**
     * Normalizes header names.
     *
     * @param  string $name Header name
     * @return string
     */
    protected function normalizeKey(string $name): string
    {
        return strtoupper(str_replace('-', '_', $name));
    }

    /**
     * Parses a accpet header and returns the values in descending order of preference.
     *
     * @param  string|null $headerValue Header value
     * @return array
     */
    protected function parseAcceptHeader(?string $headerValue = null): array
    {
        $groupedAccepts = [];

        if(empty($headerValue))
        {
            return $groupedAccepts;
        }

        // Collect acceptable values
        foreach(explode(',', $headerValue) as $accept)
        {
            $quality = 1;
            if(strpos($accept, ';'))
            {
                // We have a quality so we need to split some more
                [$accept, $quality] = explode(';', $accept, 2);
                // Strip the "q=" part so that we're left with only the numeric value
                $quality = substr(trim($quality), 2);
            }
            $groupedAccepts[$quality][] = trim($accept);
        }
        // Sort in descending order of preference
        krsort($groupedAccepts);
        // Flatten array and return it
        return array_merge(...array_values($groupedAccepts));
    }

    /**
     * Returns an array of acceptable content types in descending order of preference.
     *
     * @param  string|null $default Default content type
     * @return array
     */
    public function acceptableContentTypes(?string $default = null): array
    {
        if (!isset($this->acceptableContentTypes) && isset($this->data['HTTP_ACCEPT']))
        {
            $this->acceptableContentTypes = $this->parseAcceptHeader($this->data['HTTP_ACCEPT']);
        }

        return $this->acceptableContentTypes ?: (array) $default;
    }

    /**
     * Returns an array of acceptable content types in descending order of preference.
     *
     * @param  string|null $default Default language
     * @return array
     */
    public function acceptableLanguages(?string $default = null): array
    {
        if(!isset($this->acceptableLanguages) && isset($this->data['HTTP_ACCEPT_LANGUAGE']))
        {
            $this->acceptableLanguages = $this->parseAcceptHeader($this->data['HTTP_ACCEPT_LANGUAGE']);
        }

        return $this->acceptableLanguages ?: (array) $default;
    }

    /**
     * Returns an array of acceptable content types in descending order of preference.
     *
     * @param  string|null $default Default charset
     * @return array
     */
    public function acceptableCharsets(?string $default = null): array
    {
        if(!isset($this->acceptableCharsets) && isset($this->data['HTTP_ACCEPT_CHARSET']))
        {
            $this->acceptableCharsets = $this->parseAcceptHeader($this->data['HTTP_ACCEPT_CHARSET']);
        }

        return $this->acceptableCharsets ?: (array) $default;
    }

    /**
     * Returns an array of acceptable content types in descending order of preference.
     *
     * @param  string|null $default Default encoding
     * @return array
     */
    public function acceptableEncodings(?string $default = null): array
    {
        if(!isset($this->acceptableEncodings) && isset($this->data['HTTP_ACCEPT_ENCODING']))
        {
            $this->acceptableEncodings = $this->parseAcceptHeader($this->data['HTTP_ACCEPT_ENCODING']);
        }

        return $this->acceptableEncodings ?: (array) $default;
    }

    /**
     * Return all properties.
     *
     * @return array
     */
    public function asArray(): array
    {
        return $this->data;
    }

    /**
     * Get a property by key.
     *
     * @return string|null
     */
    public function __get(string $key)
    {
        if (isset($this->data[$this->normalizeKey($key)]))
        {
            return $this->data[$this->normalizeKey($key)];
        }

        return null;
    }

    /**
     * Set a property by key.
     */
    public function __set(string $key, $value): void
    {
        $this->data[$this->normalizeKey($key)] = $value;
    }

    /**
     * Check if a property by key exists.
     *
     * @return bool
     */
    public function __isset(string $key): bool
    {
        return isset($this->data[$this->normalizeKey($key)]);
    }

    /**
     * Unset a property by key.
     */
    public function __unset(string $key): void
    {
        if (isset($this->data[$this->normalizeKey($key)]))
        {
            unset($this->data[$this->normalizeKey($key)]);
        }
    }
}
