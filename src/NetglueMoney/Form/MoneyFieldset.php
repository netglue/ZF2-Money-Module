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

        $this->add(array(
            'name' => 'currency',
            'type' => 'Zend\Form\Element\Text',
            'attributes' => array(
                'maxlength' => 3,
                'placeholder' => 'XXX',
            ),
        ));
        $this->add(array(
            'name' => 'amount',
            'type' => 'Zend\Form\Element\Text',
        ));
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
}
