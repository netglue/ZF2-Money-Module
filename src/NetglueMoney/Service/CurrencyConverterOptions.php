<?php

namespace NetglueMoney\Service;

use Zend\StdLib\AbstractOptions;

use NetglueMoney\Exception;


class CurrencyConverterOptions extends AbstractOptions {
	
	protected $round = true;
	
	protected $roundMode = PHP_ROUND_HALF_UP;
	
	protected $precision = 2;
	
	protected $bcScale = 8;
	
	public function setBcScale($scale) {
		$this->bcScale = (int) $scale;
		return $this;
	}
	
	public function getBcScale() {
		return $this->bcScale;
	}
	
	public function setRound($flag) {
		$this->round = (bool) $flag;
		return $this;
	}
	
	public function getRound() {
		return $this->round;
	}
	
	public function setRoundMode($mode) {
		$mode = (int) $mode;
		$possible = array(
			PHP_ROUND_HALF_UP,
			PHP_ROUND_HALF_DOWN,
			PHP_ROUND_HALF_EVEN,
			PHP_ROUND_HALF_ODD,
		);
		if(!in_array($mode, $possible)) {
			throw new Exception\InvalidArgumentException("Round mode should be one of the PHP_ROUND_* constants");
		}
		$this->roundMode = $mode;
		return $this;
	}
	
	public function getRoundMode() {
		return $this->roundMode;
	}
	
	public function setPrecision($precision) {
		$this->precision = (int) $precision;
		return $this;
	}
	
	public function getPrecision() {
		return $this->precision;
	}
	
}