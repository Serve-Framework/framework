<?php

/**
 * @copyright Joe J. Howard
 * @license   https://github.com/Serve-Framework/Framework/blob/master/LICENSE
 */

namespace serve\utility;

use ReflectionClass;

use function call_user_func_array;
use function end;
use function explode;
use function is_array;
use function is_null;
use function is_string;
use function strpos;

/**
 * Callback helper.
 *
 * @author Joe J. Howard
 */
class Callback
{
	/**
	 * Call a callback closure or class method.
	 *
	 * @param  mixed $callback The callback to call
	 * @param  mixed $args     The args to call the callback with
	 * @return mixed
	 */
	public static function apply($callback, $args = null)
	{
        $args = self::normalizeArgs($args);

		// is the callback a string
        if (is_string($callback))
        {
            // Are we calling a static method
            if (strpos($callback, '::') !== false)
            {
                $segments = explode('::', $callback);

                return call_user_func_array([$segments[0], $segments[1]], $args);
            }
            else
            {
                // grab all parts based on a / separator
                $parts = explode('/', $callback);

                // collect the last index of the array
                $last = end($parts);

                // grab the class name and method call
                $segments = explode('@', $last);

                // instantiate the class
                $class = self::newClass($segments[0], $args);

                // call method
                $method = $segments[1];

                return $class->$method();
            }
        }
        else
        {
            return call_user_func_array($callback, $args);
        }
	}

    /**
     * Returns a new class object by name with args.
     *
     * @param  string $class The class name to instantiate
     * @param  array  $args  Array of args to apply to class constructor
     * @return object
     */
    public static function newClass(string $class, array $args = [])
    {
        return call_user_func_array([new ReflectionClass($class), 'newInstance'], $args);
    }

    /**
     * Converts args to array.
     *
     * @param  mixed $args The args to call the callback with
     * @return array
     */
    public static function normalizeArgs($args): array
    {
        return is_null($args) ? [] : (!is_array($args) ? [$args] : $args);
    }
}
