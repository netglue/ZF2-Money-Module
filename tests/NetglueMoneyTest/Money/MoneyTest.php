<?php
declare(strict_types=1);
/**
 * Money
 *
 * Copyright (c) 2012-2014, Sebastian Bergmann <sebastian@phpunit.de>.
 * All rights reserved.
 *
 * Redistribution and use in source and binary forms, with or without
 * modification, are permitted provided that the following conditions
 * are met:
 *
 *   * Redistributions of source code must retain the above copyright
 *     notice, this list of conditions and the following disclaimer.
 *
 *   * Redistributions in binary form must reproduce the above copyright
 *     notice, this list of conditions and the following disclaimer in
 *     the documentation and/or other materials provided with the
 *     distribution.
 *
 *   * Neither the name of Sebastian Bergmann nor the names of his
 *     contributors may be used to endorse or promote products derived
 *     from this software without specific prior written permission.
 *
 * THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS
 * "AS IS" AND ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT
 * LIMITED TO, THE IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS
 * FOR A PARTICULAR PURPOSE ARE DISCLAIMED. IN NO EVENT SHALL THE
 * COPYRIGHT OWNER OR CONTRIBUTORS BE LIABLE FOR ANY DIRECT, INDIRECT,
 * INCIDENTAL, SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES (INCLUDING,
 * BUT NOT LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES;
 * LOSS OF USE, DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER
 * CAUSED AND ON ANY THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT
 * LIABILITY, OR TORT (INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN
 * ANY WAY OUT OF THE USE OF THIS SOFTWARE, EVEN IF ADVISED OF THE
 * POSSIBILITY OF SUCH DAMAGE.
 *
 * @package    Money
 * @author     Sebastian Bergmann <sebastian@phpunit.de>
 * @copyright  2012-2014 Sebastian Bergmann <sebastian@phpunit.de>
 * @license    http://www.opensource.org/licenses/BSD-3-Clause  The BSD 3-Clause License
 * @link       http://www.github.com/sebastianbergmann/money
 */
namespace NetglueMoneyTest\Money;

use NetglueMoney\Exception\CurrencyMismatchException;
use NetglueMoney\Exception\InvalidArgumentException;
use NetglueMoney\Exception\OverflowException;
use NetglueMoney\Money\Currency;
use NetglueMoney\Money\Money;
use NetglueMoneyTest\Framework\TestCase;

class MoneyTest extends TestCase
{
    public function testExceptionIsRaisedForInvalidConstructorArguments2() : void
    {
        $this->expectException(InvalidArgumentException::class);
        new Money(0, null);
    }

    public function testObjectCanBeConstructedForValidConstructorArguments() : Money
    {
        $m = new Money(0, new Currency('EUR'));

        $this->assertInstanceOf(Money::class, $m);

        return $m;
    }

    public function testObjectCanBeConstructedForValidConstructorArguments2() : Money
    {
        $m = new Money(0, 'EUR');

        $this->assertInstanceOf(Money::class, $m);

        return $m;
    }

    public function testObjectCanBeConstructedFromStringValue() : void
    {
        $this->assertEquals(
            new Money(1234, new Currency('EUR')),
            Money::fromString('12.34', new Currency('EUR'))
        );
    }

    public function testObjectCanBeConstructedFromStringValue2() : void
    {
        $this->assertEquals(
            new Money(1234, new Currency('EUR')),
            Money::fromString('12.34', 'EUR')
        );
    }

    /**
     * @depends testObjectCanBeConstructedForValidConstructorArguments
     */
    public function testAmountCanBeRetrieved(Money $m) : void
    {
        $this->assertEquals(0, $m->getAmount());
    }

    /**
     * @depends testObjectCanBeConstructedForValidConstructorArguments
     */
    public function testCurrencyCanBeRetrieved(Money $m) : void
    {
        $this->assertEquals(new Currency('EUR'), $m->getCurrency());
    }

