<?php
declare(strict_types=1);

namespace NetglueMoneyTest\Form;

use Locale;
use NetglueMoney\Form\MoneyFieldset;
use NetglueMoney\Money\Currency;
use NetglueMoney\Money\Money;
use NetglueMoneyTest\Framework\TestCase;
use Zend\Form\ElementInterface;
use Zend\Form\Form;

class MoneyFieldsetTest extends TestCase
{

    public function testInitialDefaults()
    {
        $fieldset = new MoneyFieldset();
        $fieldset->init();
        $this->assertInstanceOf(ElementInterface::class, $fieldset->getCurrencyElement());
        $this->assertInstanceOf(ElementInterface::class, $fieldset->getAmountElement());
        $this->assertSame('currency', $fieldset->getCurrencyElement()->getName());
        $this->assertSame('amount', $fieldset->getAmountElement()->getName());
    }

    public function testSetGetLocale()
    {
        $fieldset = new MoneyFieldset;
        $this->assertSame(Locale::getDefault(), $fieldset->getLocale());
        $fieldset->setLocale('test');
        $this->assertSame('test', $fieldset->getLocale());
    }

    public function testSetGetDefaultCurrencyCode()
    {
        $fieldset = new MoneyFieldset;
        $this->assertNull($fieldset->getDefaultCurrencyCode());
        $fieldset->setDefaultCurrencyCode('GBP');
        $this->assertSame('GBP', $fieldset->getDefaultCurrencyCode());
    }

    public function testSetGetElementSpec()
    {
        $fieldset = new MoneyFieldset;
        $spec = ['foo' => 'bar'];
        $fieldset->setCurrencyElementSpec($spec);
        $this->assertSame($spec, $fieldset->getCurrencyElementSpec());
        $fieldset->setAmountElementSpec($spec);
        $this->assertSame($spec, $fieldset->getAmountElementSpec());
    }

    public function testSetMoneySetsBoundObject()
    {
        $fieldset = new MoneyFieldset;
        $money = new Money(123, new Currency('GBP'));
        $fieldset->setMoney($money);
        $this->assertSame($money, $fieldset->getObject());
        $this->assertSame($money, $fieldset->getMoney());
    }

    public function testSetMoneySetsElementValues()
    {
        $fieldset = new MoneyFieldset;
        $money = new Money(123, new Currency('GBP'));
        $fieldset->setMoney($money);
        $this->assertEquals(1.23, $fieldset->getAmountElement()->getValue());
        $this->assertEquals('GBP', $fieldset->getCurrencyElement()->getValue());
    }

    public function testMinMaxOptionsUpdateInputFilterSpec()
    {
        $fieldset = new MoneyFieldset;
        $spec = $fieldset->getInputFilterSpecification();
        $this->assertCount(1, $spec['amount']['validators']);

        $fieldset->setMinimumAmount(100, true, 'foo');

        $spec = $fieldset->getInputFilterSpecification();
        $this->assertCount(2, $spec['amount']['validators']);

        $this->assertSame(100, $spec['amount']['validators'][1]['options']['min']);
        $this->assertTrue($spec['amount']['validators'][1]['options']['inclusive']);

        $fieldset->setMaximumAmount(100, true, 'foo');

        $spec = $fieldset->getInputFilterSpecification();
        $this->assertCount(3, $spec['amount']['validators']);

        $this->assertSame(100, $spec['amount']['validators'][2]['options']['max']);
        $this->assertTrue($spec['amount']['validators'][2]['options']['inclusive']);
    }

    public function testDefaultCurrencySettingWillInitialiseCurrencyElementWithValue()
    {
        $fieldset = new MoneyFieldset('someName', ['default_currency' => 'USD']);
        $fieldset->init();
        $this->assertSame('USD', $fieldset->getCurrencyElement()->getValue());
    }

    public function testSettingRequiredFlagOnFieldsetAppliesToElements()
    {
        $formSpec = [
            'fieldsets' => [
                'money' => [
                    'spec' => [
                        'name' => 'money',
                        'type' => MoneyFieldset::class,
                        'attributes' => [
                            'required' => false,
                        ],
                    ],
                ],
            ],
        ];
        $postValues = [
            'money' => [
                'currency' => null,
                'amount' => null,
            ],
        ];
        $factory = $this->getFormFactory();
        $form = $factory->createForm($formSpec);

        $fieldset = $form->get('money');
        $this->assertInstanceOf(MoneyFieldset::class, $fieldset);
        $form->setData($postValues);
        $this->assertTrue($form->isValid());

        $formSpec['fieldsets']['money']['spec']['attributes']['required'] = true;
        $form = $factory->createForm($formSpec);
        $form->setData($postValues);
        $this->assertFalse($form->isValid());
    }
}
