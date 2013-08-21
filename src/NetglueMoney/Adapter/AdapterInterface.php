<?php

namespace NetglueMoney\Adapter;

interface AdapterInterface {
	
	/**
	 * Set adapter options
	 * @param AdapterOptionsInterface $options
	 * @return AdapterInterface self
	 */
	public function setOptions(AdapterOptionsInterface $options);
	
	/**
	 * Return configured options object
	 * @return AdapterOptionsInterface|NULL
	 */
	public function getOptions();
	
	/**
	 * Return an array of supported currencies.
	 * The array should contain only 3 letter ISO 4217 Currency codes
	 * @return array
	 */
	public function getSupportedCurrencyCodes();
	
	/**
	 * Return the rate between the two currency codes
	 * @param string $from
	 * @param string $to
	 * @param int Unix Timestamp The time the rate is relevant. Leave as NULL for the most recent rate
	 * @return string|float Return a string to preserve float accuracy if required
	 * @throws Exception\ExceptionInterface if it's not possible to find a rate or any of the currencies requested are not supported
	 * 
	 */
	public function getRate($from, $to, $time = NULL);
	
	
}