    /**
     * @depends testObjectCanBeConstructedForValidConstructorArguments
     */
    public function testCurrencyCodeCanBeRetrieved(Money $m) : void
    {
        $this->assertEquals('EUR', $m->getCurrencyCode());
    }

    public function testAnotherMoneyObjectWithSameCurrencyCanBeAdded() : void
    {
        $a = new Money(1, new Currency('EUR'));
        $b = new Money(2, new Currency('EUR'));
        $c = $a->add($b);

        $this->assertEquals(1, $a->getAmount());
        $this->assertEquals(2, $b->getAmount());
        $this->assertEquals(3, $c->getAmount());
    }

    public function testExceptionIsThrownForOverflowingAddition() : void
    {
        $a = new Money(PHP_INT_MAX, new Currency('EUR'));
        $b = new Money(2, new Currency('EUR'));
        $this->expectException(OverflowException::class);
        $a->add($b);
    }

    public function testExceptionIsRaisedForIntegerOverflow() : void
    {
        $a = new Money(PHP_INT_MAX, new Currency('EUR'));
        $this->expectException(OverflowException::class);
        $a->multiply(2);
    }

    public function testExceptionIsRaisedWhenMoneyObjectWithDifferentCurrencyIsAdded() : void
    {
        $a = new Money(1, new Currency('EUR'));
        $b = new Money(2, new Currency('USD'));
        $this->expectException(CurrencyMismatchException::class);
        $a->add($b);
    }

    public function testAnotherMoneyObjectWithSameCurrencyCanBeSubtracted() : void
    {
        $a = new Money(1, new Currency('EUR'));
        $b = new Money(2, new Currency('EUR'));
        $c = $b->subtract($a);

        $this->assertEquals(1, $a->getAmount());
        $this->assertEquals(2, $b->getAmount());
        $this->assertEquals(1, $c->getAmount());
    }

    /**
     * @expectedException OverflowException
     */
    public function testExceptionIsThrownForOverflowingSubtraction() : void
    {
        $a = new Money(-PHP_INT_MAX, new Currency('EUR'));
        $b = new Money(2, new Currency('EUR'));
        $a->subtract($b);
    }

    public function testExceptionIsRaisedWhenMoneyObjectWithDifferentCurrencyIsSubtracted() : void
    {
        $a = new Money(1, new Currency('EUR'));
        $b = new Money(2, new Currency('USD'));
        $this->expectException(CurrencyMismatchException::class);
        $b->subtract($a);
    }

    public function testCanBeNegated() : void
    {
        $a = new Money(1, new Currency('EUR'));
        $b = $a->negate();

        $this->assertEquals(1, $a->getAmount());
        $this->assertEquals(-1, $b->getAmount());
    }

    public function testCanBeMultipliedByAFactor() : void
    {
        $a = new Money(1, new Currency('EUR'));
        $b = $a->multiply(2);

        $this->assertEquals(1, $a->getAmount());
        $this->assertEquals(2, $b->getAmount());
    }

    /**
     * @expectedException InvalidArgumentException
     */
    public function testExceptionIsRaisedWhenMultipliedUsingInvalidRoundingMode() : void
    {
        $a = new Money(1, new Currency('EUR'));
        $a->multiply(2, 123456789);
    }

    public function testCanBeAllocatedToNumberOfTargets() : void
    {
        $a = new Money(99, new Currency('EUR'));
        $r = $a->allocateToTargets(10);

        $this->assertEquals(
            [
                new Money(10, new Currency('EUR')),
                new Money(10, new Currency('EUR')),
                new Money(10, new Currency('EUR')),
                new Money(10, new Currency('EUR')),
                new Money(10, new Currency('EUR')),
                new Money(10, new Currency('EUR')),
                new Money(10, new Currency('EUR')),
                new Money(10, new Currency('EUR')),
                new Money(10, new Currency('EUR')),
                new Money(9, new Currency('EUR')),
            ],
            $r
        );
    }

