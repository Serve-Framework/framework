<?php

/**
 * @copyright Joe J. Howard
 * @license   https://github.com/Serve-Framework/Framework/blob/master/LICENSE
 */

namespace serve\tests\unit\utility;

use serve\tests\TestCase;
use serve\utility\Str;

use function strlen;

/**
 * @group unit
 */
class StrUtilityTest extends TestCase
{
	 /**
	  *
	  */
	 public function testNl2br(): void
	{
	 	$this->assertEquals('Hello<br>World!', Str::nl2br("Hello\nWorld!"));
	 	$this->assertEquals('Hello<br>World!', Str::nl2br("Hello\rWorld!"));
	 	$this->assertEquals('Hello<br>World!', Str::nl2br("Hello\n\rWorld!"));
	 	$this->assertEquals('Hello<br>World!', Str::nl2br("Hello\r\nWorld!"));
	 	$this->assertEquals('Hello<br/>World!', Str::nl2br("Hello\nWorld!", true));
	 	$this->assertEquals('Hello<br/>World!', Str::nl2br("Hello\rWorld!", true));
	 	$this->assertEquals('Hello<br/>World!', Str::nl2br("Hello\n\rWorld!", true));
	 	$this->assertEquals('Hello<br/>World!', Str::nl2br("Hello\r\nWorld!", true));
	}

	/**
	 *
	 */
	public function testBr2nl(): void
	{
	 	$this->assertEquals("Hello\nWorld!", Str::br2nl('Hello<br>World!'));
	 	$this->assertEquals("Hello\nWorld!", Str::br2nl('Hello<br/>World!'));
	 	$this->assertEquals("Hello\nWorld!", Str::br2nl('Hello<br />World!'));
	}

	/**
	 *
	 */
	public function testCamel2underscored(): void
	{
	 	$this->assertEquals('camel_cased', Str::camel2underscored('CamelCased'));

	 	$this->assertEquals('camel_cased', Str::camel2underscored('camelCased'));

	 	$this->assertEquals('camel_cased test_case', Str::camel2underscored('CamelCased TestCase'));

	 	$this->assertEquals('camel_cased_test_caseings_tt', Str::camel2underscored('CamelCasedTestCaseingsTT'));
	}

	/**
	 *
	 */
	public function testCamel2case(): void
	{
	 	$this->assertEquals('Camel Cased', Str::camel2case('CamelCased'));

	 	$this->assertEquals('Camel Cased Text', Str::camel2case('CamelCased Text'));
	}

	/**
	 *
	 */
	public function testUnderscored2camel(): void
	{
	 	$this->assertEquals('camelCased', Str::underscored2camel('camel_cased'));

	 	$this->assertEquals('CamelCased', Str::underscored2camel('Camel_cased'));

	 	$this->assertEquals('CamelCasedText', Str::underscored2camel('Camel_cased_text'));
	}

	/**
	 *
	 */
	public function testReduce(): void
	{
	 	$this->assertEquals('This', Str::reduce('This is some text', 4));

	 	$this->assertEquals('This...', Str::reduce('This is some text', 4, '...'));

	 	$this->assertEquals('This is some text', Str::reduce('This is some text', 17, '...'));

	 	$this->assertEquals('This', Str::reduce('This is some text', 1, '', false));

	 	$this->assertEquals('This...', Str::reduce('This is some text', 1, '...', false));

	 	$this->assertEquals('This is some text', Str::reduce('This is some text', 4, '...', false));
	}

	/**
	 *
	 */
	public function testContains(): void
	{
		$this->assertEquals(true, Str::contains('This is some text', 'some'));

		$this->assertEquals(false, Str::contains('This is some text', 'thisss'));

		$this->assertEquals(true, Str::contains('This is some text', 's'));

		$this->assertEquals(false, Str::contains('This is some text', 'z'));
	}

	/**
	 *
	 */
	public function testGetAfterLastChar(): void
	{
		$this->assertEquals(' some text', Str::getAfterLastChar('This * is * some text', '*'));
	}

