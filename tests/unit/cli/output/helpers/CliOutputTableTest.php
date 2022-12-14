<?php

/**
 * @copyright Joe J. Howard
 * @license   https://github.com/Serve-Framework/Framework/blob/master/LICENSE
 */

namespace serve\tests\unit\cli\output\helpers;

use RuntimeException;
use serve\cli\output\Formatter;
use serve\cli\output\helpers\Table;
use serve\cli\output\Output;
use serve\tests\TestCase;

/**
 * @group unit
 */
class CliOutputTableTest extends TestCase
{
	/**
	 *
	 */
	public function testBasicTable(): void
	{
		$formatter = new Formatter;
		$output    = $this->mock(Output::class);

		$output->shouldReceive('formatter')->once()->andReturn($formatter);

		$table = new Table($output);

		$expected  = '';
		$expected .= '---------' . PHP_EOL;
		$expected .= '| Col1  |' . PHP_EOL;
		$expected .= '---------' . PHP_EOL;
		$expected .= '| Cell1 |' . PHP_EOL;
		$expected .= '---------' . PHP_EOL;

		$this->assertSame($expected, $table->render(['Col1'], [['Cell1']]));
	}

	/**
	 *
	 */
	public function testTableWithMultipleRows(): void
	{
		$formatter = new Formatter;
		$output    = $this->mock(Output::class);

		$output->shouldReceive('formatter')->once()->andReturn($formatter);

		$table = new Table($output);

		$expected  = '';
		$expected .= '---------' . PHP_EOL;
		$expected .= '| Col1  |' . PHP_EOL;
		$expected .= '---------' . PHP_EOL;
		$expected .= '| Cell1 |' . PHP_EOL;
		$expected .= '| Cell1 |' . PHP_EOL;
		$expected .= '---------' . PHP_EOL;

		$this->assertSame($expected, $table->render(['Col1'], [['Cell1'], ['Cell1']]));
	}

	/**
	 *
	 */
	public function testTableWithMultipleColumns(): void
	{
		$formatter = new Formatter;
		$output    = $this->mock(Output::class);

		$output->shouldReceive('formatter')->once()->andReturn($formatter);

		$table = new Table($output);

		$expected  = '';
		$expected .= '-----------------' . PHP_EOL;
		$expected .= '| Col1  | Col2  |' . PHP_EOL;
		$expected .= '-----------------' . PHP_EOL;
		$expected .= '| Cell1 | Cell2 |' . PHP_EOL;
		$expected .= '-----------------' . PHP_EOL;

		$this->assertSame($expected, $table->render(['Col1', 'Col2'], [['Cell1', 'Cell2']]));
	}

	/**
	 *
	 */
	public function testTableWithMultipleColumnsAndRows(): void
	{
		$formatter = new Formatter;
		$output    = $this->mock(Output::class);

		$output->shouldReceive('formatter')->once()->andReturn($formatter);

		$table = new Table($output);

		$expected  = '';
		$expected .= '-----------------' . PHP_EOL;
		$expected .= '| Col1  | Col2  |' . PHP_EOL;
		$expected .= '-----------------' . PHP_EOL;
		$expected .= '| Cell1 | Cell2 |' . PHP_EOL;
		$expected .= '| Cell1 | Cell2 |' . PHP_EOL;
		$expected .= '-----------------' . PHP_EOL;

		$this->assertSame($expected, $table->render(['Col1', 'Col2'], [['Cell1', 'Cell2'], ['Cell1', 'Cell2']]));
	}

	/**
	 *
	 */
	public function testStyledContent(): void
	{
		$formatter = new Formatter;
		$output    = $this->mock(Output::class);

		$output->shouldReceive('formatter')->once()->andReturn($formatter);

		$table = new Table($output);

		$expected  = '';
		$expected .= '---------' . PHP_EOL;
		$expected .= '| <blue>Col1</blue>  |' . PHP_EOL;
		$expected .= '---------' . PHP_EOL;
		$expected .= '| Cell1 |' . PHP_EOL;
		$expected .= '---------' . PHP_EOL;

		$this->assertSame($expected, $table->render(['<blue>Col1</blue>'], [['Cell1']]));
	}

	/**
	 *
	 */
	public function testInvalidInput(): void
	{
		$this->expectException(RuntimeException::class);

		$formatter = new Formatter;
		$output    = $this->mock(Output::class);

		$output->shouldReceive('formatter')->once()->andReturn($formatter);

		$table = new Table($output);

		$table->render(['Col1'], [['Cell1', 'Cell2']]);
	}
}
