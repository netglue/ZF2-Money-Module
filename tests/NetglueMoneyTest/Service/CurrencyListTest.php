<?php

namespace NetglueMoney\Service;

use NetglueMoney\Money\Currency;
use NetglueMoney\Money\Money;

class CurrencyListTest extends  \PHPUnit_Framework_TestCase
{

    public function testNewInstanceAllowsAnyValidCurrency()
    {
        $list = new CurrencyList;
        $this->assertTrue($list->isAllowed('XXX'));

        return $list;
    }

    public function testConstructAppliesAllowList()
    {
        $list = new CurrencyList(array(
            'USD',
        ));
        $this->assertTrue($list->isAllowed('USD'));
        $this->assertFalse($list->isAllowed('XXX'));
        $this->assertCount(1, $list->getAllow());
    }

    public function testConstructAppliesDenyList()
    {
        $list = new CurrencyList(NULL, array(
            'USD',
        ));
        $this->assertFalse($list->isAllowed('USD'));
        $this->assertTrue($list->isAllowed('XXX'));
    }

    public function testAddInitialisesAllowedArray()
    {
        $list = new CurrencyList;
        $this->assertTrue($list->isAllowed('USD'));
        $this->assertSame($list, $list->add('GBP'));
        $this->assertFalse($list->isAllowed('USD'));
        $this->assertCount(1, $list->getAllow());
    }

    public function testRemoveInitialisesDenyArray()
    {
        $list = new CurrencyList;
        $this->assertTrue($list->isAllowed('USD'));
        $this->assertTrue($list->isAllowed('GBP'));
        $this->assertSame($list, $list->remove('GBP'));
        $this->assertTrue($list->isAllowed('USD'));
        $this->assertFalse($list->isAllowed('GBP'));
        $expect = count($list->getKnown()) - 1;
        $this->assertCount($expect, $list->getAllow());
    }

    /**
     * @expectedException NetglueMoney\Exception\InvalidArgumentException
     * @expectedExceptionMessage Currency code should be a string
     */
    public function testNonStringRaisesException()
    {
        $list = new CurrencyList;
        $list->add(123);
    }

    /**
     * @expectedException NetglueMoney\Exception\InvalidCurrencyCodeException
     * @expectedExceptionMessage not a valid ISO 4217 Currency code
     */
    public function testInvalidCodeRaisesException()
    {
        $list = new CurrencyList;
        $list->add('ZZZ');
    }

    public function testAddAcceptsMoneyAndCurrencyInstances()
    {
        $list = new CurrencyList;
        $money = new Money(100, new Currency('GBP'));
        $list->add($money);
        $this->assertSame(array('GBP'), $list->getAllow());

        $list = new CurrencyList;
        $code = new Currency('ZAR');
        $list->add($code);
        $this->assertSame(array('ZAR'), $list->getAllow());
    }

}
