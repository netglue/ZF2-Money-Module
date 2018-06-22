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

use NetglueMoney\Money\Currency;
use NetglueMoneyTest\Framework\TestCase;

class CurrencyTest extends TestCase
{
    /**
     * @covers            \NetglueMoney\Money\Currency::__construct
     * @expectedException \NetglueMoney\Exception\InvalidArgumentException
     */
    public function testExceptionIsRaisedForInvalidConstructorArgument()
    {
        new Currency(null);
    }

    /**
     * @covers \NetglueMoney\Money\Currency::__construct
     */
    public function testObjectCanBeConstructedForValidConstructorArgument()
    {
        $c = new Currency('EUR');

        $this->assertInstanceOf('NetglueMoney\\Money\\Currency', $c);

        return $c;
    }

    /**
     * @covers \NetglueMoney\Money\Currency::addCurrency
     * @uses   \NetglueMoney\Money\Currency::__construct
     */
    public function testCustomCurrencyCanBeRegistered()
    {
        Currency::addCurrency(
            'BTC',
            'Bitcoin',
            999,
            4,
            1000
        );

        $this->assertInstanceOf(
            'NetglueMoney\Money\Currency',
            new Currency('BTC')
        );
    }

    /**
     * @covers  \NetglueMoney\Money\Currency::__toString
     * @depends testObjectCanBeConstructedForValidConstructorArgument
     */
    public function testCanBeCastToString(Currency $c)
    {
        $this->assertEquals('EUR', (string) $c);
    }

    /**
     * @covers  \NetglueMoney\Money\Currency::getCurrencyCode
     * @depends testObjectCanBeConstructedForValidConstructorArgument
     */
    public function testCurrencyCodeCanBeRetrieved(Currency $c)
    {
        $this->assertEquals('EUR', $c->getCurrencyCode());
    }

    /**
     * @covers  \NetglueMoney\Money\Currency::getDefaultFractionDigits
     * @depends testObjectCanBeConstructedForValidConstructorArgument
     */
    public function testDefaultFractionDigitsCanBeRetrieved(Currency $c)
    {
        $this->assertEquals(2, $c->getDefaultFractionDigits());
    }

    /**
     * @covers  \NetglueMoney\Money\Currency::getDisplayName
     * @depends testObjectCanBeConstructedForValidConstructorArgument
     */
    public function testDisplayNameCanBeRetrieved(Currency $c)
    {
        $this->assertEquals('Euro', $c->getDisplayName());
    }

    /**
     * @covers  \NetglueMoney\Money\Currency::getNumericCode
     * @depends testObjectCanBeConstructedForValidConstructorArgument
     */
    public function testNumericCodeCanBeRetrieved(Currency $c)
    {
        $this->assertEquals(978, $c->getNumericCode());
    }

    /**
     * @covers  \NetglueMoney\Money\Currency::getSubUnit
     * @depends testObjectCanBeConstructedForValidConstructorArgument
     */
    public function testSubUnitCanBeRetrieved(Currency $c)
    {
        $this->assertEquals(100, $c->getSubUnit());
    }

    /**
     * covers \NetglueMoney\Money\Currency::getAvailableCurrencies
     */
    public function testGetAvailableCurencies()
    {
        $array = Currency::getAvailableCurrencies();
        $this->assertInternalType('array', $array);
        $this->assertContainsOnly('string', $array);
        foreach ($array as $code) {
            $this->assertRegExp('/^[A-Z]{3}$/', $code);
        }
    }

    /**
     * covers \NetglueMoney\Money\Currency::getAvailableCurrencyNames
     */
    public function testGetAvailableCurencyNames()
    {
        $array = Currency::getAvailableCurrencyNames();
        $this->assertInternalType('array', $array);
        $this->assertContainsOnly('string', $array);
        foreach ($array as $code => $name) {
            $this->assertRegExp('/^[A-Z]{3}$/', $code);
        }
    }
}
