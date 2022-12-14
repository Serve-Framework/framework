<?php

namespace serve\http\response;

use function in_array;

class Status {

    /**
     * HTTP response code messages.
     *
     * @var array
     */
    protected $messages =
    [
        // Informational responses
        100 => 'Continue',
        101 => 'Switching Protocols',
        102 => 'Processing',

        // Success
        200 => 'OK',
        201 => 'Created',
        202 => 'Accepted',
        203 => 'Non-Authoritative Information',
        204 => 'No Content',
        205 => 'Reset Content',
        206 => 'Partial Content',
        207 => 'Multi-Status',
        208 => 'Already Reported',
        226 => 'IM Used',

        // Redirection
        300 => 'Multiple Choices',
        301 => 'Moved Permanently',
        302 => 'Found',
        303 => 'See Other',
        304 => 'Not Modified',
        305 => 'Use Proxy',
        306 => 'Switch Proxy',
        307 => 'Temporary Redirect',
        308 => 'Permanent Redirect',

        // Client errors
        400 => 'Bad Request',
        401 => 'Unauthorized',
        402 => 'Payment Required',
        403 => 'Forbidden',
        404 => 'Not Found',
        405 => 'Method Not Allowed',
        406 => 'Not Acceptable',
        407 => 'Proxy Authentication Required',
        408 => 'Request Timeout',
        409 => 'Conflict',
        410 => 'Gone',
        411 => 'Length Required',
        412 => 'Precondition Failed',
        413 => 'Payload Too Large',
        414 => 'URI Too Long',
        415 => 'Unsupported Media Type',
        416 => 'Range Not Satisfiable',
        417 => 'Expectation Failed',
        418 => 'I\'m a teapot',
        421 => 'Misdirected Request',
        422 => 'Unprocessable Entity',
        423 => 'Locked',
        424 => 'Failed Dependency',
        426 => 'Upgrade Required',
        428 => 'Precondition Required',
        429 => 'Too Many Requests',
        431 => 'Request Header Fields Too Large',
        451 => 'Unavailable For Legal Reasons',

        // Server errors
        500 => 'Internal Server Error',
        501 => 'Not Implemented',
        502 => 'Bad Gateway',
        503 => 'Service Unavailable',
        504 => 'Gateway Time-out',
        505 => 'HTTP Version Not Supported',
        506 => 'Variant Also Negotiates',
        507 => 'Insufficient Storage',
        508 => 'Loop Detected',
        510 => 'Not Extended',
        511 => 'Network Authentication Required',

        // Unofficial codes
        103 => 'Checkpoint',
        420 => 'Method Failure',
        450 => 'Blocked by Windows Parental Controls',
        498 => 'Invalid Token',
        499 => 'Token Required',
        509 => 'Bandwidth Limit Exceeded',
        530 => 'Site is frozen',
        598 => 'Network read timeout error',
        599 => 'Network connect timeout error',
        440 => 'Login Time-out',
        449 => 'Retry With',
        444 => 'No Response',
        495 => 'SSL Certificate Error',
        496 => 'SSL Certificate Required',
        497 => 'HTTP Request Sent to HTTPS Port',
        520 => 'Unknown Error',
        521 => 'Web Server Is Down',
        522 => 'Connection Timed Out',
        523 => 'Origin Is Unreachable',
        524 => 'A Timeout Occurred',
        525 => 'SSL Handshake Failed',
        526 => 'Invalid SSL Certificate',
        527 => 'Railgun Error',
    ];

    /**
     * The HTTP response code.
     *
     * @var int
     */
    protected $code = 200;

    /**
     * Constructor.
     */
    public function __construct(int $code = 200)
    {
        $this->code = $code;
    }

    /**
     * Set the code.
     *
     * @param int $code The response code to set
     */
    public function set(int $code): void
    {
        $this->code = $code;
    }

    /**
     * Get the code.
     *
     * @return int
     */
    public function get(): int
    {
        return $this->code;
    }

    /**
     * Get the message for the current code.
     *
     * @return string|null
     */
    public function message()
    {
        if (isset($this->messages[$this->code]))
        {
            return $this->messages[$this->code];
        }

        return null;
    }

    /**
     * Is the response not modified.
     *
     * @return bool
     */
    public function isNotModified(): bool
    {
        return $this->code === 304;
    }

    /**
     * Is the response empty.
     *
     * @return bool
     */
    public function isEmpty(): bool
    {
        return $this->code === 204;
    }

    /**
     * Is this an informational response.
     *
     * @return bool
     */
    public function isInformational(): bool
    {
        return $this->code >= 100 && $this->code < 200;
    }

    /**
     * Is the response ok 200?
     *
     * @return bool
     */
    public function isOk(): bool
    {
        return $this->code === 200;
    }

    /**
     * Is the response successful ?
     *
     * @return bool
     */
    public function isSuccessful(): bool
    {
        return $this->code >= 200 && $this->code < 300;
    }

    /**
     * Is the response a redirect ?
     *
     * @return bool
     */
    public function isRedirect(): bool
    {
        return in_array($this->code, [301, 302, 303, 307]);
    }

    /**
     * Is the response forbidden ?
     *
     * @return bool
     */
    public function isForbidden(): bool
    {
        return $this->code === 403;
    }

    /**
     * Is the response 404 not found ?
     *
     * @return bool
     */
    public function isNotFound(): bool
    {
        return $this->code === 404;
    }

    /**
     * Is this a client error ?
     *
     * @return bool
     */
    public function isClientError(): bool
    {
        return $this->code >= 400 && $this->code < 500;
    }

    /**
     * Is this a server error ?
     *
     * @return bool
     */
    public function isServerError(): bool
    {
        return $this->code >= 500 && $this->code < 600;
    }
}
