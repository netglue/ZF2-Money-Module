<?php
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

use NetglueMoney\Money\Money;
use NetglueMoney\Money\Currency;
use NetglueMoneyTest\Framework\TestCase;

class MoneyTest extends TestCase
{

    /**
     * @covers            \NetglueMoney\Money\Money::__construct
     * @covers            \NetglueMoney\Money\Money::handleCurrencyArgument
     * @uses              \NetglueMoney\Money\Currency
     * @expectedException \NetglueMoney\Exception\InvalidArgumentException
     */
    public function testExceptionIsRaisedForInvalidConstructorArguments2()
    {
        new Money(0, null);
    }

    /**
     * @covers \NetglueMoney\Money\Money::__construct
     * @covers \NetglueMoney\Money\Money::handleCurrencyArgument
     * @uses   \NetglueMoney\Money\Currency
     */
    public function testObjectCanBeConstructedForValidConstructorArguments()
    {
        $m = new Money(0, new Currency('EUR'));

        $this->assertInstanceOf('NetglueMoney\\Money\\Money', $m);

        return $m;
    }

    /**
     * @covers \NetglueMoney\Money\Money::__construct
     * @covers \NetglueMoney\Money\Money::handleCurrencyArgument
     * @uses   \NetglueMoney\Money\Currency
     */
    public function testObjectCanBeConstructedForValidConstructorArguments2()
    {
        $m = new Money(0, 'EUR');

        $this->assertInstanceOf('NetglueMoney\\Money\\Money', $m);

        return $m;
    }

    /**
     * @covers \NetglueMoney\Money\Money::fromString
     * @uses   \NetglueMoney\Money\Money::__construct
     * @uses   \NetglueMoney\Money\Money::handleCurrencyArgument
     * @uses   \NetglueMoney\Money\Currency
     */
    public function testObjectCanBeConstructedFromStringValue()
    {
        $this->assertEquals(
            new Money(1234, new Currency('EUR')),
            Money::fromString('12.34', new Currency('EUR'))
        );
    }

    /**
     * @covers \NetglueMoney\Money\Money::fromString
     * @uses   \NetglueMoney\Money\Money::__construct
     * @uses   \NetglueMoney\Money\Money::handleCurrencyArgument
     * @uses   \NetglueMoney\Money\Currency
     */
    public function testObjectCanBeConstructedFromStringValue2()
    {
        $this->assertEquals(
            new Money(1234, new Currency('EUR')),
            Money::fromString('12.34', 'EUR')
        );
    }

    /**
     * @covers  \NetglueMoney\Money\Money::getAmount
     * @depends testObjectCanBeConstructedForValidConstructorArguments
     */
    public function testAmountCanBeRetrieved(Money $m)
    {
        $this->assertEquals(0, $m->getAmount());
    }

    /**
     * @covers  \NetglueMoney\Money\Money::getCurrency
     * @uses    \NetglueMoney\Money\Currency
     * @depends testObjectCanBeConstructedForValidConstructorArguments
     */
    public function testCurrencyCanBeRetrieved(Money $m)
    {
        $this->assertEquals(new Currency('EUR'), $m->getCurrency());
    }

    /**
     * @covers  \NetglueMoney\Money\Money::getCurrencyCode
     * @uses    \NetglueMoney\Money\Currency
     * @depends testObjectCanBeConstructedForValidConstructorArguments
     */
    public function testCurrencyCodeCanBeRetrieved(Money $m)
    {
        $this->assertEquals('EUR', $m->getCurrencyCode());
    }

    /**
     * @covers \NetglueMoney\Money\Money::add
     * @covers \NetglueMoney\Money\Money::newMoney
     * @covers \NetglueMoney\Money\Money::assertSameCurrency
     * @uses   \NetglueMoney\Money\Money::__construct
     * @uses   \NetglueMoney\Money\Money::handleCurrencyArgument
     * @uses   \NetglueMoney\Money\Money::getAmount
     * @uses   \NetglueMoney\Money\Money::getCurrency
     * @uses   \NetglueMoney\Money\Money::assertIsInteger
     * @uses   \NetglueMoney\Money\Currency
     */
    public function testAnotherMoneyObjectWithSameCurrencyCanBeAdded()
    {
        $a = new Money(1, new Currency('EUR'));
        $b = new Money(2, new Currency('EUR'));
        $c = $a->add($b);

        $this->assertEquals(1, $a->getAmount());
        $this->assertEquals(2, $b->getAmount());
        $this->assertEquals(3, $c->getAmount());
    }

