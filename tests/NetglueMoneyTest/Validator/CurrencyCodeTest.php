<?php

namespace NetglueMoney\Validator;

use NetglueMoney\Service\CurrencyList;

class CurrencyCodeTest extends  \PHPUnit_Framework_TestCase
{

    public function testSetGetCurrencyList()
    {
        $v = new CurrencyCode;
        $list = new CurrencyList;

        $this->assertInstanceOf('NetglueMoney\Service\CurrencyList', $v->getCurrencyList());
        $this->assertSame($v, $v->setCurrencyList($list));
        $this->assertSame($list, $v->getCurrencyList());

        return $v;
    }

    /**
     * @depends testSetGetCurrencyList
     */
    public function testBasic(CurrencyCode $v)
    {

        $this->assertFalse($v->isValid('123'));
        $this->assertFalse($v->isValid(array()));

        $this->assertTrue($v->isValid('GBP'));
        $this->assertTrue($v->isValid('USD'));
        $v->getCurrencyList()->add('GBP');
        $this->assertTrue($v->isValid('GBP'));
        $this->assertFalse($v->isValid('USD'));
    }

}
