<?php
/**
 * Netglue Money Module
 * @author George Steel <george@net-glue.co.uk>
 * @copyright Copyright (c) 2013 Net Glue Ltd (http://netglue.co)
 * @license http://opensource.org/licenses/MIT
 * @link https://bitbucket.org/netglue/zf2-skeleton
 */

namespace NetglueMoney;

/**
 * Config Provider
 */
use Zend\ModuleManager\Feature\ConfigProviderInterface;

/**
 * Autoloader
 */
use Zend\Loader\AutoloaderFactory;
use Zend\Loader\StandardAutoloader;
use Zend\ModuleManager\Feature\AutoloaderProviderInterface;

/**
 * Controller Provider
 */
use Zend\ModuleManager\Feature\ControllerProviderInterface;

/**
 * Service Provider
 */
use Zend\ModuleManager\Feature\ServiceProviderInterface;

/**
 * Netglue Money Module
 * @author George Steel <george@net-glue.co.uk>
 * @copyright Copyright (c) 2013 Net Glue Ltd (http://netglue.co)
 * @license http://opensource.org/licenses/MIT
 * @link https://bitbucket.org/netglue/zf2-skeleton
 */
class Module implements 
	AutoloaderProviderInterface,
	ControllerProviderInterface,
	ServiceProviderInterface,
	ConfigProviderInterface
{
	
	/**
	 * Return Controller Config
	 * @return array
	 */
	public function getControllerConfig() {
		return array(
			'invokables' => array(
				
			),
		);
	}
	
	/**
	 * Return Service Config
	 * @return array
	 * @implements ServiceProviderInterface
	 */
	public function getServiceConfig() {
		return array();
	}
	
	/**
	 * Include/Return module configuration
	 * @return array
	 * @implements ConfigProviderInterface
	 */
	public function getConfig() {
		return include __DIR__ . '/../../config/module.config.php';
	}
	
	/**
	 * Return autoloader configuration
	 * @link http://framework.zend.com/manual/2.0/en/user-guide/modules.html
	 * @return array
	 */
	public function getAutoloaderConfig() {
    return array(
			AutoloaderFactory::STANDARD_AUTOLOADER => array(
				StandardAutoloader::LOAD_NS => array(
					__NAMESPACE__ => __DIR__,
				),
			),
		);
	}
	
}
