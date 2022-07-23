<?php

namespace Smolblog\Core;

use ArgumentCountError;
use PHPUnit\Framework\TestCase;

final class EndpointParameterTest extends TestCase {
	public function testItRequiresAName() {
		$this->expectException(ArgumentCountError::class);

		$param = new EndpointParameter();
	}

	public function testItsNameCanBeSetAndAccessed() {
		$param = new EndpointParameter(name: 'id');

		$this->assertInstanceOf(EndpointParameter::class, $param);
		$this->assertEquals('id', $param->name);
	}

	public function testItsNameCannotBeModified() {
		$this->expectError();

		$param = new EndpointParameter(name: 'id');
		$param->name = 'slug';
	}

	public function testItCanBeOptional() {
		$param = new EndpointParameter(name: 'id');

		$this->assertTrue($param->validate());
		$this->assertTrue($param->validate(5));
	}

	public function testItCanBeRequired() {
		$param = new EndpointParameter(name: 'id', isRequired: true);

		$this->assertFalse($param->validate());
		$this->assertTrue($param->validate(5));
	}

	public function testItCanHaveADefault() {
		$param = new EndpointParameter(name: 'id', defaultValue: 'boom');

		$this->assertEquals('boom', $param->parse());
		$this->assertEquals('ska', $param->parse('ska'));
	}
}
