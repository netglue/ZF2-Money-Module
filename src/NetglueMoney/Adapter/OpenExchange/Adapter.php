<?php

namespace NetglueMoney\Adapter\OpenExchange;

use NetglueMoney\Adapter\AbstractAdapter;

use NetglueMoney\Adapter\Exception;

use DateTime;

use Zend\Http\Client;
use Zend\Http\Request;
use Zend\Http\Response;
use Zend\Http\Exception\ExceptionInterface as HttpException;

use Zend\Cache\Exception\ExceptionInterface as CacheException;

class Adapter extends AbstractAdapter {
	
	/**
	 * HTTP Client
	 * @var Client
	 */
	protected $http;
	
	protected static $paths = array(
		'supportedCurrencies' => '/currencies.json',
		'latestRates' => '/latest.json',
	);
	
	/**
	 * Cached response for the latest rates
	 * @var StdClass|NULL
	 */
	protected $latestRates;
	
	/**
	 * Return supported currencies as an array
	 * @return array
	 */
	public function getSupportedCurrencyCodes() {
		$key = 'openExchangeSupportedCurrencies';
		if($this->hasCache() && $this->getCache()->hasItem($key)) {
			try {
				$c = $this->getCache();
				$data = $c->getItem($key, $success, $casToken);
				if(NULL !== $data) {
					return unserialize($data);
				}
			} catch(CacheException $e) {
				
			}
		}
		$response = $this->get(static::$paths['supportedCurrencies']);
		if(!$response->isSuccess()) {
			$data = json_decode($response->getBody());
			throw new Exception\ApiException($data->description, $data->status);
		}
		$data = json_decode($response->getBody(), true);
		$supported = array_keys($data);
		if($this->hasCache()) {
			$this->getCache()->setItem($key, serialize($supported));
		}
		return $supported;
	}
	
	/**
	 * Return the rate between the two currency codes
	 * @param string $from
	 * @param string $to
	 * @param int Unix Timestamp The time the rate is relevant. Leave as NULL for the most recent rate
	 * @return string|float Return a string to preserve float accuracy if required
	 * @throws Exception\ExceptionInterface if it's not possible to find a rate or any of the currencies requested are not supported
	 */
	public function getRate($from, $to, $time = NULL) {
		/**
		 * Make sure we can handle the provided codes
		 */
		$this->checkCodePair($from, $to);
		
		if($from === $to) {
			return (string) 1;
		}
		/**
		 * Hand off to specific methods for current or historical rates
		 */
		if(NULL === $time) {
			$time = time();
		}
		$date = new DateTime;
		$date->setTimestamp($time);
		$now = new DateTime;
		if($date->format("Ymd") >= $now->format("Ymd")) {
			return $this->getCurrentRate($from, $to);
		} else {
			return $this->getHistoricalRate($from, $to, $time);
		}
	}
	
	/**
	 * Return a current rate between the two codes
	 * @param string $from
	 * @param string $to
	 * @return string|float Return a string to preserve float accuracy if required
	 * @throws Exception\ExceptionInterface if it's not possible to find a rate or any of the currencies requested are not supported
	 */
	public function getCurrentRate($from, $to) {
		$this->checkCodePair($from, $to);
		$this->getCurrentRates();
		$base = $this->getBaseCurrency();
		// Return Found Rate
		if($from === $base) {
			if(!isset($this->latestRates->rates->{$to})) {
				throw new Exception\RuntimeException("{$to} is not available as a current rate");
			}
			return (string) $this->latestRates->rates->{$to};
		}
		
		$scale = $this->getOptions()->getScale();
		
		// Compute Reverse Rate
		if($to === $base) {
			if(!isset($this->latestRates->rates->{$from})) {
				throw new Exception\RuntimeException("{$from} is not available as a current rate");
			}
			$rev = (string) $this->latestRates->rates->{$from};
			return bcdiv(1, $rev, $scale);
		}
		// Compute Cross Rate
		if(!isset($this->latestRates->rates->{$to})) {
			throw new Exception\RuntimeException("{$to} is not available as a current rate");
		}
		if(!isset($this->latestRates->rates->{$from})) {
			throw new Exception\RuntimeException("{$from} is not available as a current rate");
		}
		$baseFrom = $this->latestRates->rates->{$from};
		$baseTo = $this->latestRates->rates->{$to};
		$revFrom = bcdiv(1, $baseFrom, $scale);
		return bcmul($baseTo, $revFrom, $scale);
	}
	
