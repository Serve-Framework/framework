<?php

/**
 * @copyright Joe J. Howard
 * @license   https://github.com/Serve-Framework/Framework/blob/master/LICENSE
 */

namespace serve\cli\output;

use serve\cli\Environment;

use function fwrite;
use function var_export;

/**
 * Output.
 *
 * @author Joe J. Howard
 */
class Output
{
	/**
	 * Output buffer stream.
	 *
	 * @var resource
	 */
	private $stdout;

	/**
	 * Formatter.
	 *
	 * @var \serve\cli\output\Formatter
	 */
	private $formatter;

    /**
     * Environment instance.
     *
     * @var \serve\cli\Environment
     */
    private $environment;

	/**
	 * Constructor.
	 *
	 * @param \serve\cli\output\Formatter $formatter   Formatter   instance
	 * @param \serve\cli\Environment      $environment Environment instance
	 * @param resource|null               $stdout      Output buffer instance (optional) (default null)
	 */
	public function __construct(Formatter $formatter, Environment $environment, $stdout = null)
	{
		$this->formatter = $formatter;

		$this->environment = $environment;

		$this->stdout = !$stdout ? STDOUT : $stdout;
	}

    /**
     * Returns the formatter.
     *
     * @return \serve\cli\output\Formatter
     */
    public function formatter(): Formatter
    {
        return $this->formatter;
    }

    /**
     * Returns the environment.
     *
     * @return \serve\cli\Environment
     */
    public function environment(): environment
    {
        return $this->environment;
    }

	/**
	 * Writes string to output.
	 *
	 * @param string $string String to write
	 */
	public function write(string $string): void
	{
		if ($this->environment->hasAnsiSupport() === false)
		{
			$string = $this->formatter->stripTags($string);
		}

		$string = $this->formatter->format($string);

		fwrite($this->stdout, $string);
	}

	/**
	 * Appends newline to string and writes it to output.
	 *
	 * @param string $string String to write
	 */
	public function writeLn(string $string): void
	{
		$this->write($string . PHP_EOL);
	}

	/**
	 * Dumps a value to the output.
	 *
	 * @param mixed $value Value
	 */
	public function dump($value): void
	{
		$this->writeLn(var_export($value, true));
	}

	/**
	 * Clears the screen.
	 */
	public function clear(): void
	{
		if($this->environment->hasAnsiSupport())
		{
			$this->write("\e[H\e[2J");
		}
	}

	/**
	 * Clears the current line.
	 */
	public function clearLine(): void
	{
		if($this->environment->hasAnsiSupport())
		{
			$this->write("\r\33[2K");
		}
	}
}
