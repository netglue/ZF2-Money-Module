<?php

namespace NetglueMoney\Factory;

use Zend\ServiceManager\ServiceManager;


class CurrencyListFactoryTest extends \PHPUnit_Framework_TestCase
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
        $sm->setFactory('currencyList', new CurrencyListFactory);
        $service = $sm->get('currencyList');
        $this->assertInstanceOf('NetglueMoney\Service\CurrencyList', $service);

        $this->assertTrue($service->isAllowed('GBP'));
        $this->assertFalse($service->isAllowed('USD'));
        $this->assertFalse($service->isAllowed('ZAR'));
    }

}