	/**
	 *
	 */
	public function testGetBeforeLastChar(): void
	{
		$this->assertEquals('This * is ', Str::getBeforeLastChar('This * is * some text', '*'));
	}

	/**
	 *
	 */
	public function testGetAfterFirstChar(): void
	{
		$this->assertEquals(' is * some text', Str::getAfterFirstChar('This * is * some text', '*'));
	}

	/**
	 *
	 */
	public function testGetBeforeFirstChar(): void
	{
		$this->assertEquals('This ', Str::getBeforeFirstChar('This * is * some text', '*'));
	}

	/**
	 *
	 */
	public function testGetAfterLastWord(): void
	{
		$this->assertEquals(' test', Str::getAfterLastWord('This text is some text test', 'text'));
	}

	/**
	 *
	 */
	public function testGetBeforeLastWord(): void
	{
		$this->assertEquals('This text is some ', Str::getBeforeLastWord('This text is some text test', 'text'));
	}

	/**
	 *
	 */
	public function testRandom(): void
	{
		$this->assertEquals(20, strlen(Str::random(20)));

		$this->assertEquals(20, strlen(Str::random(20, Str::NUMERIC)));

		$this->assertEquals(20, strlen(Str::random(20, Str::ALPHA)));

		$this->assertEquals(20, strlen(Str::random(20, Str::HEXDEC)));

		$this->assertEquals(20, strlen(Str::random(20, Str::SYMBOLS)));
	}

	/**
	 *
	 */
	public function testCompareNumeric(): void
	{
		$this->assertTrue(Str::compareNumeric('1.3', '1.3'));

		$this->assertTrue(Str::compareNumeric('0.04323', '0.04323'));

		$this->assertTrue(Str::compareNumeric('4323', '4323'));
	}

	/**
	 *
	 */
	public function testSlug(): void
	{
		$this->assertEquals('name-of-slug-test-123', Str::slug('name of slug test 123'));

		$this->assertEquals('foo-bar-4', Str::slug('foo $@#$# bar 4#@!#@!#'));

		$this->assertEquals('foo-bar-4', Str::slug('Foo $@#$# BAR 4#@!#@!#'));
	}

	/**
	 *
	 */
	public function testAlpha(): void
	{
		$this->assertEquals('OnlyAllowAlpha', Str::alpha('Only Allow $$#@$# Alpha 123'));
	}

	/**
	 *
	 */
	public function testAlphaDash(): void
	{
		$this->assertEquals('Only-Allow-Alpha', Str::alphaDash('Only Allow $$#@$# Alpha 123 $# $#$@#'));
	}

	/**
	 *
	 */
	public function testAlphaNum(): void
	{
		$this->assertEquals('OnlyAllowAlpha123Numbers', Str::alphaNum('Only Allow $$#@$# Alpha 123 $# $#$@# Numbers'));
	}

	/**
	 *
	 */
	public function testAlphaNumDash(): void
	{
		$this->assertEquals('Only-Allow-Alpha-123-Numbers', Str::alphaNumDash('Only Allow $$#@$# Alpha 123 $# $#$@# Numbers'));
	}

	/**
	 *
	 */
	public function testMysqlEncode(): void
	{
		$this->assertEquals('This has been&#039;s encoded', Str::mysqlEncode('This has been\'s encoded'));
	}

	/**
	 *
	 */
	public function testMysqlDecode(): void
	{
		$this->assertEquals('This has been\'s encoded', Str::mysqlDecode('This has been&#039;s encoded'));
	}

	/**
	 *
	 */
	public function testBool(): void
	{
		$this->assertTrue(Str::bool('true'));
		$this->assertTrue(Str::bool('yes'));
		$this->assertTrue(Str::bool('on'));
		$this->assertTrue(Str::bool('1'));

		$this->assertFalse(Str::bool('false'));
		$this->assertFalse(Str::bool('no'));
		$this->assertFalse(Str::bool('off'));
		$this->assertFalse(Str::bool('0'));
		$this->assertFalse(Str::bool('-1'));
		$this->assertFalse(Str::bool(''));
	}
}