	/**
	 * Helper method to throw exceptions if either pair of currencies are not among those supported
	 * @param string $from
	 * @param string $to
	 * @return void
	 * @throws Exception\CurrencyNotSupportedException
	 */
	protected function checkCodePair(& $from, & $to) {
		$from = trim(strtoupper($from));
		$to = trim(strtoupper($to));
		/**
		 * Make sure we can handle the provided codes
		 */
		if(!$this->isSupportedCurrency($from)) {
			throw new Exception\CurrencyNotSupportedException("{$from} is not a supported currency code");
		}
		if(!$this->isSupportedCurrency($to)) {
			throw new Exception\CurrencyNotSupportedException("{$to} is not a supported currency code");
		}
	}
	
	/**
	 * Utility Method to get a response from the given path and params
	 * @param string $path
	 * @param array $params
	 * @return Response
	 * @throws Exception\RuntimeException if there's an HttpException thrown by the HTTP Client
	 */
	protected function get($path, array $params = array()) {
		try {
			$client = $this->getHttpClient();
			$client->resetParameters();
			$client->setMethod(Request::METHOD_GET);
			$client->setUri($this->getBaseUri().$path);
			$params['app_id'] = $this->getOptions()->getAppId();
			$client->setParameterGet($params);
			return $client->send();
		} catch(HttpException $e) {
			throw new Exception\RuntimeException("Failed to get {$path}", NULL, $e);
		}
	}
	
	/**
	 * Return the current rates for the base currency configured
	 * The StdClass returned is the decoded response from the api, unaltered
	 * @link https://openexchangerates.org/documentation#preview-api-response
	 * @return StdClass
	 */
	public function getCurrentRates() {
		if(NULL !== $this->latestRates) {
			return $this->latestRates;
		}
		$key = 'openExchangeLatest'.$this->getBaseCurrency();
		if($this->hasCache() && $this->getCache()->hasItem($key)) {
			try {
				$c = $this->getCache();
				$data = $c->getItem($key, $success, $casToken);
				if(NULL !== $data) {
					$latest = unserialize($data);
					$this->latestRates = $latest;
					return $latest;
				}
			} catch(CacheException $e) {
				
			}
		}
		$params = array(
			'base' => $this->getBaseCurrency(),
		);
		$response = $this->get(static::$paths['latestRates'], $params);
		if(!strlen($response->getBody())) {
			throw new Exception\ApiException("No response returned from API. Have you set an App ID?");
		}
		if(!$response->isSuccess()) {
			$data = json_decode($response->getBody());
			throw new Exception\ApiException($data->description, $data->status);
		}
		$latest = json_decode($response->getBody());
		$this->latestRates = $latest;
		if($this->hasCache()) {
			$this->getCache()->setItem($key, serialize($latest));
		}
		return $latest;
	}
	
	/**
	 * Return an HTTP Client
	 * @return Client
	 */
	public function getHttpClient() {
		if( ! $this->http instanceof Client) {
			$this->setHttpClient(new Client);
		}
		return $this->http;
	}
	
	/**
	 * Set/Override Http Client
	 * @param Client $client
	 * @return self
	 */
	public function setHttpClient(Client $client) {
		/**
		 * We only want to speak in JSON thanks very much,
		 * And don't follow redirects.
		 * If you don't supply an app id at all, you get redirected to an HTML page
		 */
		$this->http = $client;
		$accept = new \Zend\Http\Header\Accept;
		$accept->addMediaType('application/json');
		$client->getRequest()->getHeaders()->addHeader($accept);
		$options = array(
			'maxredirects' => 0,
		);
		$client->setOptions($options);
		return $this;
	}
	
	/**
	 * Whether the adapter supports historical exchange rate lookups
	 * This is dependent on your account type for this adapter
	 * @return bool
	 */
	public function supportsHistoricalRates() {
		return $this->getOptions()->hasHistoricalCapability();
	}
	
	/**
	 * Return full Base URI including scheme for the api
	 * @return string
	 */
	public function getBaseUri() {
		return sprintf('%s://%s',
			$this->getOptions()->getScheme(),
			rtrim($this->getOptions()->getBaseUri(), '/'));
	}
	
	/**
	 * Return the base currency checking that we are allowed to change from the default
	 * @return string
	 */
	public function getBaseCurrency() {
		$code = $this->getOptions()->getBaseCurrency();
		if( $code !== $this->getOptions()->getDefaultBaseCurrency() && false === $this->getOptions()->canChangeBaseCurrency() ) {
			throw new OptionsException("Your account type does not allow alteration of the base currency");
		}
		return $code;
	}
	
	public function setOptions($options) {
		if(!$options instanceof Options) {
			$options = new Options($options);
		}
		return parent::setOptions($options);
	}
	
}