    public function testPercentageCanBeExtracted() : void
    {
        $original = new Money(10000, new Currency('EUR'));
        $extract  = $original->extractPercentage(21);

        $this->assertEquals(new Money(8264, new Currency('EUR')), $extract['subtotal']);
        $this->assertEquals(new Money(1736, new Currency('EUR')), $extract['percentage']);
    }

    public function testCanBeAllocatedByRatios() : void
    {
        $a = new Money(5, new Currency('EUR'));
        $r = $a->allocateByRatios([3, 7]);

        $this->assertEquals(
            [
                new Money(2, new Currency('EUR')),
                new Money(3, new Currency('EUR')),
            ],
            $r
        );
    }

    public function testCanBeComparedToAnotherMoneyObjectWithSameCurrency() : void
    {
        $a = new Money(1, new Currency('EUR'));
        $b = new Money(2, new Currency('EUR'));

        $this->assertEquals(-1, $a->compareTo($b));
        $this->assertEquals(1, $b->compareTo($a));
        $this->assertEquals(0, $a->compareTo($a));
    }

    /**
     * @depends testCanBeComparedToAnotherMoneyObjectWithSameCurrency
     */
    public function testCanBeComparedToAnotherMoneyObjectWithSameCurrency2() : void
    {
        $a = new Money(1, new Currency('EUR'));
        $b = new Money(2, new Currency('EUR'));

        $this->assertFalse($a->greaterThan($b));
        $this->assertTrue($b->greaterThan($a));
    }

    /**
     * @depends testCanBeComparedToAnotherMoneyObjectWithSameCurrency
     */
    public function testCanBeComparedToAnotherMoneyObjectWithSameCurrency3() : void
    {
        $a = new Money(1, new Currency('EUR'));
        $b = new Money(2, new Currency('EUR'));

        $this->assertFalse($b->lessThan($a));
        $this->assertTrue($a->lessThan($b));
    }

    /**
     * @depends testCanBeComparedToAnotherMoneyObjectWithSameCurrency
     */
    public function testCanBeComparedToAnotherMoneyObjectWithSameCurrency4() : void
    {
        $a = new Money(1, new Currency('EUR'));
        $b = new Money(1, new Currency('EUR'));

        $this->assertEquals(0, $a->compareTo($b));
        $this->assertEquals(0, $b->compareTo($a));
        $this->assertTrue($a->equals($b));
        $this->assertTrue($b->equals($a));
    }

    /**
     * @depends testCanBeComparedToAnotherMoneyObjectWithSameCurrency
     */
    public function testCanBeComparedToAnotherMoneyObjectWithSameCurrency5() : void
    {
        $a = new Money(2, new Currency('EUR'));
        $b = new Money(2, new Currency('EUR'));
        $c = new Money(1, new Currency('EUR'));

        $this->assertTrue($a->greaterThanOrEqual($a));
        $this->assertTrue($a->greaterThanOrEqual($b));
        $this->assertTrue($a->greaterThanOrEqual($c));
        $this->assertFalse($c->greaterThanOrEqual($a));
    }

    /**
     * @depends testCanBeComparedToAnotherMoneyObjectWithSameCurrency
     */
    public function testCanBeComparedToAnotherMoneyObjectWithSameCurrency6() : void
    {
        $a = new Money(1, new Currency('EUR'));
        $b = new Money(1, new Currency('EUR'));
        $c = new Money(2, new Currency('EUR'));

        $this->assertTrue($a->lessThanOrEqual($a));
        $this->assertTrue($a->lessThanOrEqual($b));
        $this->assertTrue($a->lessThanOrEqual($c));
        $this->assertFalse($c->lessThanOrEqual($a));
    }

    public function testExceptionIsRaisedWhenComparedToMoneyObjectWithDifferentCurrency() : void
    {
        $a = new Money(1, new Currency('EUR'));
        $b = new Money(2, new Currency('USD'));
        $this->expectException(CurrencyMismatchException::class);
        $a->compareTo($b);
    }
}
