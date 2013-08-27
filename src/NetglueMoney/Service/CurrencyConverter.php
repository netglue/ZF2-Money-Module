<?php

namespace NetglueMoney\Service;

use NetglueMoney\Adapter\AdapterInterface;

class CurrencyConverter {
	
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
	
}