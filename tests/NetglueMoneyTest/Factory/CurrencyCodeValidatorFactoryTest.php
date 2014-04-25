<?php

namespace NetglueMoney\Factory;

use Zend\ServiceManager\ServiceManager;


class CurrencyCodeValidatorFactoryTest extends \PHPUnit_Framework_TestCase
{

    public function testCanBeRegisteredAsFactory()
    {
        $sm = new ServiceManager;
        $sm->setService('config', array(
            'ng_money' => array(
                'allowCurrencies' => array(
                    'GBP', 'USD',
                ),
                'excludeCurrencies' => array(
                    'USD',
                ),
            ),
        ));
        $sm->setFactory('NetglueMoney\Service\CurrencyList', new CurrencyListFactory);

        $validatorManager = new \Zend\Validator\ValidatorPluginManager;
        $validatorManager->setServiceLocator($sm);

        $sm->setService('ValidatorManager', $validatorManager);

        $validatorManager->setFactory('mine', new CurrencyCodeValidatorFactory);

        $valid = $validatorManager->get('mine');
        $this->assertInstanceOf('NetglueMoney\Validator\CurrencyCode', $valid);
        $this->assertInstanceOf('NetglueMoney\Service\CurrencyList', $valid->getCurrencyList());
    }



}