    /**
     * @covers            \NetglueMoney\Money\Money::add
     * @covers            \NetglueMoney\Money\Money::newMoney
     * @covers            \NetglueMoney\Money\Money::assertSameCurrency
     * @covers            \NetglueMoney\Money\Money::assertIsInteger
     * @uses              \NetglueMoney\Money\Money::__construct
     * @uses              \NetglueMoney\Money\Money::handleCurrencyArgument
     * @uses              \NetglueMoney\Money\Money::getAmount
     * @uses              \NetglueMoney\Money\Money::getCurrency
     * @uses              \NetglueMoney\Money\Currency
     * @expectedException \NetglueMoney\Exception\OverflowException
     */
    public function testExceptionIsThrownForOverflowingAddition()
    {
        $a = new Money(PHP_INT_MAX, new Currency('EUR'));
        $b = new Money(2, new Currency('EUR'));
        $a->add($b);
    }

    /**
     * @covers            \NetglueMoney\Money\Money::assertInsideIntegerBounds
     * @uses              \NetglueMoney\Money\Money::__construct
     * @uses              \NetglueMoney\Money\Money::handleCurrencyArgument
     * @uses              \NetglueMoney\Money\Money::multiply
     * @uses              \NetglueMoney\Money\Money::castToInt
     * @uses              \NetglueMoney\Money\Currency
     * @expectedException \NetglueMoney\Exception\OverflowException
     */
    public function testExceptionIsRaisedForIntegerOverflow()
    {
        $a = new Money(PHP_INT_MAX, new Currency('EUR'));
        $a->multiply(2);
    }

    /**
     * @covers            \NetglueMoney\Money\Money::add
     * @covers            \NetglueMoney\Money\Money::assertSameCurrency
     * @uses              \NetglueMoney\Money\Money::__construct
     * @uses              \NetglueMoney\Money\Money::handleCurrencyArgument
     * @uses              \NetglueMoney\Money\Money::getAmount
     * @uses              \NetglueMoney\Money\Money::getCurrency
     * @uses              \NetglueMoney\Money\Currency
     * @expectedException \NetglueMoney\Exception\CurrencyMismatchException
     */
    public function testExceptionIsRaisedWhenMoneyObjectWithDifferentCurrencyIsAdded()
    {
        $a = new Money(1, new Currency('EUR'));
        $b = new Money(2, new Currency('USD'));

        $a->add($b);
    }

    /**
     * @covers \NetglueMoney\Money\Money::subtract
     * @covers \NetglueMoney\Money\Money::newMoney
     * @covers \NetglueMoney\Money\Money::assertSameCurrency
     * @uses   \NetglueMoney\Money\Money::__construct
     * @uses   \NetglueMoney\Money\Money::handleCurrencyArgument
     * @uses   \NetglueMoney\Money\Money::getAmount
     * @uses   \NetglueMoney\Money\Money::getCurrency
     * @uses   \NetglueMoney\Money\Money::assertIsInteger
     * @uses   \NetglueMoney\Money\Currency
     */
    public function testAnotherMoneyObjectWithSameCurrencyCanBeSubtracted()
    {
        $a = new Money(1, new Currency('EUR'));
        $b = new Money(2, new Currency('EUR'));
        $c = $b->subtract($a);

        $this->assertEquals(1, $a->getAmount());
        $this->assertEquals(2, $b->getAmount());
        $this->assertEquals(1, $c->getAmount());
    }

