<?php
declare(strict_types=1);

namespace NetglueMoneyTest\Hydrator;

use NetglueMoney\Hydrator\MoneyHydrator;
use NetglueMoney\Money\Money;
use NetglueMoney\Money\Currency;
use NetglueMoneyTest\Framework\TestCase;

class MoneyHydratorTest extends TestCase
{

    public function testExtract()
    {
        $money = new Money(100, new Currency('GBP'));
        $hydrator = new MoneyHydrator;
        $data = $hydrator->extract($money);

        $this->assertInternalType('array', $data);
        $this->assertSame([
            'amount' => 1.0,
            'currency' => 'GBP',
        ], $data);
    }

    /**
     * @expectedException \NetglueMoney\Exception\InvalidArgumentException
     */
    public function testExtractThrowsException()
    {
        $hydrator = new MoneyHydrator;
        $hydrator->extract(new \stdClass);
    }

    public function testHydrate()
    {
        $money = new Money(100, new Currency('GBP'));
        $hydrator = new MoneyHydrator;

        $data = [
            'amount' => 100,
            'currency' => 'GBP',
        ];

        /**
         * Money object is immutable so we can only use the returned
         * instance.
         */

        $money = $hydrator->hydrate($data, $money);

        $this->assertSame(10000, $money->getAmount());
        $this->assertSame('GBP', $money->getCurrencyCode());
    }
}
