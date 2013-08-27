<?php
/**
 * A factory for setting up an instance of a currency converter
 */

namespace NetglueMoney\Service;

use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;
use NetglueMoney\Service\CurrencyConverter;
use NetglueMoney\Adapter\AdapterPluginManager;
use NetglueMoney\Adapter\AdapterInterface;

use Zend\ServiceManager\AbstractPluginManager;
use Zend\Cache\StorageFactory as CacheFactory;

class CurrencyConverterFactory implements FactoryInterface {
	
	protected static $adapterManager;
	
	/**
	 * Create service
	 *
	 * @param ServiceLocatorInterface $serviceLocator
	 * @return CurrencyConverter|false
	 */
	public function createService(ServiceLocatorInterface $serviceLocator) {
		$config = $serviceLocator->get('config');
		$config = isset($config['ng_money']) ? $config['ng_money'] : array();
		if(!isset($config['adapter']['name'])) {
			return false;
		}
		$options = isset($config['adapter']['options']) ? $config['adapter']['options'] : array();
		$adapter = static::adapterFactory($config['adapter']['name'], $options);
		
		// Create a cache adapter, or retrieve it from the service locator if set to some sort of boolean value
		$cacheName = isset($config['cache']['adapter']['name']) ? $config['cache']['adapter']['name'] : NULL;
		$cacheOpt = isset($config['cache']['adapter']['options']) ? $config['cache']['adapter']['options'] : array();
		if($cacheName) {
			$cache = CacheFactory::factory($config['cache']);
			$adapter->setCache($cache);
		} elseif(isset($config['cache']) && (bool) $config['cache']) {
			$cache = $serviceLocator->get('cache');
			$adapter->setCache($cache);
		}
		
		
		$converter = new CurrencyConverter($adapter);
		return $converter;
	}
	
	public static function setAdapterPluginManager(AbstractPluginManager $pluginManager) {
		static::$adapterManager = $pluginManager;
	}
	
	public static function getAdapterPluginManager() {
		if(NULL === static::$adapterManager) {
			static::$adapterManager = new AdapterPluginManager;
		}
		return static::$adapterManager;
	}
	
	/**
	 * Create an adapter using the AdapterPluginManager
	 * @param string|AdapterInterface $name
	 * @param array|Traversable|\NetglueMoney\Adapter\AdapterOptions $options
	 * @return AdapterInterface
	 */
	public static function adapterFactory($name, $options = array()) {
		if($name instanceof AdapterInterface) {
			$adapter = $name;
		} else {
			$adapter = static::getAdapterPluginManager()->get($name);
		}
		if($options) {
			$adapter->setOptions($options);
		}
		return $adapter;
	}
	
}
