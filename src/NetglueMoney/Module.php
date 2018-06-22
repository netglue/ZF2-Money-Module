<?php

namespace NetglueMoney;

use NetglueMoney\Exception;

/**
 * Config Provider
 */
use Zend\ModuleManager\Feature\ConfigProviderInterface;

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
        if (! extension_loaded('intl')) {
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
        return [
            'factories' => [
                Service\CurrencyList::class => Factory\CurrencyListFactory::class,
            ],
        ];
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
     * Get Form Element Config
     * @return array
     */
    public function getFormElementConfig()
    {
        return [
            'factories' => [
                'NetglueMoney\Form\Element\SelectCurrency' => 'NetglueMoney\Factory\CurrencySelectFactory',
            ],
            'aliases' => [
                'SelectCurrency' => 'NetglueMoney\Form\Element\SelectCurrency',
            ],
            'invokables' => [
                'NetglueMoney\Form\MoneyFieldset' => 'NetglueMoney\Form\MoneyFieldset',
                'NetglueMoney\Form\Element\Money' => 'NetglueMoney\Form\Element\Money',
            ],
        ];
    }

    /**
     * Get validator config
     * @return array
     */
    public function getValidatorConfig()
    {
        return [
            'factories' => [
                'NetglueMoney\Validator\CurrencyCode' => 'NetglueMoney\Factory\CurrencyCodeValidatorFactory',
            ],
        ];
    }

    public function getViewHelperConfig()
    {
        return [
            'invokables' => [
                'moneyFormat' => 'NetglueMoney\View\Helper\MoneyFormat',
                'formMoney' => 'NetglueMoney\View\Helper\FormMoney',
            ],
        ];
    }
}
