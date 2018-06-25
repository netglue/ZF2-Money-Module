<?php
/**
 * Copyright (c) 2018. Net Glue Ltd
 *
 */

declare(strict_types=1);

namespace NetglueMoney;

use Zend\ServiceManager\Factory\InvokableFactory;

class ConfigProvider
{

    public function __invoke() : array
    {
        return [
            'dependencies'  => $this->getDependencyConfig(),
            'view_helpers'  => $this->getViewHelperConfig(),
            'validators'    => $this->getValidatorConfig(),
            'form_elements' => $this->getFormElementConfig(),
            'templates'     => $this->getTemplates(),
            'ng_money'      => $this->getModuleConfig(),
        ];
    }

    public function getModuleConfig() : array
    {
        return [
            'allowCurrencies' => null,
            'excludeCurrencies' => null,
        ];
    }

    public function getDependencyConfig() : array
    {
        return [
            'factories' => [
                Service\CurrencyList::class => Factory\CurrencyListFactory::class,
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

    public function getTemplateMap() : array
    {
        return [
            'netglue-money/form/money-fieldset' => __DIR__ . '/../../view/netglue-money/form/money-fieldset.phtml',
        ];
    }

    public function getTemplates() : array
    {
        return [
            'map' => $this->getTemplateMap(),
        ];
    }

}
