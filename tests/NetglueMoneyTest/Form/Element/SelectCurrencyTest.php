<?php

namespace NetglueMoney\Form\Element;

use NetglueMoney\Service\CurrencyList;

class SelectCurrencyTest extends \PHPUnit_Framework_TestCase
{

    public function testCanCreateInstance()
    {
        $element = new SelectCurrency('myName', array());
        $this->assertInstanceOf('NetglueMoney\Form\Element\SelectCurrency', $element);

        return $element;
    }

    /**
     * @depends testCanCreateInstance
     */
    public function testSetGetCurrencyList(SelectCurrency $element)
    {
        $list = new CurrencyList;
        $list->add('GBP')->add('USD');

        $this->assertInstanceOf('NetglueMoney\Service\CurrencyList', $element->getCurrencyList());
        $this->assertSame($element, $element->setCurrencyList($list));
        $this->assertSame($list, $element->getCurrencyList());

        return $element;
    }

    /**
     * @depends testSetGetCurrencyList
     */
    public function testSetGetDisplayNames(SelectCurrency $element)
    {
        $this->assertInternalType('bool', $element->getDisplayNames(), 'Display names options should be initially set to a bool');
        $this->assertSame($element, $element->setDisplayNames(true));
        $this->assertTrue($element->getDisplayNames());
        $element->setDisplayNames(false);
        $this->assertFalse($element->getDisplayNames());

        return $element;
    }

    /**
     * @depends testSetGetDisplayNames
     */
    public function testMakeCurrency(SelectCurrency $element)
    {
        $this->assertInstanceOf('NetglueMoney\Money\Currency', $element->makeCurrency('ZAR'));
        $this->assertNull($element->makeCurrency(''));
        $this->assertNull($element->makeCurrency(NULL));
        $this->assertNull($element->makeCurrency(100));

        return $element;
    }

    /**
     * @depends testMakeCurrency
     */
    public function testGetInputSpecReturnsArray(SelectCurrency $element)
    {
        $spec = $element->getInputSpecification();
        $this->assertInternalType('array', $spec);
        $this->assertArrayHasKey('name', $spec);
        $this->assertSame($element->getName(), $spec['name']);

        return $element;
    }

    /**
     * @depends testGetInputSpecReturnsArray
     */
    public function testGetValueOptionsIsSeededWithList(SelectCurrency $element)
    {
        $list = $element->getCurrencyList();

        $element->setDisplayNames(false);

        $options = $element->getValueOptions();
        foreach($options as $code => $label) {
            $this->assertSame($code, $label);
            $this->assertTrue($list->isAllowed($code));
        }
        return $element;
    }

    public function testSetValueConvertsToCurrencyInstance()
    {
        $element = new SelectCurrency('myName', array());
        $element->setValue('GBP');
        $value = $element->getValue();
        $this->assertInstanceOf('NetglueMoney\Money\Currency', $value);
    }

    public function testSetValueMultipleConvertsToCurrencyInstance()
    {
        $element = new SelectCurrency('myName');
        $element->setAttribute('multiple', true);
        $element->setValue(array(
            'GBP', 'ZAR', 'USD',
        ));
        $value = $element->getValue();
        $this->assertInternalType('array', $value);
        $this->assertContainsOnlyInstancesOf('NetglueMoney\Money\Currency', $value);

        $element->setValue('GBP');
        $value = $element->getValue();
        $this->assertInternalType('array', $value);
        $this->assertContainsOnlyInstancesOf('NetglueMoney\Money\Currency', $value);

        $input = new \ArrayObject(array(
            'GBP', 'USD'
        ));
        $element->setValue($input);
        $value = $element->getValue();
        $this->assertInternalType('array', $value);
        $this->assertContainsOnlyInstancesOf('NetglueMoney\Money\Currency', $value);

        $element->setValue(NULL);
        $this->assertSame(array(), $element->getValue());
    }

    public function testGetSetValidator()
    {
        $element = new SelectCurrency('myName');
        $v = $element->getValidator();
        $this->assertInstanceOf('NetglueMoney\Validator\CurrencyCode', $v);
        $this->assertSame($v, $element->getValidator());

        $new = new \NetglueMoney\Validator\CurrencyCode;
        $this->assertSame($element, $element->setValidator($new));
        $this->assertSame($new, $element->getValidator());
    }
}
