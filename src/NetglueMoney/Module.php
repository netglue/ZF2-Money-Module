<?php

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
 * Service Provider
 */
use Zend\ModuleManager\Feature\ServiceProviderInterface;

/**
 * Form Element Provider
 */
use Zend\ModuleManager\Feature\FormElementProviderInterface;

/**
 * Validator Provider
 */
use Zend\ModuleManager\Feature\ValidatorProviderInterface;

class Module implements
	AutoloaderProviderInterface,
	ServiceProviderInterface,
	FormElementProviderInterface,
	ConfigProviderInterface,
	ValidatorProviderInterface
{

	/**
	 * Return Service Config
	 * @return array
	 * @implements ServiceProviderInterface
	 */
	public function getServiceConfig()
	{
		return array(
		    'factories' => array(
                'CurrencyConverter' => 'NetglueMoney\Service\CurrencyConverterFactory',
			    'NetglueMoney\Service\CurrencyList' => 'NetglueMoney\Factory\CurrencyListFactory',
            ),
		);
	}

	/**
	 * Include/Return module configuration
	 * @return array
	 * @implements ConfigProviderInterface
	 */
	public function getConfig()
	{
		return include __DIR__ . '/../../config/module.config.php';
	}

	/**
	 * Return autoloader configuration
	 * @link http://framework.zend.com/manual/2.0/en/user-guide/modules.html
	 * @return array
	 */
	public function getAutoloaderConfig()
	{
        return array(
			AutoloaderFactory::STANDARD_AUTOLOADER => array(
				StandardAutoloader::LOAD_NS => array(
					__NAMESPACE__ => __DIR__,
				),
			),
		);
	}

    /**
     * Get Form Element Config
     * @return array
     */
	public function getFormElementConfig()
	{
	    return array(
	        'factories' => array(
	            'NetglueMoney\Form\Element\SelectCurrency' => 'NetglueMoney\Factory\CurrencySelectFactory',
	        ),
	        'aliases' => array(
	            'SelectCurrency' => 'NetglueMoney\Form\Element\SelectCurrency',
	        ),
	    );
	}

    /**
     * Get validator config
     * @return array
     */
    public function getValidatorConfig()
    {
        return array(
            'factories' => array(
                'NetglueMoney\Validator\CurrencyCode' => 'NetglueMoney\Factory\CurrencyCodeValidatorFactory',
            ),
        );
    }

}
