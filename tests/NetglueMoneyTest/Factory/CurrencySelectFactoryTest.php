<?php

namespace NetglueMoney\Factory;

use Zend\ServiceManager\ServiceManager;

class CurrencySelectFactoryTest extends \PHPUnit_Framework_TestCase
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
        $list = $sm->get('NetglueMoney\Service\CurrencyList');

        $formElementManager = new \Zend\Form\FormElementManager;
        $formElementManager->setServiceLocator($sm);

        $validatorManager = new \Zend\Validator\ValidatorPluginManager;
        $validatorManager->setServiceLocator($sm);

        $sm->setService('ValidatorManager', $validatorManager);

        $formElementManager->setFactory('select', new CurrencySelectFactory);

        $select = $formElementManager->get('select');
        $this->assertInstanceOf('NetglueMoney\Form\Element\SelectCurrency', $select);
        $this->assertInstanceOf('NetglueMoney\Service\CurrencyList', $select->getCurrencyList());
        $this->assertInstanceOf('NetglueMoney\Validator\CurrencyCode', $select->getValidator());
    }

    /**
     * @expectedException UnexpectedValueException
     */
    public function testFormElementManagerIsRequired()
    {
        $sm = new ServiceManager;
        $factory = new CurrencySelectFactory;
        $factory->createService($sm);
    }

}
