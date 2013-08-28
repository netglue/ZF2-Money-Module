<?php

namespace NetglueMoney;


use DateTime;

class Money {
	
	/**
	 * ISO 4217 Currency Code
	 * @var string|NULL
	 */
	protected $currencyCode;
	
	/**
	 * Monetary amount
	 * 
	 * Currency values are stored as strings and computed using bcmath to avoid rounding errors
	 *
	 * @var string|NULL
	 */
	protected $amount;
	
	/**
	 * DateTime - important for currency conversions
	 * @var DateTime|NULL
	 */
	protected $dateTime;
	
	/**
	 * Constructor
	 * @param string|int|float $amount
	 * @param string $code ISO 4217 Currency Code
	 * @param DateTime|int|string $date DateTime, Unix timestamp, something that will create a successfull DateTime instance
	 * @return void
	 */
	public function __construct($amount = NULL, $code = NULL, $date = NULL) {
		if(NULL !== $amount) {
			$this->setAmount($amount);
		}
		if(NULL !== $code) {
			$this->setCurrencyCode($code);
		}
		if(NULL !== $date) {
			$this->setDateTime($date);
		}
	}
	
	/**
	 * Set ISO 4217 Currency Code
	 * @param string $code
	 * @throws Exception\InvalidArgumentException if the currency code is not a three letter string
	 * @return self
	 */
	public function setCurrencyCode($code) {
		$code = trim(strtoupper($code));
		if(!preg_match('/^[A-Z]{3}$/', $code)) {
			throw new Exception\InvalidArgumentException("{$code} is not a valid ISO 4217 Currency Code");
		}
		$this->currencyCode = $code;
		return $this;
	}
	
	/**
	 * Return current ISO 4217 Currency Code
	 * @return string|NULL
	 */
	public function getCurrencyCode() {
		return $this->currencyCode;
	}
	
	/**
	 * Set currency amount
	 * @param string|float|int $amount
	 * @throws Exception\InvalidArgumentException if the amount is not numeric
	 * @return self
	 */
	public function setAmount($amount) {
		if(!is_numeric($amount)) {
			$value = is_scalar($amount) ? $amount : gettype($amount);
			throw new Exception\InvalidArgumentException("Currency amount should be number, received {$value}");
		}
		$this->amount = (string) $amount;
		return $this;
	}
	
	/**
	 * Return amount
	 * @return string|NULL
	 */
	public function getAmount() {
		return $this->amount;
	}
	
	/**
	 * Set the date/time attribute
	 * @param DateTime|int $dateTime DateTime or Unix timestamp
	 * @return self
	 */
	public function setDateTime($dateTime) {
		if(is_numeric($dateTime)) {
			$date = new DateTime;
			$date->setTimestamp($dateTime);
			$dateTime = $date;
		}
		if(!$dateTime instanceof DateTime) {
			try {
				$dateTime = new DateTime($dateTime);
			} catch(\Exception $e) {
				throw new Exception\InvalidArgumentException("Invalid date/time value", NULL, $e);
			}
		}
		$this->dateTime = $dateTime;
		return $this;
	}
	
	/**
	 * Return date/time
	 * @return DateTime
	 */
	public function getDateTime() {
		if(NULL === $this->dateTime) {
			$this->setDateTime(time());
		}
		return $this->dateTime;
	}
	
	/**
	 * Set reference time for the currency object with a unix timestamp
	 * @param int $timestamp
	 * @return self
	 */
	public function setTimestamp($timestamp) {
		if(!is_numeric($timestamp)) {
			$value = is_scalar($timestamp) ? $timestamp : gettype($timestamp);
			throw new Exception\InvalidArgumentException("Unix timestamp should be number, received {$value}");
		}
		$dt = new DateTime;
		if(false === $dt->setTimestamp($timestamp)) {
			throw new Exception\InvalidArgumentException("Invalid Unix timestamp provided");
		}
		$this->dateTime = $dt;
		return $this;
	}
	
	/**
	 * Return the reference timestamp for this currency amount
	 * @return int|NULL
	 */
	public function getTimestamp() {
		if(!$this->dateTime instanceof DateTime) {
			return NULL;
		}
		return $this->dateTime->getTimestamp();
	}
	
	
}