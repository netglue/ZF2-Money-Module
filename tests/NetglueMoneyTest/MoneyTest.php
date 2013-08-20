<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/ZendSkeletonModule for the canonical source repository
 * @copyright Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace NetglueMoneyTest;

use NetglueMoney\Money;

use DateTime;

class MoneyTest extends Framework\TestCase {
	
	public function testConstructor() {
		
	}
	
	/**
	 * @covers Money::setAmount
	 * @covers Money::getAmount
	 */
	public function testGetSetAmount() {
		$money = new Money;
		$this->assertNull($money->getAmount());
		$this->assertSame($money, $money->setAmount(1));
		$this->assertInternalType('string', $money->getAmount());
		$this->assertSame('1', $money->getAmount());
		$money = new Money;
		$money->setAmount(1.23456789);
		$this->assertSame('1.23456789', $money->getAmount());
		
		$precision = ini_get('precision');
		$amount = '1.'.str_repeat(1, $precision - 1);
		$money->setAmount( (float) $amount);
		$this->assertSame($amount, $money->getAmount());
	}
	
	/**
	 * @covers Money::setAmount
	 * @expectedException \NetglueMoney\Exception\InvalidArgumentException
	 */
	public function testInvalidAmountThrowsException() {
		$money = new Money;
		$money->setAmount('foo');
	}
	
	/**
	 * @covers Money::setCurrencyCode
	 * @covers Money::getCurrencyCode
	 */
	public function testGetSetCurrencyCode() {
		$money = new Money;
		$this->assertNull($money->getCurrencyCode());
		
		$this->assertSame($money, $money->setCurrencyCode('GBP'));
		$this->assertSame('GBP', $money->getCurrencyCode());
	}
	
	/**
	 * @covers Money::setCurrencyCode
	 * @expectedException \NetglueMoney\Exception\InvalidArgumentException
	 */
	public function testInvalidCurrencyCodeThrowsException() {
		$money = new Money;
		$money->setCurrencyCode('123');
	}
	
	/**
	 * @covers Money::setDateTime
	 * @covers Money::getDateTime
	 */
	public function testSetGetDateTime() {
		$money = new Money;
		$this->assertNull($money->getDateTime());
		
		$date = new DateTime;
		$this->assertSame($money, $money->setDateTime($date));
		$this->assertSame($date, $money->getDateTime());
	}
	
	/**
	 * @covers Money::setTimestamp
	 * @covers Money::getTimestamp
	 */
	public function testSetGetTimestamp() {
		$money = new Money;
		$this->assertNull($money->getTimestamp());
		$this->assertNull($money->getDateTime());
		
		$time = time();
		$this->assertSame($money, $money->setTimestamp($time));
		
		$this->assertSame($time, $money->getTimestamp());
		$this->assertInstanceOf('DateTime', $money->getDateTime());
	}
	
	/**
	 * @covers Money::setTimestamp
	 * @expectedException \NetglueMoney\Exception\InvalidArgumentException
	 */
	public function testSetTimestampThrowsExceptionForInvalidTimestamp() {
		$money = new Money;
		$money->setTimestamp('foo');
	}
	
	/**
	 * @covers Money::setTimestamp
	 * @expectedException \NetglueMoney\Exception\InvalidArgumentException
	 */
	public function testSetTimestampThrowsExceptionForFalseReturnOfDateTime() {
		$money = new Money;
		$money->setTimestamp();
	}
	
}
