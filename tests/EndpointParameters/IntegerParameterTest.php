<?php

namespace Smolblog\Core\EndpointParameters;

use PHPUnit\Framework\TestCase;

final class IntegerParameterTest extends TestCase {
	public function testItValidatesAnInteger() {
		$param = new IntegerParameter(name: 'id');

		$this->assertTrue($param->validate(5));
	}

	public function testItValidatesANumericString() {
		$param = new IntegerParameter(name: 'id');

		$this->assertTrue($param->validate('5'));
	}

	public function testItDoesNotValidateAnAlphanumericString() {
		$param = new IntegerParameter(name: 'id');

		$this->assertFalse($param->validate('Twenty'));
	}

	public function testItParsesANumericString() {
		$param = new IntegerParameter(name: 'id');

		$this->assertEquals(5, $param->parse('5'));
	}

	public function testItParsesAnInt() {
		$param = new IntegerParameter(name: 'id');

		$this->assertEquals(5, $param->parse(5));
	}

	public function testItParsesADecimalIntoAnInteger() {
		$param = new IntegerParameter(name: 'id');

		$this->assertEquals(intval(3.14), $param->parse('3.14'));
		$this->assertEquals(intval(3.14), $param->parse(3.14));
	}
}
