<?php

namespace Smolblog\Foundation\Value\Messages;
use PHPUnit\Framework\Attributes\CoversClass;
use Smolblog\Test\TestCase;

readonly class ExampleCommand extends Command {
	public function __construct(public string $name) { parent::__construct(); }
}

#[CoversClass(Command::class)]
final class CommandTest extends TestCase {
	public function testItCanBeInstantiated() {
		$this->assertInstanceOf(Command::class, new ExampleCommand('test'));
	}

	public function testItCanHaveMessageMetadata() {
		$command = new ExampleCommand('test');
		$command->setMetaValue('one', 'two');

		$this->assertEquals('two', $command->getMetaValue('one'));
	}

	public function testItCanHaveAReturnValue() {
		$command = new ExampleCommand('test');
		$command->setReturnValue('retval');

		$this->assertEquals('retval', $command->returnValue());
	}
}
