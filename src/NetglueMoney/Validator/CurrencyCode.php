<?php
declare(strict_types=1);

namespace NetglueMoney\Validator;

use NetglueMoney\Service\CurrencyList;
use Zend\Validator\AbstractValidator;
use function is_string;
use function preg_match;

class CurrencyCode extends AbstractValidator
{
    private const PATTERN = '/^[A-Z]{3}$/';

    public const INVALID = 'notString';
    public const NOT_MATCH = 'noMatch';
    public const NOT_FOUND = 'notFound';

    /**
     * Configured List of allowed currencies
     * @var CurrencyList
     */
    protected $currencyList;

    /**
     * Error Message Templates
     * @var array
     */
    protected $messageTemplates = [
        self::INVALID => 'Invalid type given. String expected',
        self::NOT_MATCH => 'Invalid currency code value. 3 uppercase letters expected',
        self::NOT_FOUND => 'The currency code provided does not match any known or allowed currency codes',
    ];

    public function __construct(CurrencyList $list, $options = null)
    {
        parent::__construct($options);
        $this->currencyList = $list;
    }

    /**
     * Whether the value is valid
     * @param  mixed $value
     * @return bool
     */
    public function isValid($value) : bool
    {
        if (! is_string($value)) {
            $this->error(self::INVALID);
            return false;
        }

        $this->setValue($value);

        if (! preg_match(self::PATTERN, $value)) {
            $this->error(self::NOT_MATCH);
            return false;
        }

        if (! $this->currencyList->isAllowed($value)) {
            $this->error(self::NOT_FOUND);
            return false;
        }

        return true;
    }
}
