<?php


namespace NetglueMoney\Adapter\EuroFxRef;

use NetglueMoney\Adapter\AbstractAdapter;

use NetglueMoney\Adapter\Exception;

use DateTime;

#use Zend\Http\Client;
#use Zend\Http\Request;
#use Zend\Http\Response;
#use Zend\Http\Exception\ExceptionInterface as HttpException;

use Zend\Cache\Exception\ExceptionInterface as CacheException;

class Adapter extends AbstractAdapter {
	
	/**
	 * Endpoints relative $this->options->getBaseUri() where we can get rates
	 * @var array
	 */
	protected static $paths = array(
		'daily' => '/eurofxref-daily.xml', // Today, Most Recent - Updates 3pm CET
		'90day' => '/eurofxref-hist-90d.xml', // Last 90 Days
		'history' => '/eurofxref-hist.xml', // Since 1999 ish
	);
	
	/**
	 * Most recent rates
	 * @var array|NULL
	 */
	protected $recent;
	
	/**
	 * 90 Day History
	 * @var array|NULL
	 */
	protected $history90;
	
	/**
	 * All Historical Data
	 * @var array|NULL
	 */
	protected $historical;
	
	/**
	 * Return the rate between the two currency codes
	 * @param string $from
	 * @param string $to
	 * @param int Unix Timestamp The time the rate is relevant. Leave as NULL for the most recent rate
	 * @return string|float Return a string to preserve float accuracy if required
	 * @throws Exception\ExceptionInterface if it's not possible to find a rate or any of the currencies requested are not supported
	 * 
	 */
	public function getRate($from, $to, $time = NULL) {
		$this->checkCodePair($from, $to);
		
	}
	
	/**
	 * Find all rates on the given day
	 * @param string $date
	 * @return array|false
	 */
	protected function getRatesOnDate($date) {
		$tz = new \DateTimeZone('UTC');
		$date = new DateTime($date, $tz);
		$now = new DateTime(NULL, $tz);
		
	}
	
	/**
	 * Return an array of supported currencies.
	 * The array should contain only 3 letter ISO 4217 Currency codes
	 * @return array
	 */
	public function getSupportedCurrencyCodes() {
		$out = array_keys($this->getDailyRates());
		$out[] = $this->getBaseCurrency();
		return $out;
	}
	
	/**
	 * Return the most recent rates as an array
	 * @return array
	 */
	public function getDailyRates() {
		if(is_array($this->recent)) {
			return $this->recent;
		}
		$key = 'euroFxRefLatest'.$this->getBaseCurrency();
		if($this->hasCache() && $this->getCache()->hasItem($key)) {
			try {
				$c = $this->getCache();
				$data = $c->getItem($key, $success, $casToken);
				if(NULL !== $data) {
					$latest = unserialize($data);
					$this->recent = $latest;
					return $latest;
				}
			} catch(CacheException $e) {
				
			}
		}
		$this->recent = $this->loadRatesXml($this->getBaseUri().static::$paths['daily']);
		if($this->hasCache()) {
			$this->getCache()->setItem($key, serialize($this->recent));
		}
		return $this->recent;
	}
	
	/**
	 * Return the data from the 90 day history file, possibly includes today's also
	 * @return array
	 */
	public function get90DayRates() {
		if(is_array($this->history90)) {
			return $this->history90;
		}
		$key = 'euroFxRef90DayHistory'.$this->getBaseCurrency();
		if($this->hasCache() && $this->getCache()->hasItem($key)) {
			try {
				$c = $this->getCache();
				$data = $c->getItem($key, $success, $casToken);
				if(NULL !== $data) {
					$latest = unserialize($data);
					$this->history90 = $latest;
					return $latest;
				}
			} catch(CacheException $e) {
				
			}
		}
		$this->history90 = $this->loadRatesXml($this->getBaseUri().static::$paths['90day']);
		if($this->hasCache()) {
			$this->getCache()->setItem($key, serialize($this->history90));
		}
		return $this->history90;
	}
	
	/**
	 * Return the data for the large history file
	 * @return array
	 */
	public function getHistoricalRates() {
		if(is_array($this->historical)) {
			return $this->historical;
		}
		$key = 'euroFxRefHistorical'.$this->getBaseCurrency();
		if($this->hasCache() && $this->getCache()->hasItem($key)) {
			try {
				$c = $this->getCache();
				$data = $c->getItem($key, $success, $casToken);
				if(NULL !== $data) {
					$latest = unserialize($data);
					$this->historical = $latest;
					return $latest;
				}
			} catch(CacheException $e) {
				
			}
		}
		$this->historical = $this->loadRatesXml($this->getBaseUri().static::$paths['history']);
		if($this->hasCache()) {
			$this->getCache()->setItem($key, serialize($this->historical));
		}
		return $this->historical;
	}
	
	/**
	 * Utility method loads remote XML file and returns an array containg rate information
	 * @param string $file
	 * @return array
	 */
	protected function loadRatesXml($file) {
		$reader = new \XMLReader;
		if(!$reader->open($file)) {
			throw new Exception\RuntimeException("Failed to load the XML file located at {$file}");
		}
		
		$rates = array();
		$base = $this->getBaseCurrency();
		// Dates for the ECB Rates are all in UTC
		$timezone = new \DateTimeZone('UTC');
		while($reader->read()) {
			if($reader->nodeType === \XmlReader::ELEMENT) {
				if($reader->name === 'Cube' && $reader->getAttribute('time')) {
					$element = array();
					$date = $reader->getAttribute('time');
					$element['date'] = $date;
					$date = new DateTime($date, $timezone);
					$element['timestamp'] = $date->getTimestamp();
					$element['base'] = $base;
					$element['rates'] = array(
						$base => 1.0,
					);
					$domElement = $reader->expand();
					foreach($domElement->getElementsByTagName('Cube') as $node) {
						$element['rates'][$node->getAttribute('currency')] = $node->getAttribute('rate');
					}
					$rates[] = $element;
				}
			}
		}
		return $rates;
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
	 * Whether the adapter supports historical exchange rate lookups
	 * By default this method returns false to save having to implement it in every adapter
	 * @return bool
	 */
	public function supportsHistoricalRates() {
		return $this->getOptions()->hasHistoricalCapability();
	}
	
	public function setOptions($options) {
		if(!$options instanceof Options) {
			$options = new Options($options);
		}
		return parent::setOptions($options);
	}
	
	/**
	 * Return the base currency checking that we are allowed to change from the default
	 * @return string
	 */
	public function getBaseCurrency() {
		$code = $this->getOptions()->getBaseCurrency();
		if( $code !== $this->getOptions()->getDefaultBaseCurrency() && false === $this->getOptions()->canChangeBaseCurrency() ) {
			throw new OptionsException("It is not possible to alter the base currency");
		}
		return $code;
	}
}