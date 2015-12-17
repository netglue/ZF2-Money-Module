<?php

namespace NetglueMoney\Form;

use Locale;
use NetglueMoney\I18n\LocaleAwareInterface;
use Zend\Form\Fieldset;
use Zend\InputFilter\InputFilterProviderInterface;
use NetglueMoney\Hydrator\MoneyHydrator;
use NetglueMoney\Money\Money;
use NetglueMoney\Money\Currency;
use Zend\Stdlib\InitializableInterface;

use Zend\Validator\GreaterThan;
use Zend\Validator\LessThan;

class MoneyFieldset extends Fieldset implements
    InputFilterProviderInterface,
    LocaleAwareInterface,
    InitializableInterface
{

    /**
     * Locale string used for interpreting inputted numbers
     * @var string
     */
    protected $locale;

    /**
     * Money instances are all we want
     * @var string
     */
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
        'type' => 'NetglueMoney\Form\Element\Money',
        'options' => array(

        ),
        'attributes' => array(
            'required' => true,
            'placeholder' => '0.00',
        ),
    );

    /**
     * Options used to seed the GreaterThan validator if required
     * @var array
     */
    private $minimumOptions = [];

    /**
     * Options used to seed the LessThan validator if required
     * @var array
     */
    private $maximumOptions = [];

    /**
     * @param  null|int|string  $name    Optional name for the element
     * @param  array            $options Optional options for the element
     */
    public function __construct($name = null, $options = array())
    {
        parent::__construct($name, $options);
        /**
         * Use specific hydrator that converts a money object to an array
         * with the keys 'amount', 'currency' and returns a new money
         * instance given an array with these keys
         */
        $this->setHydrator(new MoneyHydrator);
    }

    /**
     * Init
     * @return void
     */
    public function init()
    {
        $code = $this->getDefaultCurrencyCode();
        if (!$code) {
            $code = 'XXX';
        }
        $this->initialiseElements();
        $this->setMoney(new Money(0, new Currency($code)));
    }

    /**
     * Adds the required elements if they do not already exist
     * @return void
     */
    protected function initialiseElements()
    {
        if(!$this->has('currency')) {
            $this->add($this->getCurrencyElementSpec());
        }
        if(!$this->has('amount')) {
            $this->add($this->getAmountElementSpec());
        }
    }

    /**
     * Get input spec
     * @return array
     */
    public function getInputFilterSpecification()
    {
        $required = true;
        if ($this->hasAttribute('required')) {
            $required = $this->getAttribute('required');
        }

        $amountValidators = array(
            array(
                'name' => 'Zend\I18n\Validator\IsFloat',
                'options' => array(
                    'locale' => $this->getLocale(),
                ),
            ),
        );

        if(count($this->minimumOptions)) {
            $spec = array(
                'name' => 'Zend\Validator\GreaterThan',
                'options' => array(
                    'min' => $this->minimumOptions['min'],
                    'inclusive' => $this->minimumOptions['inclusive'],
                ),
            );
            if(!empty($this->minimumOptions['message'])) {
                $spec['options']['messages'] = array(
                    GreaterThan::NOT_GREATER => $this->minimumOptions['message'],
                    GreaterThan::NOT_GREATER_INCLUSIVE => $this->minimumOptions['message'],
                );
            }
            $amountValidators[] = $spec;
        }
        if(count($this->maximumOptions)) {
            $spec = array(
                'name' => 'Zend\Validator\LessThan',
                'options' => array(
                    'max' => $this->maximumOptions['max'],
                    'inclusive' => $this->maximumOptions['inclusive'],
                ),
            );
            if(!empty($this->maximumOptions['message'])) {
                $spec['options']['messages'] = array(
                    LessThan::NOT_LESS => $this->maximumOptions['message'],
                    LessThan::NOT_LESS_INCLUSIVE => $this->maximumOptions['message'],
                );
            }
            $amountValidators[] = $spec;
        }

        return array(
            'currency' => array(
                'required' => $required,
                'filters' => array(
                    array('name' => 'StringTrim'),
                    array('name' => 'StringToUpper'),
                ),
                'validators' => array(
                    array('name' => 'NetglueMoney\Validator\CurrencyCode'),
                ),
            ),
            'amount' => array(
                'required' => $required,
                'filters' => array(
                    array('name' => 'StringTrim'),
                    array(
                        'name' => 'Zend\I18n\Filter\NumberParse',
                        'options' => array(
                            'style' => \NumberFormatter::DECIMAL,
                            'type' => \NumberFormatter::TYPE_DOUBLE,
                            'locale' => $this->getLocale(),
                        ),
                    ),
                ),
                'validators' => $amountValidators,
            ),
        );
    }

    /**
     * Set the given money object as the bound object, and populate the form fields with the values
     * @param Money $money
     */
    public function setMoney(Money $money)
    {
        $this->setObject($money);
        $this->initialiseElements();
        $this->populateValues($this->extract());
        return $this;
    }

    /**
     * Return the bound Money object if any
     * @return Money|NULL
     */
    public function getMoney()
    {
        return $this->getObject();
    }

    /**
     * Return the currency element
     * @return \Zend\Form\ElementInterface
     */
    public function getCurrencyElement()
    {
        return $this->get('currency');
    }

    /**
     * Return the amount element
     * @return \Zend\Form\ElementInterface
     */
    public function getAmountElement()
    {
        return $this->get('amount');
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
     * Set the currency element specification
     * @param  array $spec
     * @return self
     */
    public function setCurrencyElementSpec(array $spec)
    {
        $this->currencyElementSpec = $spec;

        return $this;
    }

    /**
     * Return amount element specification
     * @return array
     */
    public function getAmountElementSpec()
    {
        return $this->amountElementSpec;
    }

    /**
     * Set amount element specification
     * @param  array $spec
     * @return self
     */
    public function setAmountElementSpec(array $spec)
    {
        $this->amountElementSpec = $spec;

        return $this;
    }

    /**
     * Sets the locale option
     *
     * @param  string|null    $locale
     * @return AbstractLocale
     */
    public function setLocale($locale = null)
    {
        $this->locale = $locale;

        return $this;
    }

    /**
     * Returns the locale option
     *
     * @return string
     */
    public function getLocale()
    {
        if (null === $this->locale) {
            return Locale::getDefault();
        }

        return $this->locale;
    }

    /**
     * Set Default Currency Code
     * @param  string $code
     * @return self
     */
    public function setDefaultCurrencyCode($code)
    {
        $this->options['default_currency'] = (string) $code;
        $this->currencyElementSpec['attributes']['value'] = (string) $code;

        return $this;
    }

    /**
     * Get Default Currency Code
     * @return string|NULL
     */
    public function getDefaultCurrencyCode()
    {
        if (isset($this->options['default_currency'])) {
            return $this->options['default_currency'];
        }

        return NULL;
    }

    /**
     * Set a minimum amount with an optional error message
     * @param float|int $min
     * @param bool $inclusive
     * @param string $message
     */
    public function setMinimumAmount($min, $inclusive = false, $message = null)
    {
        $this->minimumOptions = [
            'min' => $min,
            'inclusive' => $inclusive,
            'message' => $message,
        ];
    }

    /**
     * Set a maximum amount with an optional error message
     * @param float|int $max
     * @param bool $inclusive
     * @param string $message
     */
    public function setMaximumAmount($max, $inclusive = false, $message = null)
    {
        $this->maximumOptions = [
            'max' => $max,
            'inclusive' => $inclusive,
            'message' => $message,
        ];
    }

}
