<?php

namespace NetglueMoney\Adapter\EuroFxRef;

use NetglueMoney\Adapter\AdapterOptions;
use NetglueMoney\Adapter\Exception;

class Options extends AdapterOptions {
	
	/**
	 * Base currency these rates are presented in
	 */
	const BASE_CURRENCY = 'EUR';
	
	/**
	 * The Base URI for making requests
	 * @var string
	 */
	protected $baseUri = 'www.ecb.europa.eu/stats/eurofxref';
	
	/**
	 * Scheme
	 * @var string
	 */
	protected $scheme = 'http';
	
	/**
	 * Whether we have historical rate capability
	 * @var bool
	 */
	protected $historicalCapability = true;
	
	/**
	 * Base Currency
	 * @var string
	 */
	protected $baseCurrency = self::BASE_CURRENCY;
	
	/**
	 * Set the base URI for api calls
	 * @param string $uri
	 * @return self
	 */
	public function setBaseUri($uri) {
		$this->baseUri = $uri;
		return $this;
	}
	
	/**
	 * Return Base Uri
	 * @return string
	 */
	public function getBaseUri() {
		return $this->baseUri;
	}
	
	/**
	 * Return Scheme
	 * @return string
	 */
	public function getScheme() {
		return $this->scheme;
	}
	
	/**
	 * Set Scheme
	 * @param string $scheme
	 * @return self
	 * @throws Exception\InvalidArgumentException if scheme is not supported
	 */
	public function setScheme($scheme) {
		$scheme = trim(strtolower($scheme));
		if(!preg_match('~^http[s]*$~', $scheme)) {
			throw new Exception\InvalidArgumentException("Scheme should be https or http. Received {$scheme}");
		}
		$this->scheme = $scheme;
		return $this;
	}
	
	/**
	 * Set the base currency
	 * @param string $code
	 * @return self
	 */
	public function setBaseCurrency($code) {
		$code = trim(strtoupper($code));
		if(!preg_match('/^[A-Z]{3}$/', $code)) {
			throw new Exception\InvalidArgumentException("Invalid currency code");
		}
		$this->baseCurrency = $code;
		return $this;
	}
	
	/**
	 * Return the base currency
	 * @return string
	 */
	public function getBaseCurrency() {
		return $this->baseCurrency;
	}
	
	/**
	 * Whether we have the capability to alter the base currency
	 * @return bool
	 */
	public function canChangeBaseCurrency() {
		return false;
	}
	
	/**
	 * Return the default base currency code
	 * @return string
	 */
	public function getDefaultBaseCurrency() {
		return self::BASE_CURRENCY;
	}
	
	/**
	 * Whether the options set dictate that we have hstorical capability
	 * @return bool
	 */
	public function hasHistoricalCapability() {
		return $this->historicalCapability;
	}
}
