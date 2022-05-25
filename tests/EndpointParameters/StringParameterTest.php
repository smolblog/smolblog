<?php

namespace Smolblog\Core\EndpointParameters;

use stdClass;
use PHPUnit\Framework\TestCase;

final class StringParameterTest extends TestCase {
	public function testItValidatesAnythingStringable() {
		$param = new StringParameter(name: 'id');

		$this->assertTrue($param->validate(5));
		$this->assertTrue($param->validate('5'));
		$this->assertTrue($param->validate('Twenty'));
	}

	public function testItDoesNotValidateAnArray() {
		$param = new StringParameter(name: 'id');

		$this->assertFalse($param->validate(['smol' => 'blog']));
	}

	public function testItDoesNotValidateAStandardObject() {
		$param = new StringParameter(name: 'id');
		$obj = new stdClass();
		$obj->name = 'bob';

		$this->assertFalse($param->validate($obj));
	}

	public function testItParsesANumericString() {
		$param = new StringParameter(name: 'id');

		$this->assertEquals('5', $param->parse('5'));
	}

	public function testItParsesAnInt() {
		$param = new StringParameter(name: 'id');

		$this->assertEquals('5', $param->parse(5));
	}

	public function testItParsesADecimalIntoAString() {
		$param = new StringParameter(name: 'id');

		$this->assertEquals(strval(3.14), $param->parse('3.14'));
		$this->assertEquals(strval(3.14), $param->parse(3.14));
	}
}
