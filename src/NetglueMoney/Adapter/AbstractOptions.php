<?php

namespace NetglueMoney\Adapter;

use Zend\StdLib\AbstractOptions as StdOptions;

abstract class AbstractOptions extends StdOptions implements AdapterOptionsInterface {
	
	protected $scale = 10;
	
	public function setScale($scale) {
		$this->scale = (int) $scale;
		return $this;
	}
	
	public function getScale() {
		return $this->scale;
	}
	
}