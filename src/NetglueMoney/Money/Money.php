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
namespace NetglueMoney\Money;

use function in_array;
use NetglueMoney\Exception;
use NetglueMoney\Exception\CurrencyMismatchException;
use NetglueMoney\Exception\InvalidArgumentException;
use function intval;
use NetglueMoney\Exception\OverflowException;
use function round;

/**
 * Value Object that represents a monetary value
 * (using a currency's smallest unit).
 *
 * @package    Money
 * @author     Sebastian Bergmann <sebastian@phpunit.de>
 * @copyright  2012-2014 Sebastian Bergmann <sebastian@phpunit.de>
 * @license    http://www.opensource.org/licenses/BSD-3-Clause  The BSD 3-Clause License
 * @link       http://www.github.com/sebastianbergmann/money
 * @see        http://martinfowler.com/bliki/ValueObject.html
 * @see        http://martinfowler.com/eaaCatalog/money.html
 */
class Money
{
    /**
     * @var integer
     */
    private $amount;

    /**
     * @var Currency
     */
    private $currency;

    /**
     * @var integer[]
     */
    private static $roundingModes = [
        PHP_ROUND_HALF_UP,
        PHP_ROUND_HALF_DOWN,
        PHP_ROUND_HALF_EVEN,
        PHP_ROUND_HALF_ODD,
    ];

    /**
     * @param int $amount
     * @param Currency|string $currency
     * @throws InvalidArgumentException
     */
    public function __construct(int $amount, $currency)
    {
        $this->amount   = $amount;
        $this->currency = self::handleCurrencyArgument($currency);
    }

    /**
     * Creates a Money object from a string such as "12.34"
     *
     * This method is designed to take into account the errors that can arise
     * from manipulating floating point numbers.
     *
     * If the number of decimals in the string is higher than the currency's
     * number of fractional digits then the value will be rounded to the
     * currency's number of fractional digits.
     *
     * @param  string $value
     * @param Currency|string $currency
     * @return Money
     * @throws InvalidArgumentException
     */
    public static function fromString(string $value, $currency) : Money
    {
        $currency = self::handleCurrencyArgument($currency);

        return new static(
            (int) round(
                $currency->getSubUnit() *
                round(
                    $value,
                    $currency->getDefaultFractionDigits(),
                    PHP_ROUND_HALF_UP
                ),
                0,
                PHP_ROUND_HALF_UP
            ),
            $currency
        );
    }

    /**
     * Returns the monetary value represented by this object.
     */
    public function getAmount() : int
    {
        return $this->amount;
    }

    /**
     * Returns the currency of the monetary value represented by this object.
     */
    public function getCurrency() : Currency
    {
        return $this->currency;
    }

    /**
     * Returns the currency code of the monetary value represented by this
     * object.
     *
     * @return string
     */
    public function getCurrencyCode() : string
    {
        return $this->currency->getCurrencyCode();
    }

    /**
     * Returns a new Money object that represents the monetary value
     * of the sum of this Money object and another.
     *
     * @param Money $other
     * @return Money
     * @throws CurrencyMismatchException
     * @throws OverflowException
     */
    public function add(Money $other) : Money
    {
        $this->assertSameCurrency($this, $other);

        $value = $this->amount + $other->getAmount();

        $this->assertIsInteger($value);

        return $this->newMoney($value);
    }

    /**
     * Returns a new Money object that represents the monetary value
     * of the difference of this Money object and another.
     *
     * @param Money $other
     * @return Money
     * @throws CurrencyMismatchException
     * @throws OverflowException
     */
    public function subtract(Money $other) : Money
    {
        $this->assertSameCurrency($this, $other);

        $value = $this->amount - $other->getAmount();

        $this->assertIsInteger($value);

        return $this->newMoney($value);
    }

    /**
     * Returns a new Money object that represents the negated monetary value
     * of this Money object.
     *
     * @return Money
     */
    public function negate() : Money
    {
        return $this->newMoney(-1 * $this->amount);
    }

    /**
     * Returns a new Money object that represents the monetary value
     * of this Money object multiplied by a given factor.
     *
     * @param  float                                            $factor
     * @param  integer                                          $roundingMode
     * @return Money
     * @throws InvalidArgumentException
     */
    public function multiply($factor, int $roundingMode = PHP_ROUND_HALF_UP) : Money
    {
        if (! in_array($roundingMode, self::$roundingModes, true)) {
            throw new InvalidArgumentException(
                '$roundingMode must be a valid rounding mode (PHP_ROUND_*)'
            );
        }

        return $this->newMoney(
            $this->castToInt(
                round($factor * $this->amount, 0, $roundingMode)
            )
        );
    }

    /**
     * Allocate the monetary value represented by this Money object
     * among N targets.
     *
     * @param  integer                     $n
     * @return Money[]
     * @throws InvalidArgumentException
     */
    public function allocateToTargets(int $n) : array
    {

        $low       = $this->newMoney((int)($this->amount / $n));
        $high      = $this->newMoney($low->getAmount() + 1);
        $remainder = $this->amount % $n;
        $result    = [];

        for ($i = 0; $i < $remainder; $i++) {
            $result[] = $high;
        }

        for ($i = $remainder; $i < $n; $i++) {
            $result[] = $low;
        }

        return $result;
    }