    /**
     * @covers            \NetglueMoney\Money\Money::subtract
     * @covers            \NetglueMoney\Money\Money::newMoney
     * @covers            \NetglueMoney\Money\Money::assertSameCurrency
     * @covers            \NetglueMoney\Money\Money::assertIsInteger
     * @uses              \NetglueMoney\Money\Money::__construct
     * @uses              \NetglueMoney\Money\Money::handleCurrencyArgument
     * @uses              \NetglueMoney\Money\Money::getAmount
     * @uses              \NetglueMoney\Money\Money::getCurrency
     * @uses              \NetglueMoney\Money\Currency
     * @expectedException \NetglueMoney\Exception\OverflowException
     */
    public function testExceptionIsThrownForOverflowingSubtraction()
    {
        $a = new Money(-PHP_INT_MAX, new Currency('EUR'));
        $b = new Money(2, new Currency('EUR'));
        $a->subtract($b);
    }

    /**
     * @covers            \NetglueMoney\Money\Money::subtract
     * @covers            \NetglueMoney\Money\Money::assertSameCurrency
     * @uses              \NetglueMoney\Money\Money::__construct
     * @uses              \NetglueMoney\Money\Money::handleCurrencyArgument
     * @uses              \NetglueMoney\Money\Money::getAmount
     * @uses              \NetglueMoney\Money\Money::getCurrency
     * @uses              \NetglueMoney\Money\Currency
     * @expectedException \NetglueMoney\Exception\CurrencyMismatchException
     */
    public function testExceptionIsRaisedWhenMoneyObjectWithDifferentCurrencyIsSubtracted()
    {
        $a = new Money(1, new Currency('EUR'));
        $b = new Money(2, new Currency('USD'));

        $b->subtract($a);
    }

    /**
     * @covers \NetglueMoney\Money\Money::negate
     * @covers \NetglueMoney\Money\Money::newMoney
     * @uses   \NetglueMoney\Money\Money::__construct
     * @uses   \NetglueMoney\Money\Money::handleCurrencyArgument
     * @uses   \NetglueMoney\Money\Money::getAmount
     * @uses   \NetglueMoney\Money\Currency
     */
    public function testCanBeNegated()
    {
        $a = new Money(1, new Currency('EUR'));
        $b = $a->negate();

        $this->assertEquals(1, $a->getAmount());
        $this->assertEquals(-1, $b->getAmount());
    }

    /**
     * @covers \NetglueMoney\Money\Money::multiply
     * @covers \NetglueMoney\Money\Money::newMoney
     * @covers \NetglueMoney\Money\Money::castToInt
     * @uses   \NetglueMoney\Money\Money::__construct
     * @uses   \NetglueMoney\Money\Money::handleCurrencyArgument
     * @uses   \NetglueMoney\Money\Money::getAmount
     * @uses   \NetglueMoney\Money\Money::assertInsideIntegerBounds
     * @uses   \NetglueMoney\Money\Currency
     */
    public function testCanBeMultipliedByAFactor()
    {
        $a = new Money(1, new Currency('EUR'));
        $b = $a->multiply(2);

        $this->assertEquals(1, $a->getAmount());
        $this->assertEquals(2, $b->getAmount());
    }

    /**
     * @covers            \NetglueMoney\Money\Money::multiply
     * @uses              \NetglueMoney\Money\Money::__construct
     * @uses              \NetglueMoney\Money\Money::handleCurrencyArgument
     * @uses              \NetglueMoney\Money\Currency
     * @expectedException \NetglueMoney\Exception\InvalidArgumentException
     */
    public function testExceptionIsRaisedWhenMultipliedUsingInvalidRoundingMode()
    {
        $a = new Money(1, new Currency('EUR'));
        $a->multiply(2, null);
    }

