<?php

namespace NetglueMoney\Form\Element;

use Zend\Form\Element\Select;
use NetglueMoney\Money\Currency;
use Zend\InputFilter\InputProviderInterface;
use Zend\Validator\ValidatorInterface;
use NetglueMoney\Validator\CurrencyCode as CurrencyValidator;
use NetglueMoney\Service\CurrencyList;
use Zend\Stdlib\ArrayUtils;

class SelectCurrency extends Select implements InputProviderInterface
{

    /**
     * Configured List of allowed currencies
     * @var CurrencyList
     */
    protected $currencyList;

    /**
     * Default Options
     * @var array
     */
    protected $options = array(
        'displayNames' => false,
    );

    /**
     * User defined or default validator
     * @var ValidatorInterface
     */
    protected $validator;

    /**
     * @return array
     */
    public function getValueOptions()
    {
        if (!count($this->valueOptions)) {
            $names = Currency::getAvailableCurrencyNames();
            $codes = $this->getCurrencyList()->getAllow();
            $options = parent::getValueOptions();
            foreach ($codes as $code) {
                $name = $this->getDisplayNames() ? $names[$code] : $code;
                $options[$code] = $name;
            }
            $this->setValueOptions($options);
        }

        return parent::getValueOptions();
    }

    /**
     * Return input filter spec
     * @return array
     */
    public function getInputSpecification()
    {
        return array(
            'name' => $this->getName(),
            'required' => true,
            'filters' => array(
                array('name' => 'Zend\Filter\StringTrim'),
                array('name' => 'Zend\Filter\StringToUpper'),
            ),
            'validators' => array(
                $this->getValidator(),
            ),
        );
    }

    /**
     * {@inheritDoc}
     */
    public function setValue($value)
    {
        $multiple = $this->getAttribute('multiple');

        if (true === $multiple || 'multiple' === $multiple) {
            if ($value instanceof \Traversable) {
                $value = ArrayUtils::iteratorToArray($value);
            } elseif ($value == null) {
                return parent::setValue(array());
            } elseif (!is_array($value)) {
                $value = (array) $value;
            }

            return parent::setValue(array_map(array($this, 'makeCurrency'), $value));
        }

        return parent::setValue($this->makeCurrency($value));
    }

    /**
     * Make a currency object with the given code or return null if the code is empty/invalid
     * @param  string        $value
     * @return Currency|NULL
     */
    public function makeCurrency($code)
    {
        if (is_string($code)) {
            try {
                return new Currency($code);
            } catch (\NetglueMoney\Exception\ExceptionInterface $e) {

            }
        }

        return NULL;
    }

    /**
     * Set Option whether to display names or codes
     * @param  bool $flag
     * @return self
     */
    public function setDisplayNames($flag)
    {
        $this->setOption('displayNames', (bool) $flag);

        return $this;
    }

    /**
     * Return display names option
     * @return bool
     */
    public function getDisplayNames()
    {
        return $this->getOption('displayNames');
    }

    /**
     * Set validator to return with input spec
     * @param  ValidatorInterface $validator
     * @return self
     */
    public function setValidator(ValidatorInterface $validator)
    {
        $this->validator = $validator;

        return $this;
    }

    /**
     * Return a default validator if noneset
     * @return ValidatorInterface
     */
    public function getValidator()
    {
        if (NULL === $this->validator) {
            $this->validator = new CurrencyValidator;
        }

        return $this->validator;
    }

    /**
     * Set Currency list to check allowed currencies against
     * @param  CurrencyList $list
     * @return self
     */
    public function setCurrencyList(CurrencyList $list)
    {
        $this->currencyList = $list;

        return $this;
    }

    /**
     * Return the currency list for checking allowed currencies
     *
     * Lazy loads one if none set
     * @return CurrencyList
     */
    public function getCurrencyList()
    {
        if (!$this->currencyList) {
            $this->setCurrencyList(new CurrencyList);
        }

        return $this->currencyList;
    }

}
