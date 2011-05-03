<?php

require_once(BASEDIR.'/src/money.php');

use money\Currency;

class CurrencyTest extends UnitTestCase
{
	public function testBasic()
	{
		$c = new Currency('usd');
	}
}
