<?php

/**
 * @copyright Joe J. Howard
 * @license   https://github.com/Serve-Framework/Framework/blob/master/LICENSE
 */

namespace serve\console;

use serve\cli\input\Input;
use serve\cli\output\helpers\Table;
use serve\cli\output\Output;
use serve\ioc\Container;

use function array_map;
use function in_array;
use function is_null;
use function is_string;
use function ksort;

/**
 * PHP Console.
 *
 * @author Joe J. Howard
 */
class Console
{
	/**
	 * Input.
	 *
	 * @var \serve\cli\input\Input
	 */
	private $input;

	/**
	 * Output.
	 *
	 * @var \serve\cli\output\Output
	 */
	private $output;

	/**
	 * Container.
	 *
	 * @var \serve\ioc\Container|null
	 */
	private $container;

	/**
	 * Commands.
	 *
	 * @var array
	 */
	private $commands = [];

	/**
	 * Constructor.
	 *
	 * @param \serve\cli\input\Input    $input     Input
	 * @param \serve\cli\output\Output  $output    Output
	 * @param \serve\ioc\Container|null $container Container instance (optional) (default null)
	 */
	public function __construct(Input $input, Output $output, ?Container $container = null)
	{
		$this->input = $input;

		$this->output = $output;

		$this->container = $container;
	}

	/**
	 * Registers a command.
	 *
	 * @param string $command Command
	 * @param string $class   Command class
	 */
	public function registerCommand(string $command, string $class): void
	{
		$this->commands[$command] = $class;
	}

	/**
	 * Run the console.
	 */
	public function run(): void
	{
		$command = $this->input->subCommand();
		$options = $this->input->options();
		$params  = $this->input->parameters();

		// No sub command provided
		if (is_null($command))
		{
			$this->displayConsoleInfoAndCommandList();

			return;
		}

		// Command does not exist
		if($this->commandExists($command) === false)
		{
			$this->unknownCommand($command);

			return;
		}

		// Help on command
		if(in_array('help', $options) || in_array('h', $options))
		{
			$this->displayCommandHelp($command);

			return;
		}

		$commandInstance = $this->commandInstance($command);

		$commandInstance->execute();
	}

	/**
	 * Draws information table.
	 *
	 * @param string $heading Table heading
	 * @param array  $headers Table headers
	 * @param array  $rows    Table rows
	 */
	private function drawTable(string $heading, array $headers, array $rows): void
	{
		if(!empty($rows))
		{
			$this->output->write(PHP_EOL);

			$this->output->write(PHP_EOL);

			$this->output->writeLn("<yellow>{$heading}</yellow>");

			$this->output->write(PHP_EOL);

			$table = new Table($this->output);

			$headers = array_map(function ($value) { return "<green>{$value}</green>"; }, $headers);

			$this->output->write($table->render($headers, $rows));
		}
	}

	/**
	 * Draws an argument table.
	 *
	 * @param string $heading   Table heading
	 * @param array  $arguments Arguments
	 */
	private function drawArgumentTable(string $heading, array $arguments): void
	{
		$this->drawTable($heading, ['Name', 'Description', 'Optional'], $arguments);
	}

	/**
	 * Displays basic console information.
	 */
	private function displayConsoleInfo(): void
	{
		// Display basic console information

		$this->output->writeLn('<yellow>Usage:</yellow>');

		$this->output->write(PHP_EOL);

		$this->output->write('php console [command] [arguments] [options]');
	}

	/**
	 * Returns an array of command information.
	 *
	 * @return array
	 */
	private function getCommands(): array
	{
		$info = [];

		foreach($this->commands as $name => $class)
		{
			if (is_string($name) && is_string($class))
			{
				$command = new $class($this->input, $this->output);

				$info[$name] = [$name, $command->getDescription()];
			}
		}

		ksort($info);

		return $info;
	}

	/**
	 * Lists available commands if there are any.
	 */
	private function listCommands(): void
	{
		$commands = $this->getCommands();

		$this->drawTable('Available commands:', ['Command', 'Description'], $commands);
	}

	/**
	 * Displays console info and lists all available commands.
	 */
	private function displayConsoleInfoAndCommandList(): void
	{
		$this->displayConsoleInfo();

		$this->listCommands();
	}

	/**
	 * Returns TRUE if the command exists and FALSE if not.
	 *
	 * @param  string $command Command
	 * @return bool
	 */
	private function commandExists(string $command): bool
	{
		return isset($this->commands[$command]);
	}

	/**
	 * Displays error message for unknown commands.
	 *
	 * @param string $command Command
	 */
	private function unknownCommand(string $command): void
	{
		$message = "Unknown command [ {$command} ].";

		$this->output->write("<red>{$message}</red>");

		$this->listCommands();
	}

	/**
	 * Construct a command instance by name.
	 *
	 * @param  string $command Command
	 * @return mixed
	 */
	private function commandInstance(string $command)
	{
		$class = $this->commands[$command];

		return new $class($this->input, $this->output, $this->container);
	}

	/**
	 * Displays information about the chosen command.
	 *
	 * @param string $command Command
	 */
	private function displayCommandHelp(string $command): void
	{
		$commandInstance = $this->commandInstance($command);

		$this->output->write('<yellow>Command: </yellow>');

		$this->output->write("php console {$command}");

		$this->output->write(PHP_EOL);

		$this->output->write('<yellow>Description: </yellow>');

		$this->output->write($commandInstance->getDescription());

		$this->drawArgumentTable('Arguments and options:', $commandInstance->getArguments());
	}
}
