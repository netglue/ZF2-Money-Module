<?php
declare(strict_types=1);

namespace NetglueMoney\Hydrator;

use Zend\Hydrator\HydratorInterface;

use NetglueMoney\Money\Money;
use NetglueMoney\Money\Currency;
use NetglueMoney\Exception;

class MoneyHydrator implements HydratorInterface
{

    /**
     * Extract a money object into an array converting the integer amount to a float
     * @param  Money                              $object
     * @return array
     * @throws Exception\InvalidArgumentException
     */
    public function extract($object)
    {
        if (! $object instanceof Money) {
            throw new Exception\InvalidArgumentException(sprintf(
                '%s expects a Money instance',
                __METHOD__
            ));
        }
        $subUnit = $object->getCurrency()->getSubUnit();
        $amount = (float) ($object->getAmount() / $subUnit);

        return [
            'amount' => $amount,
            'currency' => $object->getCurrencyCode(),
        ];
    }

    /**
     * Hydrate an array to a money object
     *
     * This hydrator breaks the hydration pattern as it returns a new instance
     * As money objects are immutable, the object given in $object cannot be changed
     *
     * @param  array $data
     * @param  mixed $object
     * @return Money
     */
    public function hydrate(array $data, $object)
    {
        $currency = new Currency($data['currency']);
        $amount = (int) ($data['amount'] * $currency->getSubUnit());
        $object = new Money($amount, $currency);

        return $object;
    }
}
