<?php

require_once(__DIR__.'/../src/money.php');

use Money\Currency;

class CurrencyTest extends PHPUnit_Framework_TestCase
{
	public function testBasic()
	{
		$c = new Currency('usd');
	}
}
