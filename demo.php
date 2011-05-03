<?php

require_once 'src/money.php';

use money\Money;

$m = Money::usd(100);

var_dump($m);