    /**
     * @covers \NetglueMoney\Money\Money::allocateToTargets
     * @covers \NetglueMoney\Money\Money::newMoney
     * @uses   \NetglueMoney\Money\Money::__construct
     * @uses   \NetglueMoney\Money\Money::handleCurrencyArgument
     * @uses   \NetglueMoney\Money\Money::getAmount
     * @uses   \NetglueMoney\Money\Currency
     */
    public function testCanBeAllocatedToNumberOfTargets()
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
                new Money(9, new Currency('EUR'))
            ],
            $r
        );
    }

    /**
     * @covers \NetglueMoney\Money\Money::extractPercentage
     * @uses   \NetglueMoney\Money\Money::__construct
     * @uses   \NetglueMoney\Money\Money::getAmount
     * @uses   \NetglueMoney\Money\Money::getCurrency
     * @uses   \NetglueMoney\Money\Money::subtract
     * @uses   \NetglueMoney\Money\Money::assertSameCurrency
     * @uses   \NetglueMoney\Money\Money::assertIsInteger
     * @uses   \NetglueMoney\Money\Money::newMoney
     * @uses   \NetglueMoney\Money\Money::handleCurrencyArgument
     * @uses   \NetglueMoney\Money\Currency
     */
    public function testPercentageCanBeExtracted()
    {
        $original = new Money(10000, new Currency('EUR'));
        $extract  = $original->extractPercentage(21);

        $this->assertEquals(new Money(8264, new Currency('EUR')), $extract['subtotal']);
        $this->assertEquals(new Money(1736, new Currency('EUR')), $extract['percentage']);
    }

    /**
     * @covers \NetglueMoney\Money\Money::allocateByRatios
     * @covers \NetglueMoney\Money\Money::newMoney
     * @covers \NetglueMoney\Money\Money::castToInt
     * @uses   \NetglueMoney\Money\Money::__construct
     * @uses   \NetglueMoney\Money\Money::handleCurrencyArgument
     * @uses   \NetglueMoney\Money\Money::getAmount
     * @uses   \NetglueMoney\Money\Money::assertInsideIntegerBounds
     * @uses   \NetglueMoney\Money\Currency
     */
    public function testCanBeAllocatedByRatios()
    {
        $a = new Money(5, new Currency('EUR'));
        $r = $a->allocateByRatios([3, 7]);

        $this->assertEquals(
            [
                new Money(2, new Currency('EUR')),
                new Money(3, new Currency('EUR'))
            ],
            $r
        );
    }

    /**
     * @covers \NetglueMoney\Money\Money::compareTo
     * @covers \NetglueMoney\Money\Money::assertSameCurrency
     * @uses   \NetglueMoney\Money\Money::__construct
     * @uses   \NetglueMoney\Money\Money::handleCurrencyArgument
     * @uses   \NetglueMoney\Money\Money::getAmount
     * @uses   \NetglueMoney\Money\Money::getCurrency
     * @uses   \NetglueMoney\Money\Currency
     */
    public function testCanBeComparedToAnotherMoneyObjectWithSameCurrency()
    {
        $a = new Money(1, new Currency('EUR'));
        $b = new Money(2, new Currency('EUR'));

        $this->assertEquals(-1, $a->compareTo($b));
        $this->assertEquals(1, $b->compareTo($a));
        $this->assertEquals(0, $a->compareTo($a));
    }

    /**
     * @covers  \NetglueMoney\Money\Money::greaterThan
     * @covers  \NetglueMoney\Money\Money::assertSameCurrency
     * @uses    \NetglueMoney\Money\Money::__construct
     * @uses    \NetglueMoney\Money\Money::handleCurrencyArgument
     * @uses    \NetglueMoney\Money\Money::compareTo
     * @uses    \NetglueMoney\Money\Money::getAmount
     * @uses    \NetglueMoney\Money\Money::getCurrency
     * @uses    \NetglueMoney\Money\Currency
     * @depends testCanBeComparedToAnotherMoneyObjectWithSameCurrency
     */
    public function testCanBeComparedToAnotherMoneyObjectWithSameCurrency2()
    {
        $a = new Money(1, new Currency('EUR'));
        $b = new Money(2, new Currency('EUR'));

        $this->assertFalse($a->greaterThan($b));
        $this->assertTrue($b->greaterThan($a));
    }

    /**
     * @covers  \NetglueMoney\Money\Money::lessThan
     * @covers  \NetglueMoney\Money\Money::assertSameCurrency
     * @uses    \NetglueMoney\Money\Money::__construct
     * @uses    \NetglueMoney\Money\Money::handleCurrencyArgument
     * @uses    \NetglueMoney\Money\Money::compareTo
     * @uses    \NetglueMoney\Money\Money::getAmount
     * @uses    \NetglueMoney\Money\Money::getCurrency
     * @uses    \NetglueMoney\Money\Currency
     * @depends testCanBeComparedToAnotherMoneyObjectWithSameCurrency
     */
    public function testCanBeComparedToAnotherMoneyObjectWithSameCurrency3()
    {
        $a = new Money(1, new Currency('EUR'));
        $b = new Money(2, new Currency('EUR'));

        $this->assertFalse($b->lessThan($a));
        $this->assertTrue($a->lessThan($b));
    }

    /**
     * @covers  \NetglueMoney\Money\Money::equals
     * @covers  \NetglueMoney\Money\Money::assertSameCurrency
     * @uses    \NetglueMoney\Money\Money::__construct
     * @uses    \NetglueMoney\Money\Money::handleCurrencyArgument
     * @uses    \NetglueMoney\Money\Money::compareTo
     * @uses    \NetglueMoney\Money\Money::getAmount
     * @uses    \NetglueMoney\Money\Money::getCurrency
     * @uses    \NetglueMoney\Money\Currency
     * @depends testCanBeComparedToAnotherMoneyObjectWithSameCurrency
     */
    public function testCanBeComparedToAnotherMoneyObjectWithSameCurrency4()
    {
        $a = new Money(1, new Currency('EUR'));
        $b = new Money(1, new Currency('EUR'));

        $this->assertEquals(0, $a->compareTo($b));
        $this->assertEquals(0, $b->compareTo($a));
        $this->assertTrue($a->equals($b));
        $this->assertTrue($b->equals($a));
    }

    /**
     * @covers  \NetglueMoney\Money\Money::greaterThanOrEqual
     * @covers  \NetglueMoney\Money\Money::assertSameCurrency
     * @uses    \NetglueMoney\Money\Money::__construct
     * @uses    \NetglueMoney\Money\Money::handleCurrencyArgument
     * @uses    \NetglueMoney\Money\Money::greaterThan
     * @uses    \NetglueMoney\Money\Money::equals
     * @uses    \NetglueMoney\Money\Money::compareTo
     * @uses    \NetglueMoney\Money\Money::getAmount
     * @uses    \NetglueMoney\Money\Money::getCurrency
     * @uses    \NetglueMoney\Money\Currency
     * @depends testCanBeComparedToAnotherMoneyObjectWithSameCurrency
     */
    public function testCanBeComparedToAnotherMoneyObjectWithSameCurrency5()
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
     * @covers  \NetglueMoney\Money\Money::lessThanOrEqual
     * @covers  \NetglueMoney\Money\Money::assertSameCurrency
     * @uses    \NetglueMoney\Money\Money::__construct
     * @uses    \NetglueMoney\Money\Money::handleCurrencyArgument
     * @uses    \NetglueMoney\Money\Money::lessThan
     * @uses    \NetglueMoney\Money\Money::equals
     * @uses    \NetglueMoney\Money\Money::compareTo
     * @uses    \NetglueMoney\Money\Money::getAmount
     * @uses    \NetglueMoney\Money\Money::getCurrency
     * @uses    \NetglueMoney\Money\Currency
     * @depends testCanBeComparedToAnotherMoneyObjectWithSameCurrency
     */
    public function testCanBeComparedToAnotherMoneyObjectWithSameCurrency6()
    {
        $a = new Money(1, new Currency('EUR'));
        $b = new Money(1, new Currency('EUR'));
        $c = new Money(2, new Currency('EUR'));

        $this->assertTrue($a->lessThanOrEqual($a));
        $this->assertTrue($a->lessThanOrEqual($b));
        $this->assertTrue($a->lessThanOrEqual($c));
        $this->assertFalse($c->lessThanOrEqual($a));
    }

    /**
     * @covers            \NetglueMoney\Money\Money::compareTo
     * @covers            \NetglueMoney\Money\Money::assertSameCurrency
     * @uses              \NetglueMoney\Money\Money::__construct
     * @uses              \NetglueMoney\Money\Money::handleCurrencyArgument
     * @uses              \NetglueMoney\Money\Money::getCurrency
     * @uses              \NetglueMoney\Money\Currency
     * @expectedException \NetglueMoney\Exception\CurrencyMismatchException
     */
    public function testExceptionIsRaisedWhenComparedToMoneyObjectWithDifferentCurrency()
    {
        $a = new Money(1, new Currency('EUR'));
        $b = new Money(2, new Currency('USD'));

        $a->compareTo($b);
    }
}
