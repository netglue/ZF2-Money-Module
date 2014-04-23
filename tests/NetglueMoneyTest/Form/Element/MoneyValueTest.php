<?php

namespace NetglueMoney\Form\Element;

use NetglueMoney\Service\CurrencyList;

class MoneyValueTest extends \PHPUnit_Framework_TestCase
{

    protected $input = array(
        'en_GB' => array(
            array(
                'input' => '£5,000.00',
                'int' => 500000,
                'code' => 'GBP',
            ),
            array(
                'input' => '£1234',
                'int' => 123400,
                'code' => 'GBP',
            ),
            array(
                'input' => '£23.50',
                'int' => 2350,
                'code' => 'GBP',
            ),
        ),
        'en_US' => array(
            array(
                'input' => '$5,000.00',
                'int' => 500000,
                'code' => 'USD',
            ),
            array(
                'input' => '$1234',
                'int' => 123400,
                'code' => 'USD',
            ),
            array(
                'input' => '$23.50',
                'int' => 2350,
                'code' => 'USD',
            ),
        ),
    );

    public function testStuff()
    {
        foreach($this->input as $locale => $data) {
            foreach($data as $values) {
                $element = new MoneyValue;
                $element->setLocale($locale);
                $element->setValue($values['input']);
                $value = $element->getValue();
                $this->assertInstanceOf('NetglueMoney\Money\Money', $value);
                $this->assertSame($values['int'], $value->getAmount());
                $this->assertSame($values['code'], $value->getCurrencyCode());
            }
        }
    }

}
