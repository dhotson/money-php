<?php

require_once 'src/money.php';

use Money\Money;

$m = Money::usd(100);

print_r($m->format(array('disambiguate' => true)));