<?php

namespace NetglueMoney\Service;

use NetglueMoney\Adapter\AdapterInterface;
use NetglueMoney\Exception;
use NetglueMoney\Money;

use DateTime;

class CurrencyConverter {
	
	/**
	 * Options
	 * @var CurrencyConverterOptions|NULL
	 */
	protected $options;
	
	/**
	 * Currency Conversion Adapter
	 * @var AdapterInterface
	 */
	protected $adapter;
	
	/**
	 * Constructor
	 * @param AdapterInterface $adapter
	 * @return void
	 */
	public function __construct(AdapterInterface $adapter) {
		$this->adapter = $adapter;
	}
	
	/**
	 * Return Adapter
	 * @return AdapterInterface
	 */
	public function getAdapter() {
		return $this->adapter;
	}
	
	/**
	 * Convert a currency amount
	 * @param string|float $amount
	 * @param string $from ISO Currency Code
	 * @param string $to ISO Currency Code
	 * @param int|DateTime $time A Unix timestamp or a DateTime instance
	 * @return string Converted Value as a float cast to a string
	 */
	public function convert($amount, $from, $to, $time = NULL) {
		if($time instanceof DateTime) {
			$time = $time->getTimestamp();
		}
		$rate = $this->adapter->getRate($from, $to, $time);
		return $this->conversionResult($amount, $rate);
	}
	
	/**
	 * Convert a Money instance
	 * @param Money $money
	 * @param string $to ISO Currency Code
	 * @return Money Returns a new instance leaving the parameter $money alone
	 */
	public function convertMoney(Money $money, $to) {
		$amount = $this->convert($money->getAmount(), $money->getCurrencyCode(), $to, $money->getDateTime());
		return new Money($amount, $to, clone($money->getDateTime()));
	}
	
	/**
	 * Multiply the amount and destination rate using configured precision and rounding rules
	 * @param string $amount
	 * @param float $rate
	 * @return string (float cast to string)
	 */
	protected function conversionResult($amount, $rate) {
		$value = bcmul($amount, $rate, $this->options->getBcScale());
		if($this->options->getRound()) {
			$value = round($value, $this->options->getPrecision(), $this->options->getRoundMode());
		}
		return (string) $value;
	}
	
	/**
	 * Set Options
	 * @param array|Traversable|CurrencyConverterOptions $options
	 * @return self
	 */
	public function setOptions($options) {
		if(is_array($options) || $options instanceof \Traversable) {
			$options = new CurrencyConverterOptions($options);
		}
		if(!$options instanceof CurrencyConverterOptions) {
			throw new Exception\InvalidArgumentException("Options should be an array, an instanceof Traversable or CurrencyConverterOptions");
		}
		$this->options = $options;
		return $this;
	}
	
	/**
	 * Get Options
	 * @return CurrencyConverterOptions
	 */
	public function getOptions() {
		if(!$this->options) {
			$this->setOptions(new CurrencyConverterOptions);
		}
		return $this->options;
	}
	
}