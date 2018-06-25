<?php
declare(strict_types=1);

namespace NetglueMoney\Form;

use Locale;
use NetglueMoney\Hydrator\MoneyHydrator;
use NetglueMoney\Money\Money;
use NetglueMoney\Validator\CurrencyCode;
use NumberFormatter;
use Zend\Filter\StringToUpper;
use Zend\Filter\StringTrim;
use Zend\Form\Element as ZendElement;
use Zend\Form\ElementInterface;
use Zend\Form\Fieldset;
use Zend\I18n\Filter\NumberParse;
use Zend\I18n\Validator\IsFloat;
use Zend\InputFilter\InputFilterProviderInterface;
use Zend\Validator\GreaterThan;
use Zend\Validator\LessThan;

class MoneyFieldset extends Fieldset implements InputFilterProviderInterface
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
    protected $allowedObjectBindingClass = Money::class;

    /**
     * Currency Code Element Specification
     * @var array
     */
    protected $currencyElementSpec = [
        'name' => 'currency',
        'type' => ZendElement\Text::class,
        'options' => [

        ],
        'attributes' => [
            'maxlength' => 3,
            'required' => true,
            'placeholder' => 'XXX',
        ],
    ];

    /**
     * Amount Element Specification
     * @var array
     */
    protected $amountElementSpec = [
        'name' => 'amount',
        'type' => Element\Money::class,
        'options' => [

        ],
        'attributes' => [
            'required' => true,
            'placeholder' => '0.00',
        ],
    ];

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
    public function __construct($name = null, $options = [])
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
        $this->initialiseElements();
        $code = $this->getDefaultCurrencyCode();
        if ($code) {
            $this->get('currency')->setValue($code);
        }
    }

    /**
     * Adds the required elements if they do not already exist
     * @return void
     */
    private function initialiseElements()
    {
        if (! $this->has('currency')) {
            $this->add($this->getCurrencyElementSpec());
        }
        if (! $this->has('amount')) {
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

        $amountValidators = [
            [
                'name' => IsFloat::class,
                'options' => [
                    'locale' => $this->getLocale(),
                ],
            ],
        ];

        if (count($this->minimumOptions)) {
            $spec = [
                'name' => GreaterThan::class,
                'options' => [
                    'min' => $this->minimumOptions['min'],
                    'inclusive' => $this->minimumOptions['inclusive'],
                ],
            ];
            if (! empty($this->minimumOptions['message'])) {
                $spec['options']['messages'] = [
                    GreaterThan::NOT_GREATER => $this->minimumOptions['message'],
                    GreaterThan::NOT_GREATER_INCLUSIVE => $this->minimumOptions['message'],
                ];
            }
            $amountValidators[] = $spec;
        }
        if (count($this->maximumOptions)) {
            $spec = [
                'name' => LessThan::class,
                'options' => [
                    'max' => $this->maximumOptions['max'],
                    'inclusive' => $this->maximumOptions['inclusive'],
                ],
            ];
            if (! empty($this->maximumOptions['message'])) {
                $spec['options']['messages'] = [
                    LessThan::NOT_LESS => $this->maximumOptions['message'],
                    LessThan::NOT_LESS_INCLUSIVE => $this->maximumOptions['message'],
                ];
            }
            $amountValidators[] = $spec;
        }

        return [
            'currency' => [
                'required' => $required,
                'filters' => [
                    ['name' => StringTrim::class],
                    ['name' => StringToUpper::class],
                ],
                'validators' => [
                    ['name' => CurrencyCode::class],
                ],
            ],
            'amount' => [
                'required' => $required,
                'filters' => [
                    ['name' => StringTrim::class],
                    [
                        'name' => NumberParse::class,
                        'options' => [
                            'style' => NumberFormatter::DECIMAL,
                            'type' => NumberFormatter::TYPE_DOUBLE,
                            'locale' => $this->getLocale(),
                        ],
                    ],
                ],
                'validators' => $amountValidators,
            ],
        ];
    }

    /**
     * Set the given money object as the bound object, and populate the form fields with the values
     * @param Money $money
     */
    public function setMoney(Money $money) : void
    {
        $this->setObject($money);
        $this->initialiseElements();
        $this->populateValues($this->extract());
    }

    /**
     * Return the bound Money object if any
     * @return Money|NULL
     */
    public function getMoney() :? Money
    {
        return $this->getObject();
    }

    /**
     * Return the currency element
     * @return \Zend\Form\ElementInterface
     */
    public function getCurrencyElement() : ElementInterface
    {
        $this->initialiseElements();
        return $this->get('currency');
    }

    /**
     * Return the amount element
     * @return \Zend\Form\ElementInterface
     */
    public function getAmountElement() : ElementInterface
    {
        $this->initialiseElements();
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
     */
    public function setCurrencyElementSpec(array $spec) : void
    {
        $this->currencyElementSpec = $spec;
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
     */
    public function setAmountElementSpec(array $spec) : void
    {
        $this->amountElementSpec = $spec;
    }

    public function setLocale(?string $locale = null) : void
    {
        $this->locale = $locale;
    }

    public function getLocale() : string
    {
        if (null === $this->locale) {
            return Locale::getDefault();
        }

        return $this->locale;
    }

    public function setDefaultCurrencyCode(string $code) : void
    {
        $this->options['default_currency'] = $code;
        $this->currencyElementSpec['attributes']['value'] = $code;
    }

    public function getDefaultCurrencyCode() :? string
    {
        if (isset($this->options['default_currency'])) {
            return $this->options['default_currency'];
        }

        return null;
    }

    /**
     * Set a minimum amount with an optional error message
     * @param float|int $min
     * @param bool $inclusive
     * @param string $message
     */
    public function setMinimumAmount($min, bool $inclusive = false, ?string $message = null) : void
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
    public function setMaximumAmount($max, bool $inclusive = false, ?string $message = null) : void
    {
        $this->maximumOptions = [
            'max' => $max,
            'inclusive' => $inclusive,
            'message' => $message,
        ];
    }
}
