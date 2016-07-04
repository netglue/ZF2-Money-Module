<?php

namespace NetglueMoney;

use NetglueMoney\Exception;

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

/**
 * View Helper Provider
 */
use Zend\ModuleManager\Feature\ViewHelperProviderInterface;

/**
 * @codeCoverageIgnore
 */
class Module implements
    AutoloaderProviderInterface,
    ServiceProviderInterface,
    FormElementProviderInterface,
    ConfigProviderInterface,
    ValidatorProviderInterface,
    ViewHelperProviderInterface
{

    /**
     * @throws Exception\ExtensionNotLoadedException if ext/intl is not present
     */
    public function __construct()
    {
        if (!extension_loaded('intl')) {
            throw new Exception\ExtensionNotLoadedException(sprintf(
                '%s component requires the intl PHP extension',
                __NAMESPACE__
            ));
        }
    }

    /**
     * Return Service Config
     * @return array
     * @implements ServiceProviderInterface
     */
    public function getServiceConfig()
    {
        return array(
            'factories' => array(
                'NetglueMoney\Service\CurrencyList' => 'NetglueMoney\Factory\CurrencyListFactory',
                'NetglueMoney\I18n\DefaultLocale' => 'NetglueMoney\I18n\DefaultLocale',
            ),
            'invokables' => array(

            ),
            'aliases' => array(
                'DefaultLocale' => 'NetglueMoney\I18n\DefaultLocale',
            ),
            'initializers' => array(
                'NetglueMoney\I18n\DefaultLocale',
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
            'invokables' => array(
                'NetglueMoney\Form\MoneyFieldset' => 'NetglueMoney\Form\MoneyFieldset',
                'NetglueMoney\Form\Element\Money' => 'NetglueMoney\Form\Element\Money',
            ),
            'initializers' => array(
                'NetglueMoney\I18n\DefaultLocale',
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
            'initializers' => array(
                'NetglueMoney\I18n\DefaultLocale',
            ),
        );
    }

    public function getViewHelperConfig()
    {
        return array(
            'initializers' => array(
                'NetglueMoney\I18n\DefaultLocale',
            ),
            'invokables' => array(
                'moneyFormat' => 'NetglueMoney\View\Helper\MoneyFormat',
                'formMoney' => 'NetglueMoney\View\Helper\FormMoney',
            ),
        );
    }
}
