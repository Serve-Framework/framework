<?php

/**
 * @copyright Joe J. Howard
 * @license   https://github.com/Serve-Framework/Framework/blob/master/LICENSE
 */

namespace serve\database\wrappers\providers;

use serve\database\builder\Builder;

/**
 * Provider base class.
 *
 * @author Joe J. Howard
 */
abstract class Provider
{
    /**
     * SQL query builder.
     *
     * @var \serve\database\builder\Builder
     */
    protected $SQL;

    /**
     * Constructor.
     *
     * @param \serve\database\builder\Builder $SQL SQL query builder
     */
    public function __construct(Builder $SQL)
    {
        $this->SQL = $SQL;
    }

    /**
     * Create an item.
     *
     * @return mixed
     */
    abstract public function create(array $row);

	/**
	 * Return an item by id.
	 *
	 * @param  int   $id Row id
	 * @return mixed
	 */
	abstract public function byId(int $id);

	/**
	 * Deletes the row item.
	 *
	 * @param  string $key   Column name
	 * @param  mixed  $value Column value
	 * @return mixed
	 */
	abstract public function byKey(string $key, $value, bool $single = false);
}
