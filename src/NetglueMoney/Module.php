<?php
declare(strict_types=1);

namespace NetglueMoney;

use NetglueMoney\Exception;

use Zend\ModuleManager\Feature\ConfigProviderInterface;
use Zend\ModuleManager\Feature\ServiceProviderInterface;
use Zend\ModuleManager\Feature\FormElementProviderInterface;
use Zend\ModuleManager\Feature\ValidatorProviderInterface;
use Zend\ModuleManager\Feature\ViewHelperProviderInterface;
use Zend\ServiceManager\Factory\InvokableFactory;

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
    public function getServiceConfig() : array
    {
        return [
            'factories' => [
                Service\CurrencyList::class => Factory\CurrencyListFactory::class,
            ],
        ];
    }

    public function getConfig() : array
    {
        return include __DIR__ . '/../../config/module.config.php';
    }

    public function getFormElementConfig() : array
    {
        return [
            'factories' => [
                Form\Element\SelectCurrency::class => Factory\CurrencySelectFactory::class,
                Form\MoneyFieldset::class          => InvokableFactory::class,
                Form\Element\Money::class          => InvokableFactory::class,
            ],
        ];
    }

    public function getValidatorConfig() : array
    {
        return [
            'factories' => [
                Validator\CurrencyCode::class => Factory\CurrencyCodeValidatorFactory::class,
            ],
        ];
    }

    public function getViewHelperConfig() : array
    {
        return [
            'factories' => [
                View\Helper\MoneyFormat::class => InvokableFactory::class,
                View\Helper\FormMoney::class   => InvokableFactory::class,
            ],
            'aliases' => [
                'moneyFormat' => View\Helper\MoneyFormat::class,
                'formMoney'   => View\Helper\FormMoney::class,
            ],
        ];
    }
}
