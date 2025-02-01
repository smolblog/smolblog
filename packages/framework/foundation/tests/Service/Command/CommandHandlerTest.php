<?php

namespace Smolblog\Foundation\Service\Command;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\TestDox;
use Smolblog\Test\TestCase;

#[CoversClass(CommandHandler::class)]
final class CommandHandlerTest extends TestCase {
	#[TestDox('CommandHandler can be instantiated.')]
	public function testItAll() {
		$this->assertInstanceOf(CommandHandler::class, new CommandHandler());
	}
}
