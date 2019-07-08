<?php
declare(strict_types=1);

namespace NetglueMoneyTest\Form\Element;

use NetglueMoney\Form\Element\SelectCurrency;
use NetglueMoney\Money\Currency;
use NetglueMoney\Service\CurrencyList;
use NetglueMoneyTest\Framework\TestCase;

class SelectCurrencyTest extends TestCase
{

    public function testSetGetDisplayNames() : void
    {
        $element = new SelectCurrency(new CurrencyList());
        $this->assertInternalType('bool', $element->getDisplayNames(), 'Display names options should be initially set to a bool');
        $element->setDisplayNames(true);
        $this->assertTrue($element->getDisplayNames());
        $element->setDisplayNames(false);
        $this->assertFalse($element->getDisplayNames());
    }

    public function testMakeCurrency() : void
    {
        $element = new SelectCurrency(new CurrencyList());
        $this->assertInstanceOf(Currency::class, $element->makeCurrency('ZAR'));
        $this->assertNull($element->makeCurrency(''));
        $this->assertNull($element->makeCurrency(null));
        $this->assertNull($element->makeCurrency(100));
    }

    public function testGetInputSpecReturnsArray() : void
    {
        $element = new SelectCurrency(new CurrencyList(), 'foo');
        $spec = $element->getInputSpecification();
        $this->assertInternalType('array', $spec);
        $this->assertArrayHasKey('name', $spec);
        $this->assertSame('foo', $spec['name']);
    }

    public function testGetValueOptionsIsSeededWithList() : void
    {
        $list = new CurrencyList();
        $element = new SelectCurrency($list);
        $element->setDisplayNames(false);

        $options = $element->getValueOptions();
        foreach ($options as $code => $label) {
            $this->assertSame($code, $label);
            $this->assertTrue($list->isAllowed($code));
        }
    }

    public function testSetValueConvertsToCurrencyInstance() : void
    {
        $element = new SelectCurrency(new CurrencyList());
        $element->setValue('GBP');
        $value = $element->getValue();
        $this->assertInstanceOf(Currency::class, $value);
    }

    public function testSetValueMultipleConvertsToCurrencyInstance() : void
    {
        $element = new SelectCurrency(new CurrencyList());
        $element->setAttribute('multiple', true);
        $element->setValue([
            'GBP', 'ZAR', 'USD',
        ]);
        $value = $element->getValue();
        $this->assertInternalType('array', $value);
        $this->assertContainsOnlyInstancesOf(Currency::class, $value);

        $element->setValue('GBP');
        $value = $element->getValue();
        $this->assertInternalType('array', $value);
        $this->assertContainsOnlyInstancesOf(Currency::class, $value);

        $input = new \ArrayObject([
            'GBP', 'USD',
        ]);
        $element->setValue($input);
        $value = $element->getValue();
        $this->assertInternalType('array', $value);
        $this->assertContainsOnlyInstancesOf(Currency::class, $value);

        $element->setValue(null);
        $this->assertSame([], $element->getValue());
    }
}
