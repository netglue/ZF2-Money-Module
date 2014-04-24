<?php

namespace NetglueMoney\Form\Element;

use NetglueMoney\Money\Money;
use NetglueMoney\Money\Currency;

use NetglueMoney\Service\CurrencyList;

use NetglueMoneyTest\bootstrap;


class MoneyElementTest extends \PHPUnit_Framework_TestCase
{

    public function testCanCreateElement()
    {
        $element = new MoneyElement;

        return $element;
    }

    /**
     * @depends testCanCreateElement
     */
    public function testAmountAndCodeAreInitiallyNull(MoneyElement $element)
    {
        $this->assertNull($element->getValue());
        $this->assertNull($element->getAmount());
        $this->assertNull($element->getCurrencyCode());
        return $element;
    }

    /**
     * @depends testAmountAndCodeAreInitiallyNull
     */
    public function testElementSpecsAreArrays(MoneyElement $element)
    {
        $this->assertInternalType('array', $element->getCurrencyElementSpec());
        $this->assertInternalType('array', $element->getAmountElementSpec());
        return $element;
    }

    public function testElementCreatedViaServiceManagerHasFactoryInstanceSeededWithElementManager()
    {
        $sl = bootstrap::getServiceManager();
        $formElementManager = $sl->get('FormElementManager');
        $this->assertInstanceOf('Zend\Form\FormElementManager', $formElementManager);
        $element = $formElementManager->get('NetglueMoney\Form\Element\MoneyElement');
        $this->assertInstanceOf('NetglueMoney\Form\Element\MoneyElement', $element);
        $this->assertInstanceOf('Zend\Form\Factory', $element->getFormFactory());
        $this->assertSame($formElementManager, $element->getFormFactory()->getFormElementManager());
        return $element;
    }

    /**
     * @depends testElementCreatedViaServiceManagerHasFactoryInstanceSeededWithElementManager
     */
    public function testGetCurrencyElementReturnsElement(MoneyElement $element)
    {
        $currency = $element->getCurrencyElement();
        $this->assertInstanceOf('Zend\Form\ElementInterface', $currency);
        $this->assertNull($currency->getValue());
        $element->setCurrencyCode('GBP');
        $this->assertSame('GBP', $element->getCurrencyCode());
        $this->assertSame('GBP', $currency->getValue());
    }

    /**
     * @depends testElementCreatedViaServiceManagerHasFactoryInstanceSeededWithElementManager
     */
    public function testGetAmountElementReturnsElement(MoneyElement $element)
    {
        $amount = $element->getAmountElement();
        $this->assertInstanceOf('Zend\Form\ElementInterface', $amount);
        $this->assertNull($amount->getValue());
        $element->setAmount('1111.11');
        $this->assertSame(1111.11, $element->getAmount());
        $this->assertSame(1111.11, $amount->getValue());
    }

    public function testSetValue() {
        $element = new MoneyElement;
        $element->setValue(array(
            'code' => 'GBP',
            'amount' => '1234.56',
        ));

        $money = $element->getValue();
        $this->assertInstanceOf('NetglueMoney\Money\Money', $money);
        $this->assertSame(123456, $money->getAmount());
        $this->assertSame('GBP', (string) $money->getCurrency());
        return $element;
    }

    public function testSetValueAcceptsMoney()
    {
        $element = new MoneyElement;
        $money = new Money(123456, new Currency('GBP'));
        $element->setValue($money);
        $this->assertSame($money, $element->getValue());
        $this->assertSame(1234.56, $element->getAmount());
        $this->assertSame('GBP', $element->getCurrencyCode());
    }

    /**
     * @expectedException Zend\Form\Exception\InvalidArgumentException
     */
    public function testSetValueThrowsException()
    {
        $element = new MoneyElement;
        $element->setValue('1234');
    }

    /**
     * @expectedException Zend\Form\Exception\InvalidArgumentException
     */
    public function testSetAmountThrowsException()
    {
        $element = new MoneyElement;
        $element->setAmount('Foo');
    }

    public function testElementHasValidatorManagerWhenCreatedViaServiceManager()
    {
        return;
        $sl = bootstrap::getServiceManager();
        $formElementManager = $sl->get('FormElementManager');
        $element = $formElementManager->get('NetglueMoney\Form\Element\MoneyElement');
        $this->assertInstanceOf('Zend\Validator\ValidatorPluginManager', $element->getValidatorPluginManager());
    }

    public function testDefaultValidationViaForm()
    {
        $formSpec = array(
            'elements' => array(
                array(
                    'spec' => array(
                        'type' => 'NetglueMoney\Form\Element\MoneyElement',
                        'name' => 'money',
                    ),
                ),
            ),
        );
        $sl = bootstrap::getServiceManager();
        $formElementManager = $sl->get('FormElementManager');
        $factory = new \Zend\Form\Factory;
        $factory->setFormElementManager($formElementManager);
        $form = $factory->createForm($formSpec);
        $form->setData(array());
        $this->assertFalse($form->isValid());

        $form->setData(array('money' => array(
            'code' => '123',
            'amount' => 'ABC',
        )));
        $this->assertFalse($form->isValid());
    }
}
