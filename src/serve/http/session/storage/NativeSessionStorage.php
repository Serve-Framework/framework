<?php

/**
 * @copyright Joe J. Howard
 * @license   https://github.com/Serve-Framework/Framework/blob/master/LICENSE
 */

namespace serve\http\session\storage;

use function function_exists;

use function session_destroy;
use function session_gc;
use function session_get_cookie_params;
use function session_id;
use function session_name;
use function session_regenerate_id;
use function session_save_path;
use function session_set_cookie_params;
use function session_start;
use function session_status;

/**
 * Session encrypt/decrypt.
 *
 * @author Joe J. Howard
 */
class NativeSessionStorage implements StoreInterface
{
    /**
     * Constructor.
     *
     * @param array  $cookieParams Assoc array of cookie configurations
     * @param string $path         Where to save the cookie files to
     */
    public function __construct(array $cookieParams = [], string $path = '')
    {
        if ($cookieParams)
        {
            $this->session_set_cookie_params($cookieParams);
        }

        if ($path)
        {
            $this->session_save_path($path);
        }
    }

    /**
     * {@inheritDoc}
     */
    public function session_save_path(string $path = '')
    {
        if ($path)
        {
            session_save_path($path);
        }

        return session_save_path();
    }

    /**
     * {@inheritDoc}
     */
    public function session_start(): void
    {
        if (session_status() == PHP_SESSION_NONE)
        {
            session_start();
        }
    }

    /**
     * {@inheritDoc}
     */
    public function session_destroy(): void
    {
        session_destroy();

        unset($_SESSION[$this->session_name()]);
    }

    /**
     * {@inheritDoc}
     */
    public function session_id(?string $id = null)
    {
        if ($id)
        {
            return session_id($id);
        }

        return session_id();
    }

    /**
     * {@inheritDoc}
     */
    public function session_name(?string $name = null)
    {
        if ($name)
        {
            return session_name($name);
        }

        return session_name();
    }

    /**
     * {@inheritDoc}
     */
    public function session_regenerate_id(bool $deleteOldSession = false): void
    {
        session_regenerate_id();
    }

    /**
     * {@inheritDoc}
     */
    public function session_set_cookie_params(array $params): void
    {
        session_set_cookie_params(
            $params['expire'],
            $params['path'],
            $params['domain'],
            $params['secure'],
            $params['httponly']
        );
    }

    /**
     * Get the cookie parameters.
     *
     * @return array
     */
    public function session_get_cookie_params()
    {
        return session_get_cookie_params();
    }

    /**
     * Collect garbage (delete expired sessions).
     */
    public function session_gc()
    {
        if (function_exists('session_gc'))
        {
            return session_gc();
        }

        return false;
    }

    /**
     * Read and return the session data.
     *
     * @return array|null
     */
    public function read()
    {
        if (isset($_SESSION) && isset($_SESSION[$this->session_name()]))
        {
            return $_SESSION[$this->session_name()];
        }

        return null;
    }

    /**
     * Write the session data.
     *
     * @param array $data Data to write to session
     */
    public function write(array $data): void
    {
        $_SESSION[$this->session_name()] = $data;
    }

    /**
     * Send the session cookie.
     */
    public function send(): void
    {

    }
}
