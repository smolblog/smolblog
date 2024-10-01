<?php

namespace Smolblog\Foundation\Exceptions;

use Smolblog\Foundation\Value\Messages\Command;
use Smolblog\Test\TestCase;
use Throwable;

final class CommandNotAuthorizedTest extends TestCase {
	public function testItRequiresACommand() {
		$cmd = new readonly class() extends Command {};
		$actual = new CommandNotAuthorized(originalCommand: $cmd);
		$this->assertInstanceOf(Throwable::class, $actual);
		$this->assertEquals($cmd, $actual->originalCommand);
	}
}
