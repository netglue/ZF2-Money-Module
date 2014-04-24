<?php
namespace NetglueMoney\Form\Element;

use NetglueMoney\Money\Money;
use NetglueMoney\Money\Currency;
use NetglueMoney\Validator\CompositeMoneyValidator;

use Zend\Form\Element as FormElement;
use Zend\Form\Exception as FormException;
use Zend\Form\Factory as FormFactory;
use Zend\Form\FormFactoryAwareInterface;
use Zend\InputFilter\InputProviderInterface;

use Zend\Validator\ValidatorInterface;
use Zend\Validator\ValidatorPluginManager;
use Zend\Validator\ValidatorPluginManagerAwareInterface;

class MoneyElement extends FormElement implements
    FormFactoryAwareInterface,
    InputProviderInterface,
    ValidatorPluginManagerAwareInterface
{

    /**
     * Element Attributes
     * @var array
     */
    protected $attributes = array(

    );

    /**
     * Currency Code Value
     * @var string
     */
    protected $currencyCode;

    /**
     * Floating Point Number or Int
     * @var string
     */
    protected $amount;

    /**
     * Currency Code Element
     * @var \Zend\Form\Element\Text|NULL
     */
    protected $currencyElement;

    /**
     * Currency Code Element Specification
     * @var array
     */
    protected $currencyElementSpec = array(
        'name' => 'code',
        'type' => 'text',
        'options' => array(

        ),
        'attributes' => array(
            'maxlength' => 3,
            'required' => true,
        ),
    );

    /**
     * Amount Element
     * @var \Zend\Form\Element\Text|NULL
     */
    protected $amountElement;

    /**
     * Amount Element Specification
     * @var array
     */
    protected $amountElementSpec = array(
        'name' => 'amount',
        'type' => 'text',
        'options' => array(

        ),
        'attributes' => array(
            'required' => true,
        ),
    );

    /**
     * Form Factory
     * @var FormFactory
     */
    protected $factory;

    /**
     * Custom Validator
     * @var NetglueMoney\Validator\CompositeMoneyValidator
     */
    protected $validator;

    protected $validatorManager;

    /**
     * Return Currency code
     * @return string|NULL
     */
    public function getCurrencyCode()
    {
        return $this->currencyCode;
    }

    /**
     * Return amount
     * @return int|float|string
     */
    public function getAmount()
    {
        return $this->amount;
    }

    /**
     * Set currency code
     * @param string $code
     * @return self
     */
    public function setCurrencyCode($code)
    {
        $code = trim(strtoupper($code));
        $this->currencyCode = empty($code) ? NULL : $code;
        $this->getCurrencyElement()->setValue($this->currencyCode);
        return $this;
    }

    /**
     * Set Amount
     * @param float|int|string $amount
     * @return self
     * @throws FormException\InvalidArgumentException
     */
    public function setAmount($amount)
    {
        if(!is_numeric($amount)) {
            throw new FormException\InvalidArgumentException(sprintf(
                '%s expects a numeric argument. received %s',
                __FUNCTION__,
                gettype($amount)
            ));
        }
        $this->amount = (float) $amount;
        $this->getAmountElement()->setValue($this->amount);
        return $this;
    }

    /**
     * Set the value
     * @param Money|array $value
     * @return self
     * @throws FormException\InvalidArgumentException
     */
    public function setValue($value)
    {
        if($value instanceof Money) {
            $currency = $value->getCurrency();
            $code = $currency->getCurrencyCode();
            $amount = ($value->getAmount() / $currency->getSubUnit());

            $this->setCurrencyCode($code);
            $this->setAmount($amount);
            return parent::setValue($value);
        }
        if(!is_array($value)) {
            throw new FormException\InvalidArgumentException(sprintf(
                '%s expects an array to set value. Received %s',
                __FUNCTION__,
                gettype($value)
            ));
        }
        $this->setAmount($value['amount'])
            ->setCurrencyCode($value['code']);
        return parent::setValue($this->createMoney());
    }

    /**
     * Return the current value as a money instance
     * @return Money
     */
    protected function createMoney()
    {
        $currency = new Currency($this->getCurrencyCode());
        $amount = (int) ($this->getAmount() * $currency->getSubUnit());
        return new Money($amount, $currency);
    }

    /**
     * Set Form Factory
     * @param FormFactory $factory
     * @return self
     */
    public function setFormFactory(FormFactory $factory) {
        $this->factory = $factory;
        return $this;
    }

    /**
     * Return form factory
     * Lazy loads a fresh one as required
     * @return FormFactory|NULL
     */
    public function getFormFactory()
    {
        if(!$this->factory) {
            $this->setFormFactory(new FormFactory);
        }
        return $this->factory;
    }

    /**
     * Return the currency element
     * @return \Zend\Form\ElementInterface
     */
    public function getCurrencyElement()
    {
        if(!$this->currencyElement) {
            $this->currencyElement = $this->getFormFactory()->createElement($this->getCurrencyElementSpec());
        }
        return $this->currencyElement;
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
     * Return the currency element
     * @return \Zend\Form\ElementInterface
     */
    public function getAmountElement()
    {
        if(!$this->amountElement) {
            $this->amountElement = $this->getFormFactory()->createElement($this->getAmountElementSpec());
        }
        return $this->amountElement;
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
	 * Return input filter spec
	 * @return array
	 */
	public function getInputSpecification()
	{
		return array(
            'name' => $this->getName(),
            'required' => true,
            'filters' => array(

            ),
            'validators' => array(
                //$this->getValidator(),
                array(
                    'name' => 'NetglueMoney\Validator\CompositeMoneyValidator',
                ),
            ),
		);
	}

    /**
     * Set validator to return with input spec
     * @param ValidatorInterface $validator
     * @return self
     */
    public function setValidator(ValidatorInterface $validator)
    {
        $this->validator = $validator;
        return $this;
    }

    /**
     * Return a default validator if noneset
     * @return CompositeMoneyValidator|ValidatorInterface
     */
    public function getValidator()
    {
        if(NULL === $this->validator) {
            $this->validator = new CompositeMoneyValidator;
        }
        return $this->validator;
    }

    /**
     * Set validator plugin manager
     *
     * @param ValidatorPluginManager $pluginManager
     */
    public function setValidatorPluginManager(ValidatorPluginManager $pluginManager)
    {
        $this->validatorManager = $pluginManager;
        return $this;
    }

    /**
     * Get validator plugin manager
     *
     * @return ValidatorPluginManager
     */
    public function getValidatorPluginManager()
    {
        return $this->validatorManager;
    }
}
