<?php

namespace NetglueMoney\Adapter\OpenExchange;

use NetglueMoney\Adapter\AbstractOptions;
use NetglueMoney\Adapter\Exception;

class Options extends AbstractOptions {
	
	const ACCOUNT_TYPE_FREE = 'free';
	
	const ACCOUNT_TYPE_DEVELOPER = 'developer';
	
	const ACCOUNT_TYPE_UNLIMITED = 'unlimited';
	
	const ACCOUNT_TYPE_ENTERPRISE = 'enterprise';
	
	const BASE_CURRENCY = 'USD';
	
	/**
	 * App ID for accessing the API
	 * @var string
	 */
	protected $appId;
	
	/**
	 * Account Type
	 * @var string
	 */
	protected $accountType = self::ACCOUNT_TYPE_FREE;
	
	/**
	 * The Base URI for making requests
	 * @var string
	 */
	protected $baseUri = 'openexchangerates.org/api';
	
	/**
	 * Scheme
	 * @var string
	 */
	protected $scheme = 'http';
	
	/**
	 * Whether we have historical rate capability
	 * @var bool
	 */
	protected $historicalCapability = false;
	
	/**
	 * Base Currency
	 * @var string
	 */
	protected $baseCurrency = self::BASE_CURRENCY;
	
	/**
	 * Set App ID
	 * @param string $id
	 * @return self
	 */
	public function setAppId($id) {
		$this->appId = $id;
		return $this;
	}
	
	/**
	 * Return App ID
	 * @return string|NULL
	 */
	public function getAppId() {
		return $this->appId;
	}
	
	/**
	 * Set the base URI for api calls
	 * @param string $uri
	 * @return self
	 */
	public function setBaseUri($uri) {
		
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
		return ( ($this->accountType === self::ACCOUNT_TYPE_ENTERPRISE) || ($this->accountType === self::ACCOUNT_TYPE_UNLIMITED) );
	}
	
	/**
	 * Return the default base currency code
	 * @return string
	 */
	public function getDefaultBaseCurrency() {
		return self::BASE_CURRENCY;
	}
	
	/**
	 * Set Account Type
	 * @param string $type
	 * @return self
	 */
	public function setAccountType($type) {
		$type = trim(strtolower($type));
		if(!in_array($type, $types)) {
			throw new Exception\InvalidArgumentException("Unknown account type {$type}");
		}
		$this->accountType = $type;
		
		if($type !== self::ACCOUNT_TYPE_FREE) {
			$this->scheme = 'https';
		}
		
		switch($type) {
			case self::ACCOUNT_TYPE_FREE:
				
				break;
				
			case self::ACCOUNT_TYPE_DEVELOPER:
				
				break;
				
			case self::ACCOUNT_TYPE_ENTERPRISE:
				$this->historicalCapability = true;
				break;
				
			case self::ACCOUNT_TYPE_UNLIMITED:
				$this->historicalCapability = true;
				break;
			
		}
		return $this;
	}
	
	/**
	 * Return account type
	 * @return string
	 */
	public function getAccountType() {
		return $this->accountType;
	}
	
	/**
	 * Whether the options set dictate that we have hstorical capability
	 * @return bool
	 */
	public function hasHistoricalCapability() {
		return $this->historicalCapability;
	}
	
}