<?php

namespace NetglueMoneyTest\Form;

use Locale;
use NetglueMoney\Form\MoneyFieldset;
use NetglueMoney\Money\Money;
use NetglueMoney\Money\Currency;
use NetglueMoneyTest\Framework\TestCase;
use Zend\Form\ElementInterface;
use Zend\Hydrator\ClassMethods;

class MoneyFieldsetTest extends TestCase
{

    public function testIntitalDefaults()
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

    public function testBindingWorksAsExpected()
    {
        return $this->markTestSkipped('Skipping binding test as the entire dependency graph will need to be built');

        $form = new \Zend\Form\Form;
        $form->setHydrator(new ClassMethods);

        $fieldset = new MoneyFieldset;
        $fieldset->init();
        $form->add($fieldset, ['name' => 'money']);

        $model = new TestModel;
        $form->bind($model);
        $this->assertEquals(5432.1, $fieldset->get('amount')->getValue());
        $this->assertEquals('ZAR', $fieldset->get('currency')->getValue());

        $form->setData([
            'money' => [
                'amount' => 1234.56,
                'currency' => 'GBP',
            ],
        ]);
        $this->assertTrue($form->isValid());
        $bound = $form->getData();
        $this->assertInstanceOf('NetglueMoney\Form\TestModel', $bound);
        $this->assertInstanceOf('NetglueMoney\Money\Money', $bound->money);
        $this->assertSame(123456, $bound->money->getAmount());
        $this->assertSame('GBP', $bound->money->getCurrencyCode());
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
}

class TestModel
{
    /**
     * @var Money
     */
    public $money;

    public function setMoney(Money $money = null)
    {
        $this->money = $money;

        return $this;
    }

    public function getMoney()
    {
        if (! $this->money) {
            $this->money = new Money(543210, new Currency('ZAR'));
        }

        return $this->money;
    }

    public function getArrayCopy()
    {
        return [
            'money' => $this->money,
        ];
    }
}
