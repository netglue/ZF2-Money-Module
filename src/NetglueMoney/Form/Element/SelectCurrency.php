<?php
declare(strict_types=1);

namespace NetglueMoney\Form\Element;

use NetglueMoney\Exception\ExceptionInterface;
use NetglueMoney\Money\Currency;
use NetglueMoney\Service\CurrencyList;
use NetglueMoney\Validator\CurrencyCode as CurrencyValidator;
use Traversable;
use Zend\Filter;
use Zend\Form\Element\Select;
use Zend\Stdlib\ArrayUtils;
use Zend\Validator\ValidatorInterface;
use function array_map;
use function is_array;
use function is_string;

class SelectCurrency extends Select
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
    protected $options = [
        'displayNames' => false,
    ];

    /**
     * User defined or default validator
     * @var ValidatorInterface
     */
    protected $validator;

    public function __construct(
        CurrencyList $currencyList,
        $name = null,
        $options = []
    ) {
        $this->currencyList = $currencyList;
        parent::__construct($name, $options);
    }

    /**
     * @return array
     */
    public function getValueOptions() : array
    {
        if (! count($this->valueOptions)) {
            $names = Currency::getAvailableCurrencyNames();
            $options = parent::getValueOptions();
            foreach ($this->currencyList->getAllow() as $code) {
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
    public function getInputSpecification() : array
    {
        return [
            'name' => $this->getName(),
            'required' => true,
            'filters' => [
                ['name' => Filter\StringTrim::class],
                ['name' => Filter\StringToUpper::class],
            ],
            'validators' => [
                [
                    'name' => CurrencyValidator::class,
                ],
            ],
        ];
    }

    /**
     * {@inheritDoc}
     */
    public function setValue($value)
    {
        $multiple = $this->getAttribute('multiple');

        if (true === $multiple || 'multiple' === $multiple) {
            if ($value instanceof Traversable) {
                $value = ArrayUtils::iteratorToArray($value);
            } elseif ($value === null) {
                return parent::setValue([]);
            } elseif (! is_array($value)) {
                $value = (array) $value;
            }

            return parent::setValue(array_map([$this, 'makeCurrency'], $value));
        }

        return parent::setValue($this->makeCurrency($value));
    }

    /**
     * Make a currency object with the given code or return null if the code is empty/invalid
     *
     * @param $code
     * @return Currency|null
     */
    public function makeCurrency($code) :? Currency
    {
        if (is_string($code)) {
            try {
                return new Currency($code);
            } catch (ExceptionInterface $e) {
            }
        }

        return null;
    }

    /**
     * Set Option whether to display names or codes
     *
     * @param  bool $flag
     * @return void
     */
    public function setDisplayNames(bool $flag) : void
    {
        $this->setOption('displayNames', $flag);
    }

    /**
     * Return display names option
     * @return bool
     */
    public function getDisplayNames() : bool
    {
        return (bool) $this->getOption('displayNames');
    }
}
