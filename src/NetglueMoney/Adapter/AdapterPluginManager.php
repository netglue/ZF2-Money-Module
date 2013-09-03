<?php

namespace NetglueMoney\Adapter;

use Zend\ServiceManager\AbstractPluginManager;

class AdapterPluginManager extends AbstractPluginManager {
	
	/**
	 * Default set of adapters
	 *
	 * @var array
	 */
	protected $invokableClasses = array(
		'openexchangerates' => 'NetglueMoney\Adapter\OpenExchange\Adapter',
		'eurofxref' => 'NetglueMoney\Adapter\EuroFxRef\Adapter',
	);
	
	protected $shareByDefault = true;
	
	/**
	 * Make sure the plugin is an instanceof Adapter\AdapterInterface
	 * @return void
	 * @throws Adapter\Exception\RuntimeException if the plugin is invalid
	 */
	public function validatePlugin($plugin) {
		if($plugin instanceof AdapterInterface) {
			return;
		}
		throw new Exception\RuntimeException("Adapter should be an instance of ".__NAMESPACE__."\AdapterInterface");
	}
}
