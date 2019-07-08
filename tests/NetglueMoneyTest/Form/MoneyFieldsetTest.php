<?php
declare(strict_types=1);

namespace NetglueMoneyTest\Form;

use Locale;
use NetglueMoney\Form\MoneyFieldset;
use NetglueMoney\Money\Currency;
use NetglueMoney\Money\Money;
use NetglueMoneyTest\Framework\MoneyModel;
use NetglueMoneyTest\Framework\TestCase;
use Zend\Form\ElementInterface;
use Zend\Form\Form;
use Zend\Hydrator\ClassMethods;

class MoneyFieldsetTest extends TestCase
{

    public function testInitialDefaults() : void
    {
        $fieldset = new MoneyFieldset();
        $fieldset->init();
        $this->assertInstanceOf(ElementInterface::class, $fieldset->getCurrencyElement());
        $this->assertInstanceOf(ElementInterface::class, $fieldset->getAmountElement());
        $this->assertSame('currency', $fieldset->getCurrencyElement()->getName());
        $this->assertSame('amount', $fieldset->getAmountElement()->getName());
    }

    public function testSetGetLocale() : void
    {
        $fieldset = new MoneyFieldset;
        $this->assertSame(Locale::getDefault(), $fieldset->getLocale());
        $fieldset->setLocale('test');
        $this->assertSame('test', $fieldset->getLocale());
    }

    public function testSetGetDefaultCurrencyCode() : void
    {
        $fieldset = new MoneyFieldset;
        $this->assertNull($fieldset->getDefaultCurrencyCode());
        $fieldset->setDefaultCurrencyCode('GBP');
        $this->assertSame('GBP', $fieldset->getDefaultCurrencyCode());
    }

    public function testSetGetElementSpec() : void
    {
        $fieldset = new MoneyFieldset;
        $spec = ['foo' => 'bar'];
        $fieldset->setCurrencyElementSpec($spec);
        $this->assertSame($spec, $fieldset->getCurrencyElementSpec());
        $fieldset->setAmountElementSpec($spec);
        $this->assertSame($spec, $fieldset->getAmountElementSpec());
    }

    public function testSetMoneySetsBoundObject() : void
    {
        $fieldset = new MoneyFieldset;
        $money = new Money(123, new Currency('GBP'));
        $fieldset->setMoney($money);
        $this->assertSame($money, $fieldset->getObject());
        $this->assertSame($money, $fieldset->getMoney());
    }

    public function testSetMoneySetsElementValues() : void
    {
        $fieldset = new MoneyFieldset;
        $money = new Money(123, new Currency('GBP'));
        $fieldset->setMoney($money);
        $this->assertEquals(1.23, $fieldset->getAmountElement()->getValue());
        $this->assertEquals('GBP', $fieldset->getCurrencyElement()->getValue());
    }

    public function testMinMaxOptionsUpdateInputFilterSpec() : void
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

    public function testDefaultCurrencySettingWillInitialiseCurrencyElementWithValue() : void
    {
        $fieldset = new MoneyFieldset('someName', ['default_currency' => 'USD']);
        $fieldset->init();
        $this->assertSame('USD', $fieldset->getCurrencyElement()->getValue());
    }

    public function testSettingRequiredFlagOnFieldsetAppliesToElements() : void
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

    public function testBinding() : void
    {
        $formSpec = [
            'fieldsets' => [
                'money' => [
                    'spec' => [
                        'name' => 'money',
                        'type' => MoneyFieldset::class,
                        'attributes' => [
                            'required' => true,
                        ],
                    ],
                ],
                'optionalMoney' => [
                    'spec' => [
                        'name' => 'optionalMoney',
                        'type' => MoneyFieldset::class,
                        'attributes' => [
                            'required' => true,
                        ],
                    ],
                ],
            ],
        ];
        $factory = $this->getFormFactory();
        /** @var Form $form */
        $form = $factory->createForm($formSpec);
        $form->setHydrator(new ClassMethods(false, true));
        $model = new MoneyModel();
        $this->assertNull($model->money);
        $this->assertNull($model->optionalMoney);
        $form->bind($model);
        $form->setData([
            'money' => [
                'amount' => 100.23,
                'currency' => 'GBP',
            ],
            'optionalMoney' => [
                'amount' => 123.45,
                'currency' => 'GBP',
            ],
        ]);
        $this->assertTrue($form->isValid());
        $bound = $form->getData();
        $this->assertSame($model, $bound);
        $this->assertInstanceOf(Money::class, $model->money);
        $this->assertSame(10023, $model->money->getAmount());
        $this->assertSame('GBP', (string) $model->money->getCurrency());
        $this->assertInstanceOf(Money::class, $model->optionalMoney);
        $this->assertSame(12345, $model->optionalMoney->getAmount());
        $this->assertSame('GBP', (string) $model->optionalMoney->getCurrency());
    }

    public function testExpectedBehaviourOfGetValue() : void
    {
        $formSpec = [
            'fieldsets' => [
                'money' => [
                    'spec' => [
                        'name' => 'money',
                        'type' => MoneyFieldset::class,
                        'attributes' => [
                            'required' => true,
                        ],
                    ],
                ],
            ],
        ];
        $factory = $this->getFormFactory();
        /** @var Form $form */
        $form = $factory->createForm($formSpec);
        $form->setData([
            'money' => [
                'amount' => 100.23,
                'currency' => 'GBP',
            ],
        ]);
        $this->assertTrue($form->isValid());
        /** @var MoneyFieldset $fieldset */
        $fieldset = $form->get('money');
        $this->assertInstanceOf(MoneyFieldset::class, $fieldset);
        $this->assertInstanceOf(Money::class, $fieldset->getMoney());
    }
}
