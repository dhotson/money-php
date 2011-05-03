<?php

require_once(BASEDIR.'/src/money.php');

use money\Money;

class MoneyTest extends UnitTestCase
{
	public function testBasic()
	{
		$m = Money::usd(100); // $1.00
		$this->assertEqual($m->cents(), 100);
		$this->assertEqual($m->currency()->id, 'usd');
	}

	public function testSplit()
	{
		$m = Money::usd(100); // $1.00
		$r = $m->split(3);
		$this->assertEqual(34, $r[0]->cents());
		$this->assertEqual(33, $r[1]->cents());
		$this->assertEqual(33, $r[2]->cents());
	}

	public function testToString()
	{
		$m = Money::usd(123456);// $1234.56
		$this->assertEqual("$m", '1234.56');
	}

	public function testFormat()
	{
		$m1 = Money::usd(123456); // $1234.56
		$m2 = Money::usd(123400); // $1234.00

		$this->assertEqual('$1,234.56', $m1->format());
		$this->assertEqual('$1,234~56', $m1->format(array('decimal_mark' => '~')));
		$this->assertEqual('$1_234.56', $m1->format(array('thousands_separator' => '_')));
		$this->assertEqual('$1,234.56 USD', $m1->format(array('with_currency' => true)));
		$this->assertEqual('$1,234.56 <span class="currency">USD</span>', $m1->format(array('with_currency' => true, 'html' => true)));
		$this->assertEqual('$1,234', $m1->format(array('no_cents'=>true)));

		$this->assertEqual('$1,234.56', $m1->format(array('no_cents_if_whole'=>true)));
		$this->assertEqual('$1,234', $m2->format(array('no_cents_if_whole'=>true)));

		$this->assertEqual('1,234.56 $', $m1->format(array('symbol_position' => 'after')));
	}

}
