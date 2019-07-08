<?php
declare(strict_types=1);

namespace NetglueMoneyTest\Hydrator;

use NetglueMoney\Hydrator\MoneyHydrator;
use NetglueMoney\Money\Currency;
use NetglueMoney\Money\Money;
use NetglueMoneyTest\Framework\TestCase;
use stdClass;

class MoneyHydratorTest extends TestCase
{

    public function testExtract() : void
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
    public function testExtractThrowsException() : void
    {
        $hydrator = new MoneyHydrator;
        $hydrator->extract(new stdClass);
    }

    public function testHydrate() : void
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
