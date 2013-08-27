<?php

namespace NetglueMoney\Adapter;

use Zend\Cache\Storage\StorageInterface;

abstract class AbstractAdapter implements AdapterInterface {
	
	/**
	 * Adapter Options
	 * @var AdapterOptionsInterface
	 */
	protected $options;
	
	/**
	 * Cache
	 * @var StorageInterface|NULL
	 */
	protected $cache;
	
	/**
	 * Set adapter options
	 * @param AdapterOptionsInterface $options
	 * @return AdapterInterface self
	 */
	public function setOptions(AdapterOptionsInterface $options) {
		$this->options = $options;
		return $this;
	}
	
	/**
	 * Return configured options object
	 * @return AdapterOptionsInterface|NULL
	 */
	public function getOptions() {
		return $this->options;
	}
	
	/**
	 * Whether the adapter supports historical exchange rate lookups
	 * By default this method returns false to save having to implement it in every adapter
	 * @return bool
	 */
	public function supportsHistoricalRates() {
		return false;
	}
	
	/**
	 * Set Cache Storage Adapter
	 * @param StorageInterface $cache
	 * @return self
	 */
	public function setCache(StorageInterface $cache) {
		$this->cache = $cache;
		return $this;
	}
	
	/**
	 * Return cache storage
	 * @return StorageInterface|NULL
	 */
	public function getCache() {
		return $this->cache;
	}
	
	/**
	 * Whether we have a cache adpater available or not
	 * @return bool
	 */
	public function hasCache() {
		return NULL !== $this->cache;
	}
	
	/**
	 * Whether the given code is supported
	 * @param string $code
	 * @return bool
	 */
	public function isSupportedCurrency($code) {
		$code = trim(strtoupper($code));
		return in_array($code, $this->getSupportedCurrencyCodes());
	}
	
	
}