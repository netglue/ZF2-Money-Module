<?php

namespace NetglueMoney\Form;

use Zend\Form\Fieldset;
use Zend\InputFilter\InputFilterProviderInterface;
use NetglueMoney\Hydrator\MoneyHydrator;
use NetglueMoney\Money\Money;
use NetglueMoney\Money\Currency;

class MoneyFieldset extends Fieldset implements InputFilterProviderInterface
{

    protected $allowedObjectBindingClass = 'NetglueMoney\Money\Money';

    /**
     * Currency Code Element Specification
     * @var array
     */
    protected $currencyElementSpec = array(
        'name' => 'currency',
        'type' => 'Zend\Form\Element\Text',
        'options' => array(

        ),
        'attributes' => array(
            'maxlength' => 3,
            'required' => true,
            'placeholder' => 'XXX',
        ),
    );

    /**
     * Amount Element Specification
     * @var array
     */
    protected $amountElementSpec = array(
        'name' => 'amount',
        'type' => 'Zend\Form\Element\Text',
        'options' => array(

        ),
        'attributes' => array(
            'required' => true,
            'placeholder' => '0.00',
        ),
    );

    /**
     * Init
     * @return void
     */
    public function init()
    {

        /**
         * Use specific hydrator that converts a money object to an array
         * with the keys 'amount', 'currency' and returns a new money
         * instance given an array with these keys
         */
        $this->setHydrator(new MoneyHydrator);
        $this->setObject(new Money(0, new Currency('XXX')));

        $this->add($this->getCurrencyElementSpec());
        $this->add($this->getAmountElementSpec());
    }

    /**
     * Get input spec
     * @return array
     */
    public function getInputFilterSpecification()
    {
        return array(
            'currency' => array(
                'required' => true,
                'filters' => array(
                    array('name' => 'StringTrim'),
                    array('name' => 'StringToUpper'),
                ),
                'validators' => array(
                    array('name' => 'NetglueMoney\Validator\CurrencyCode'),
                ),
            ),
            'amount' => array(
                'required' => true,
                'filters' => array(
                    array('name' => 'StringTrim'),
                    array(
                        'name' => 'Zend\I18n\Filter\NumberParse',
                        'options' => array(
                            'type' => \NumberFormatter::TYPE_CURRENCY
                        ),
                    ),
                ),
                'validators' => array(
                    array('name' => 'Zend\I18n\Validator\Float'),
                ),
            ),
        );
    }

    /**
     * Return currency element specification
     * @return array
     */
    public function getCurrencyElementSpec()
    {
        return $this->currencyElementSpec;
    }

    /**
     * Return amount element specification
     * @return array
     */
    public function getAmountElementSpec()
    {
        return $this->amountElementSpec;
    }
}
