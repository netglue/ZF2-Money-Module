<?php
namespace NetglueMoney\Validator;


use NetglueMoney\Money\Money;

use Zend\Validator\AbstractValidator;
use Zend\Validator\ValidatorInterface;

class CompositeMoneyValidator extends AbstractValidator
{

    const INVALID = 'invalid';
    const INVALID_CURRENCY = 'invalidCurrency';
    const INVALID_NUMBER = 'invalidNumber';

    /**
	 * Error Message Templates
	 * @var array
	 */
	protected $messageTemplates = array(
		self::INVALID => "Expected a money instance or an array containing 'code' and 'amount' keys",
		self::INVALID_CURRENCY => "Invalid currency code value. 3 uppercase letters expected",
		self::INVALID_NUMBER => "The amount should be an integer or floating point number",
	);

    protected $currencyValidator;

    /**
	 * Whether the value is valid
	 * @param mixed $value
	 * @return bool
	 */
    public function isValid($value)
    {
        if($value instanceof Money) {
            return true;
        }
        if(!is_array($value)) {
            $this->error(self::INVALID);
			return false;
        }
        if(!array_key_exists('code') || !array_key_exists('amount')) {
            $this->error(self::INVALID);
			return false;
        }

        if(!$this->getCurrencyValidator()->isValid($value['code'])) {
            $this->error(self::INVALID_CURRENCY);
        }
        if(!is_numeric($value['amount'])) {
            $this->error(self::INVALID_NUMBER);
        }
        return count($this->getMessages()) === 0;
    }

    /**
     * @return CurrencyCode
     */
    public function getCurrencyValidator()
    {
        if(!$this->currencyValidator) {
            $this->currencyValidator = new CurrencyCode;
        }
        return $this->currencyValidator;
    }

    public function setCurrencyValidator(ValidatorInterface $validator)
    {
        $this->currencyValidator = $validator;
        return $this;
    }

}
