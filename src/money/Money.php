<?php

namespace money;

class Money
{
	private $_cents;
	private $_currency;

	public static $defaultCurrency;

	public function __construct($cents, $currency=null)
	{
		$this->_cents = round($cents);
		$this->_currency = isset($currency)
			? Currency::wrap($currency)
			: Money::$defaultCurrency;
	}

	public static function usd($cents)
	{
		return new self($cents, 'usd');
	}

	public static function aud($cents)
	{
		return new self($cents, 'aud');
	}

	public function cents()
	{
		return $this->_cents;
	}

	public function currency()
	{
		return $this->_currency;
	}

	/**
	 * Split money amongst parties evenly without loosing pennies.
	 *
	 * @param $num number of parties.
	 *
	 * @return [Array<Money, Money, Money>]
	 *
	 * @example
	 *   Money.new(100, "USD").split(3) #=> [Money.new(34), Money.new(33), Money.new(33)]
	 */
	public function split($num)
	{
		if ($num === 0)
			throw new Exception("need at least one party");

		$low = new self($this->_cents / $num);
		$high = new self($low->_cents + 1);

		$remainder = $this->_cents % $num;
		$result = array();

		for ($i=0; $i<$num; $i++)
			$result[$i] = $i < $remainder ? $high : $low;

		return $result;
	}

	public function symbol()
	{
		return isset($this->_currency->symbol)
			? $this->_currency->symbol
			: "¤";
	}

	public function decimalMark()
	{
		return isset($this->_currency->decimalMark)
			? $this->_currency->decimalMark
			: ".";
	}

	public function thousandsSeparator()
	{
		return isset($this->_currency->thousandsSeparator)
			? $this->_currency->thousandsSeparator
			: ",";
	}

	public function format($rules=array())
	{

		if ($this->_cents === 0)
		{
			if (is_string($rules['display_free']))
				return $rules['display_free'];
			elseif (isset($rules['display_free']) && $rules['display_free'])
				return "free";
		}

		if (isset($rules['symbol']))
		{
			if ($rules['symbol'] === true)
				$symbolValue = $this->symbol();
			elseif ($rules['symbol'])
				$symbolValue = $rules['symbol'];
			else
				$symbolValue = "";
		}
		elseif (isset($rules['html']))
			$symbolValue = $this->_currency->htmlEntity;
		else
			$symbolValue = $this->symbol();

		if (isset($rules['no_cents']) && $rules['no_cents'] === true)
			$formatted = (string)floor($this->__toString());
		else
			$formatted = $this->__toString();

		if (isset($rules['no_cents_if_whole']) && $this->_cents % $this->_currency->subunitToUnit == 0)
		{
			$formatted = (string)floor($this->__toString());
		}

		if (isset($rules['symbol_position']))
			$symbolPosition = $rules['symbol_position'];
		elseif ($this->_currency->symbolFirst)
			$symbolPosition = 'before';
		else
			$symbolPosition = 'after';

		if (isset($symbolValue) && !empty($symbolValue))
		{
			$formatted = $symbolPosition === 'before'
				? "$symbolValue$formatted"
				: "$formatted $symbolValue";
		}

		if (isset($rules['decimal_mark']) && $rules['decimal_mark'] && $rules['decimal_mark'] !== $this->decimalMark())
		{
			$formatted = str_replace($this->decimalMark(), $rules['decimal_mark'], $formatted, $tmp = 1 /* Needs to be pass by ref */);
		}

		$thousandsSeparatorValue = $this->thousandsSeparator();
		if (isset($rules['thousands_separator']))
		{
			if ($rules['thousands_separator'] === false || $rules['thousands_separator'] === null)
				$thousandsSeparatorValue = '';
			elseif ($rules['thousands_separator'])
				$thousandsSeparatorValue = $rules['thousands_separator'];
		}

		$formatted = preg_replace('/(\d)(?=(?:\d{3})+(?:[^\d]|$))/', '\1'.$thousandsSeparatorValue, $formatted);

		if (isset($rules['with_currency']) && $rules['with_currency'])
		{
			$formatted .= ' ';
			if (isset($rules['html']) && $rules['html'])
				$formatted .= '<span class="currency">';
			$formatted .= $this->_currency->__toString();
			if (isset($rules['html']) && $rules['html'])
				$formatted .= '</span>';
		}

		return $formatted;
	}

	public function __toString()
	{
		$unit  = (string)floor(abs($this->_cents) / $this->_currency->subunitToUnit);
		$subunit  = (string)floor(abs($this->_cents) % $this->_currency->subunitToUnit);
		if ($this->_currency->decimalPlaces() == 0)
		{
			if ($this->_cents < 0)
				return "-$unit";
			else
				return $unit;
		}

		$subunit = str_repeat("0", $this->_currency->decimalPlaces()) . $subunit;
		$subunit = substr($subunit, -1 * $this->_currency->decimalPlaces());

		if ($this->_cents < 0)
			return '-' . $unit . $this->decimalMark() . $subunit;
		else
			return $unit . $this->decimalMark() . $subunit;
	}
}

Money::$defaultCurrency = new Currency('USD');
