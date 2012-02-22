<?php

require_once(__DIR__.'/../src/money.php');

use Money\Money;

class MoneyTest extends PHPUnit_Framework_TestCase
{
	public function testBasic()
	{
		$m = Money::usd(100); // $1.00
		$this->assertEquals($m->cents(), 100);
		$this->assertEquals($m->currency()->id, 'usd');
	}

	public function testSplit()
	{
		$m = Money::usd(100); // $1.00
		$r = $m->split(3);
		$this->assertEquals(34, $r[0]->cents());
		$this->assertEquals(33, $r[1]->cents());
		$this->assertEquals(33, $r[2]->cents());
	}

	public function testToString()
	{
		$m = Money::usd(123456);// $1234.56
		$this->assertEquals("$m", '1234.56');
	}

	public function testFormat()
	{
		$m1 = Money::usd(123456); // $1234.56
		$m2 = Money::usd(123400); // $1234.00

		$this->assertEquals('$1,234.56', $m1->format());
		$this->assertEquals('$1,234~56', $m1->format(array('decimal_mark' => '~')));
		$this->assertEquals('$1_234.56', $m1->format(array('thousands_separator' => '_')));
		$this->assertEquals('$1,234.56 USD', $m1->format(array('with_currency' => true)));
		$this->assertEquals('<span class="symbol">$</span><span class="amount">1,234.56</span><span class="currency">USD</span>', $m1->format(array('with_currency' => true, 'html' => true)));
		$this->assertEquals('$1,234', $m1->format(array('no_cents'=>true)));

		$this->assertEquals('$1,234.56', $m1->format(array('no_cents_if_whole'=>true)));
		$this->assertEquals('$1,234', $m2->format(array('no_cents_if_whole'=>true)));

		$this->assertEquals('1,234.56$', $m1->format(array('symbol_position' => 'after')));
	}

}
