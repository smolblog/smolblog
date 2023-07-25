<?php

namespace Smolblog\Framework\Exceptions;

use Smolblog\Test\TestCase;
use Smolblog\Framework\Messages\Command;
use Smolblog\Framework\Objects\Identifier;

final class TestInvalidCommand extends Command {}

final class InvalidCommandParametersExceptionTest extends TestCase {
	public function testItHasADefaultMessage() {
		$ex = new InvalidCommandParametersException(new TestInvalidCommand());
		$expected = 'Invalid parameters given to command ' . TestInvalidCommand::class;

		$this->assertEquals($expected, $ex->getMessage());
	}

	public function testTheDefaultMessageCanBeOverridden() {
		$message = $this->randomId()->toString();
		$ex = new InvalidCommandParametersException(new TestInvalidCommand(), message: $message);

		$this->assertEquals($message, $ex->getMessage());
	}
}