    /**
     * Allocate the monetary value represented by this Money object
     * using a list of ratios.
     *
     * @param  array                       $ratios
     * @return Money[]
     */
    public function allocateByRatios(array $ratios) : array
    {
        /** @var Money[] $result */
        $result    = [];
        $total     = array_sum($ratios);
        $remainder = $this->amount;

        for ($i = 0; $i < count($ratios); $i++) {
            $amount     = $this->castToInt($this->amount * $ratios[$i] / $total);
            $result[]   = $this->newMoney($amount);
            $remainder -= $amount;
        }

        for ($i = 0; $i < $remainder; $i++) {
            $result[$i] = $this->newMoney($result[$i]->getAmount() + 1);
        }

        return $result;
    }

    /**
     * Extracts a percentage of the monetary value represented by this Money
     * object and returns an array of two Money objects:
     * $original = $result['subtotal'] + $result['percentage'];
     *
     * Please note that this extracts the percentage out of a monetary value
     * where the percentage is already included. If you want to get the
     * percentage of the monetary value you should use multiplication
     * (multiply(0.21), for instance, to calculate 21% of a monetary value
     * represented by a Money object) instead.
     *
     * @param  float $percentage
     * @param  integer $roundingMode
     * @return Money[]
     * @see    https://github.com/sebastianbergmann/money/issues/27
     */
    public function extractPercentage(float $percentage, ?int $roundingMode = PHP_ROUND_HALF_UP) : array
    {
        $percentage = $this->newMoney(
            $this->castToInt(
                round($this->amount / (100 + $percentage) * $percentage, 0, $roundingMode)
            )
        );

        return [
            'percentage' => $percentage,
            'subtotal'   => $this->subtract($percentage)
        ];
    }

    /**
     * Compares this Money object to another.
     *
     * Returns an integer less than, equal to, or greater than zero
     * if the value of this Money object is considered to be respectively
     * less than, equal to, or greater than the other Money object.
     *
     * @param Money $other
     * @return integer                                           -1|0|1
     * @throws CurrencyMismatchException
     */
    public function compareTo(Money $other) : int
    {
        $this->assertSameCurrency($this, $other);

        if ($this->amount === $other->getAmount()) {
            return 0;
        }

        return $this->amount < $other->getAmount() ? -1 : 1;
    }

    /**
     * Returns TRUE if this Money object equals to another.
     *
     * @param Money $other
     * @return boolean
     * @throws CurrencyMismatchException
     */
    public function equals(Money $other) : bool
    {
        return $this->compareTo($other) == 0;
    }

    /**
     * Returns TRUE if the monetary value represented by this Money object
     * is greater than that of another, FALSE otherwise.
     *
     * @param Money $other
     * @return boolean
     * @throws CurrencyMismatchException
     */
    public function greaterThan(Money $other) : bool
    {
        return $this->compareTo($other) == 1;
    }

    /**
     * Returns TRUE if the monetary value represented by this Money object
     * is greater than or equal that of another, FALSE otherwise.
     *
     * @param Money $other
     * @return boolean
     * @throws CurrencyMismatchException
     */
    public function greaterThanOrEqual(Money $other) : bool
    {
        return $this->greaterThan($other) || $this->equals($other);
    }

    /**
     * Returns TRUE if the monetary value represented by this Money object
     * is smaller than that of another, FALSE otherwise.
     *
     * @param Money $other
     * @return boolean
     * @throws CurrencyMismatchException
     */
    public function lessThan(Money $other) : bool
    {
        return $this->compareTo($other) == -1;
    }

    /**
     * Returns TRUE if the monetary value represented by this Money object
     * is smaller than or equal that of another, FALSE otherwise.
     *
     * @param Money $other
     * @return boolean
     * @throws CurrencyMismatchException
     */
    public function lessThanOrEqual(Money $other) : bool
    {
        return $this->lessThan($other) || $this->equals($other);
    }

    /**
     * @param Money $a
     * @param Money $b
     * @throws CurrencyMismatchException
     */
    private function assertSameCurrency(Money $a, Money $b) : void
    {
        if ($a->getCurrency() != $b->getCurrency()) {
            throw new CurrencyMismatchException;
        }
    }

    /**
     * Raises an exception if the amount is not an integer
     *
     * @param  number $amount
     * @return void
     * @throws OverflowException
     */
    private function assertIsInteger($amount) : void
    {
        if (! is_int($amount)) {
            throw new OverflowException;
        }
    } // @codeCoverageIgnore

    /**
     * Raises an exception if the amount is outside of the integer bounds
     *
     * @param  number $amount
     * @return void
     * @throws OverflowException
     */
    private function assertInsideIntegerBounds($amount) : void
    {
        if (abs($amount) > PHP_INT_MAX) {
            throw new OverflowException;
        }
    } // @codeCoverageIgnore

    /**
     * Cast an amount to an integer but ensure that the operation won't hide overflow
     *
     * @param number $amount
     * @return int
     * @throws OverflowException
     */
    private function castToInt($amount) : int
    {
        $this->assertInsideIntegerBounds($amount);

        return intval($amount);
    }

    /**
     * @param  integer                   $amount
     * @return Money
     */
    private function newMoney(int $amount) : Money
    {
        return new Money($amount, $this->currency);
    }

    /**
     * @param Currency|string $currency
     * @return Currency
     * @throws InvalidArgumentException
     */
    private static function handleCurrencyArgument($currency) : Currency
    {
        if (! $currency instanceof Currency && ! is_string($currency)) {
            throw new InvalidArgumentException('$currency must be an object of type Currency or a string');
        }

        if (is_string($currency)) {
            $currency = new Currency($currency);
        }

        return $currency;
    }
}
