<?php

namespace NetglueMoney\Factory;

use Zend\ServiceManager\ServiceManager;

use NetglueMoney\Service\CurrencyListAwareInterface;
use NetglueMoney\Service\CurrencyListAwareTrait;

class CurrencyListInitializerTest extends \PHPUnit_Framework_TestCase
{

    public function testInitializerBasic()
    {
        $sm = new ServiceManager;
        $sm->setService('config', array());
        $sm->setFactory('NetglueMoney\Service\CurrencyList', new CurrencyListFactory);
        $list = $sm->get('NetglueMoney\Service\CurrencyList');

        $this->assertInstanceOf('NetglueMoney\Service\CurrencyList', $list);

        $init = new CurrencyListInitializer;
        $instance = new ListInititializerTester;
        $init->initialize($instance, $sm);

        $this->assertSame($list, $instance->getCurrencyList());
    }

    public function testInitializerWorksWithOtherLocators()
    {
        $sm = new ServiceManager;
        $sm->setService('config', array());
        $sm->setFactory('NetglueMoney\Service\CurrencyList', new CurrencyListFactory);
        $list = $sm->get('NetglueMoney\Service\CurrencyList');

        $vm = new \Zend\Validator\ValidatorPluginManager;
        $vm->setServiceLocator($sm);

        $init = new CurrencyListInitializer;
        $instance = new ListInititializerTester;
        $init->initialize($instance, $vm);
        $this->assertSame($list, $instance->getCurrencyList());
    }

    public function testTraitReturnsNewListWhenNoneSet()
    {
        $instance = new ListInititializerTester;
        $list = $instance->getCurrencyList();
        $this->assertInstanceOf('NetglueMoney\Service\CurrencyList', $list);
    }

}

class ListInititializerTester implements CurrencyListAwareInterface
{

    use CurrencyListAwareTrait;

}
