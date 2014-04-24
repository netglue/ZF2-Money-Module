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

class MoneyElement extends FormElement implements
    FormFactoryAwareInterface,
    InputProviderInterface
{

    /**
     * Element Attributes
     * @var array
     */
    protected $attributes = array(

    );

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
     * Return Currency code
     * @return string|NULL
     */
    public function getCurrencyCode()
    {
        return $this->getCurrencyElement()->getValue();
    }

    /**
     * Return amount
     * @return int|float|string
     */
    public function getAmount()
    {
        return $this->getAmountElement()->getValue();
    }

    /**
     * Set currency code
     * @param string $code
     * @return self
     */
    public function setCurrencyCode($code)
    {
        $this->getCurrencyElement()->setValue($code);
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
        $this->getAmountElement()->setValue($amount);
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
        try{
            $currency = new Currency($this->getCurrencyCode());
            $amount = (int) ($this->getAmount() * $currency->getSubUnit());
            return new Money($amount, $currency);
        } catch(\NetglueMoney\Exception\ExceptionInterface $e) {
            return NULL;
        }
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
            $this->currencyElement->setName($this->getName().'[code]');
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
            $this->amountElement->setName($this->getName().'[amount]');
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
                array(
                    'name' => 'NetglueMoney\Validator\CompositeMoneyValidator',
                ),
            ),
		);
	}

}
