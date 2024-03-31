<?php

namespace Smolblog\Foundation\Exceptions;
use Exception;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\TestDox;
use Smolblog\Test\TestCase;

#[CoversClass(CodePathNotSupported::class)]
final class CodePathNotSupportedTest extends TestCase {
	#[TestDox('It can be created with a message.')]
	public function testWithMessage() {
		$exception = new CodePathNotSupported('message');
		$this->assertEquals('message', $exception->getMessage());
	}

	#[TestDox('It can be created with a code.')]
	public function testWithCode() {
		$this->assertInstanceOf(CodePathNotSupported::class, new CodePathNotSupported(code: 123));
	}

	#[TestDox('It can be created with a previous exception.')]
	public function testWithPrevious() {
		$previous = new Exception('previous');
		$exception = new CodePathNotSupported(previous: $previous);
		$this->assertEquals($previous, $exception->getPrevious());
	}

	#[TestDox('It can be created with a location and message.')]
	public function testWithLocationAndMessage() {
		$exception = new CodePathNotSupported('message', location: 'location');
		$this->assertEquals('In location: message', $exception->getMessage());
	}

	#[TestDox('It can be created with a location and no message.')]
	public function testWithLocation() {
		$exception = new CodePathNotSupported(location: 'location');
		$this->assertEquals('In location: The code path is not supported.', $exception->getMessage());
	}

	#[TestDox('It can be created without a message.')]
	public function testBlank() {
		$exception = new CodePathNotSupported();
		$this->assertEquals('The code path is not supported.', $exception->getMessage());
	}
}
