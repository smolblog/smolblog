<?php

namespace Smolblog\Core\EndpointParameters;

use ArgumentCountError;
use PHPUnit\Framework\TestCase;

final class BasicParameterTest extends TestCase {
	public function testItRequiresAName() {
		$this->expectException(ArgumentCountError::class);

		$param = new BasicParameter();
	}

	public function testItsNameCanBeSetAndAccessed() {
		$param = new BasicParameter(name: 'id');

		$this->assertInstanceOf(BasicParameter::class, $param);
		$this->assertEquals('id', $param->slug());
	}

	public function testItCanBeOptional() {
		$param = new BasicParameter(name: 'id');

		$this->assertTrue($param->validate());
		$this->assertTrue($param->validate(5));
	}

	public function testItCanBeRequired() {
		$param = new BasicParameter(name: 'id', isRequired: true);

		$this->assertFalse($param->validate());
		$this->assertTrue($param->validate(5));
	}

	public function testItCanHaveADefault() {
		$param = new BasicParameter(name: 'id', defaultValue: 'boom');

		$this->assertEquals('boom', $param->parse());
		$this->assertEquals('ska', $param->parse('ska'));
	}
}
