<?php

namespace NetglueMoney\Form;
use Locale;
use NetglueMoney\Money\Money;
use NetglueMoney\Money\Currency;

class MoneyFieldsetTest extends \PHPUnit_Framework_TestCase
{

    public function testIntitalDefaults()
    {
        $fieldset = new MoneyFieldset;
        $fieldset->init();
        $this->assertInstanceOf('\Zend\Form\ElementInterface', $fieldset->getCurrencyElement());
        $this->assertInstanceOf('\Zend\Form\ElementInterface', $fieldset->getAmountElement());
        $this->assertSame('currency', $fieldset->getCurrencyElement()->getName());
        $this->assertSame('amount', $fieldset->getAmountElement()->getName());


    }

    public function testSetGetLocale()
    {
        $fieldset = new MoneyFieldset;
        $this->assertSame(Locale::getDefault(), $fieldset->getLocale());
        $this->assertSame($fieldset, $fieldset->setLocale('test'));
        $this->assertSame('test', $fieldset->getLocale());
    }

    public function testSetGetDefaultCurrencyCode()
    {
        $fieldset = new MoneyFieldset;
        $this->assertNull($fieldset->getDefaultCurrencyCode());
        $this->assertSame($fieldset, $fieldset->setDefaultCurrencyCode('GBP'));
        $this->assertSame('GBP', $fieldset->getDefaultCurrencyCode());
    }

    public function testSetGetElementSpec()
    {
        $fieldset = new MoneyFieldset;
        $spec = array('foo' => 'bar');
        $this->assertSame($fieldset, $fieldset->setCurrencyElementSpec($spec));
        $this->assertSame($spec, $fieldset->getCurrencyElementSpec());
        $this->assertSame($fieldset, $fieldset->setAmountElementSpec($spec));
        $this->assertSame($spec, $fieldset->getAmountElementSpec());
    }

    public function testBindingWorksAsExpected()
    {
        $form = new \Zend\Form\Form;
        $form->setHydrator(new \Zend\Stdlib\Hydrator\ClassMethods);

        $fieldset = new MoneyFieldset;
        $fieldset->init();
        $form->add($fieldset, array('name' => 'money'));

        $model = new TestModel;
        $form->bind($model);
        $this->assertEquals(5432.1, $fieldset->get('amount')->getValue());
        $this->assertEquals('ZAR', $fieldset->get('currency')->getValue());

        $form->setData(array(
            'money' => array(
                'amount' => 1234.56,
                'currency' => 'GBP',
            ),
        ));
        $this->assertTrue($form->isValid());
        $bound = $form->getData();
        $this->assertInstanceOf('NetglueMoney\Form\TestModel', $bound);
        $this->assertInstanceOf('NetglueMoney\Money\Money', $bound->money);
        $this->assertSame(123456, $bound->money->getAmount());
        $this->assertSame('GBP', $bound->money->getCurrencyCode());
    }


}

class TestModel
{
    /**
     * @var Money
     */
    public $money;

    public function setMoney(Money $money = NULL)
    {
        $this->money = $money;
        return $this;
    }

    public function getMoney()
    {
        if(!$this->money) {
            $this->money = new Money(543210, new Currency('ZAR'));
        }
        return $this->money;
    }

    public function getArrayCopy()
    {
        return array(
            'money' => $this->money,
        );
    }

}
